<?php

namespace Modules\Setting\Services;

use Exception;
use Modules\Setting\Entities\Coupon;
use Modules\Setting\Entities\PackageOrder;

class CouponService
{
    public function all(array $filters = [])
    {
        $query = Coupon::query()->latest();

        if (!empty($filters['search'])) {
            $query->where('code', 'LIKE', '%' . $filters['search'] . '%');
        }

        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null && $filters['is_active'] !== '') {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function find(int $id)
    {
        return Coupon::find($id);
    }

    public function create(array $data): Coupon
    {
        $data['code'] = $this->normalizeCode($data['code']);
        $data['used_count'] = 0;

        return Coupon::create($data);
    }

    public function update(int $id, array $data)
    {
        $coupon = Coupon::find($id);
        if (!$coupon instanceof Coupon) {
            return false;
        }

        if (isset($data['code'])) {
            $data['code'] = $this->normalizeCode($data['code']);
        }

        $coupon->update($data);

        return $coupon;
    }

    public function delete(int $id): bool
    {
        $coupon = Coupon::find($id);
        if (!$coupon instanceof Coupon) {
            return false;
        }

        return (bool) $coupon->delete();
    }

    /**
     * @param  float  $cartTotal  Full cart total (order subtotal before discount).
     * @param  float  $mainPackageSubtotal  Portion of the cart total that belongs to main-package line(s); discount is computed only on this amount.
     */
    public function preview(string $code, int $userId, float $cartTotal, float $mainPackageSubtotal): array
    {
        $coupon = Coupon::where('code', $this->normalizeCode($code))->first();
        $this->assertCanUseCoupon($coupon, $userId);
        if (!$coupon instanceof Coupon) {
            throw new Exception('Coupon does not exist.');
        }

        $discountAmount = $this->calculateDiscount($coupon, max($mainPackageSubtotal, 0));

        return [
            'coupon' => $coupon,
            'original_amount' => round($cartTotal, 2),
            'discount_amount' => $discountAmount,
            'payable_amount' => max(round($cartTotal - $discountAmount, 2), 0),
        ];
    }

    /**
     * @param  float  $mainPackageSubtotal  Discount base (main package lines only).
     */
    public function reserveForCheckout(string $code, int $userId, float $mainPackageSubtotal): array
    {
        $coupon = Coupon::where('code', $this->normalizeCode($code))->first();
        $this->assertCanUseCoupon($coupon, $userId);
        if (!$coupon instanceof Coupon) {
            throw new Exception('Coupon does not exist.');
        }

        return [
            'coupon' => $coupon,
            'discount_amount' => $this->calculateDiscount($coupon, max($mainPackageSubtotal, 0)),
        ];
    }

    /**
     * Stripe Checkout session discount: prefer percent coupon scoped to main Stripe product(s)
     * so add-ons are not discounted; otherwise use a fixed amount_off matching computed discount.
     *
     * When checkout line items use price_data.product_data (listing presentation), pass
     * $restrictToMainProducts = false — inline products do not match stored stripe_product_id values.
     */
    public function createStripeCheckoutCoupon(
        Coupon $coupon,
        float $computedDiscountUsd,
        array $stripeProductIds,
        bool $restrictToMainProducts = true
    ): string {
        $ids = $restrictToMainProducts
            ? array_values(array_filter(array_unique(array_map('strval', $stripeProductIds))))
            : [];

        if ($ids !== []) {
            $params = [
                'duration' => 'once',
                'name' => substr((string) $coupon->code, 0, 40),
                'applies_to' => ['products' => $ids],
            ];

            if ($coupon->type === Coupon::TYPE_FREE) {
                $params['percent_off'] = 100;
            } else {
                $params['percent_off'] = min(100, max(0, (float) $coupon->discount_value));
            }

            return \Stripe\Coupon::create($params)->id;
        }

        $cents = (int) round(max($computedDiscountUsd, 0) * 100);
        if ($cents < 1) {
            throw new Exception('Computed discount is zero.');
        }

        return \Stripe\Coupon::create([
            'amount_off' => $cents,
            'currency' => strtolower((string) config('services.stripe.currency', 'usd')),
            'duration' => 'once',
            'name' => substr((string) $coupon->code, 0, 40),
        ])->id;
    }

    private function assertCanUseCoupon(?Coupon $coupon, int $userId): void
    {
        if (!$coupon) {
            throw new Exception('Coupon does not exist.');
        }

        if (!$coupon->is_active) {
            throw new Exception('Coupon is not active.');
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw new Exception('Coupon has expired.');
        }

        $totalUsageCount = PackageOrder::where('coupon_id', $coupon->id)
            ->where('status', 1)
            ->count();

        if ($coupon->max_usage !== null && $totalUsageCount >= $coupon->max_usage) {
            throw new Exception('Coupon usage limit has been reached.');
        }

        $hasUsedCoupon = PackageOrder::where('coupon_id', $coupon->id)
            ->where('user_id', $userId)
            ->where('status', 1)
            ->exists();

        if ($hasUsedCoupon) {
            throw new Exception('You have already used this coupon.');
        }
    }

    private function calculateDiscount(Coupon $coupon, float $totalAmount): float
    {
        if ($coupon->type === Coupon::TYPE_FREE) {
            return round($totalAmount, 2);
        }

        $discount = $totalAmount * ((float) $coupon->discount_value / 100);

        return min(round($discount, 2), round($totalAmount, 2));
    }

    private function normalizeCode(string $code): string
    {
        return strtoupper(trim($code));
    }
}
