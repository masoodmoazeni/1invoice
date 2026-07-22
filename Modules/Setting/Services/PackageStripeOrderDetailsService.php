<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Modules\ListBusiness\Entities\ListBusiness;
use Modules\ListBusiness\Support\ListImageUrlResolver;
use Modules\Setting\Entities\Package;

class PackageStripeOrderDetailsService
{
    /**
     * Build Stripe line items, order snapshot, totals, and main-package flags from requested package rows.
     *
     * @param  array<int, array{package_id: int|string, qty: int|string}>  $requestedPackages
     * @return array{
     *     line_items: array<int, array{price: mixed, quantity: int}>,
     *     order_items: array<int, array<string, mixed>>,
     *     total_amount: float,
     *     is_subscription: bool,
     *     has_main_package: bool,
     *     has_main_diy_package: bool,
     *     has_main_broker_package: bool,
     *     main_package_line_subtotal: float,
     *     main_package_stripe_product_ids: list<string>
     * }
     */
    public function buildFromRequestedPackages(array $requestedPackages): array
    {
        $lineItems = [];
        $orderItems = [];
        $totalAmount = 0;
        $isSubscription = false;
        $hasMainDiyPackage = false;
        $hasMainBrokerPackage = false;
        $mainPackageLineSubtotal = 0.0;
        $mainStripeProductIds = [];

        foreach ($requestedPackages as $p) {
            $package = Package::find($p['package_id']);
            $qty = (int) $p['qty'];
            $unitPrice = (float) $package->price;
            $totalPrice = $unitPrice * $qty;
            $typeStripe = $package->type_stripe ?? $package->type ?? 'one-time';

            if ((int) $package->main_package === 1) {
                if ($package->type_package === 'diy') {
                    $hasMainDiyPackage = true;
                } elseif ($package->type_package === 'broker') {
                    $hasMainBrokerPackage = true;
                }
                $mainPackageLineSubtotal += $totalPrice;
                $pid = $package->stripe_product_id ?? null;
                if (is_string($pid) && $pid !== '') {
                    $mainStripeProductIds[$pid] = true;
                }
            }

            $orderItems[] = [
                'package_id' => $package->id,
                'name' => $package->title ?? $package->name ?? null,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'total' => $totalPrice,
                'main_package' => (int) ($package->main_package ?? 0) === 1 ? 1 : 0,
            ];

            if ($typeStripe === 'subscription') {
                $isSubscription = true;
                $priceId = $package->stripe_recurring_price_id ?? $package->stripe_price_id;
            } else {
                $priceId = $package->stripe_price_id;
            }

            $lineItems[] = [
                'price' => $priceId,
                'quantity' => $qty,
            ];

            $totalAmount += $totalPrice;
        }

        return [
            'line_items' => $lineItems,
            'order_items' => $orderItems,
            'total_amount' => round($totalAmount, 2),
            'is_subscription' => $isSubscription,
            'has_main_package' => $hasMainDiyPackage || $hasMainBrokerPackage,
            'has_main_diy_package' => $hasMainDiyPackage,
            'has_main_broker_package' => $hasMainBrokerPackage,
            'main_package_line_subtotal' => round($mainPackageLineSubtotal, 2),
            'main_package_stripe_product_ids' => array_keys($mainStripeProductIds),
        ];
    }

    public function cartAllowsCouponForListingType(int $listType, array $orderDetails): bool
    {
        if ($listType === 2) {
            return false;
        }

        return match ($listType) {
            0 => (bool) ($orderDetails['has_main_diy_package'] ?? false),
            1 => (bool) ($orderDetails['has_main_broker_package'] ?? false),
            default => false,
        };
    }

    public function couponRejectionMessage(int $listType, array $orderDetails): ?string
    {
        if ($this->cartAllowsCouponForListingType($listType, $orderDetails)) {
            return null;
        }

        if ($listType === 2) {
            return 'Coupons are not available for Full Broker listings.';
        }

        return match ($listType) {
            0 => 'Coupons apply only when the DIY main package is included at checkout.',
            1 => 'Coupons apply only when the Broker Lite main package is included at checkout.',
            default => 'Coupons cannot be applied to this checkout.',
        };
    }

    /**
     * Stripe Checkout session fields so the payer sees which listing they are paying for.
     *
     * The large image on the left uses the first image on each line item’s inline product
     * (see buildStripeCheckoutLineItemsWithListingPresentation); branding_settings only sets display_name
     * so we do not duplicate a tiny header logo.
     *
     * @return array{
     *     metadata: array<string, string>,
     *     custom_text: array{submit: array{message: string}},
     *     payment_description: string,
     *     branding_settings: array{display_name: string}
     * }
     */
    public function stripeCheckoutListingContext(ListBusiness $list): array
    {
        $listingRef = $this->listingCheckoutPublicRef($list);
        $company = trim((string) ($list->company_name ?? ''));
        $title = trim((string) ($list->title ?? ''));

        $message = $this->listingCheckoutSubmitMessage($list);
        $message = Str::limit($message, 1000, '');

        $oneLine = trim($listingRef . ' — ' . ($company !== '' ? $company . ' — ' : '') . $title);
        if ($oneLine === '' || $oneLine === '—') {
            $oneLine = 'Listing #' . (int) $list->id;
        }
        $paymentDescription = Str::limit($oneLine, 500, '');

        $metadata = array_filter([
            'list_id' => (string) $list->id,
            'listing_id' => Str::limit($listingRef, 500, ''),
            'listing_company' => $company !== '' ? Str::limit($company, 500, '') : null,
            'listing_title' => $title !== '' ? Str::limit($title, 500, '') : null,
        ], static fn ($v) => $v !== null && $v !== '');

        $displayName = $title !== '' ? $title : ($company !== '' ? $company : 'Listing ' . $listingRef);
        $displayName = Str::limit($displayName, 100, '');

        return [
            'metadata' => $metadata,
            'custom_text' => [
                'submit' => [
                    'message' => $message,
                ],
            ],
            'payment_description' => $paymentDescription,
            'branding_settings' => [
                'display_name' => $displayName,
            ],
        ];
    }

    /**
     * Rebuild Checkout line items with price_data so the listing cover is the first product image
     * (large hero on the left). Listing details are prepended to each product description.
     *
     * @param  array<int, array{price: string, quantity: int}>  $lineItems
     * @return array<int, array{price_data: array<string, mixed>, quantity: int}|array{price: string, quantity: int}>
     */
    public function buildStripeCheckoutLineItemsWithListingPresentation(array $lineItems, ListBusiness $list): array
    {
        $listingImageUrl = $this->stripeCheckoutListingBrandingLogoUrl($list);
        $listingBlock = $this->listingCheckoutProductDescriptionBlock($list);

        $out = [];
        foreach (array_values($lineItems) as $idx => $row) {
            if (empty($row['price']) || ! is_string($row['price'])) {
                $out[] = $row;

                continue;
            }

            try {
                $price = \Stripe\Price::retrieve($row['price'], ['expand' => ['product']]);
            } catch (\Throwable) {
                $out[] = $row;

                continue;
            }

            if (($price->billing_scheme ?? 'per_unit') !== 'per_unit') {
                $out[] = $row;

                continue;
            }

            if ($price->recurring && (($price->recurring->usage_type ?? 'licensed') === 'metered')) {
                $out[] = $row;

                continue;
            }

            if ($price->custom_unit_amount ?? null) {
                $out[] = $row;

                continue;
            }

            $product = $price->product;
            if (is_string($product)) {
                $product = \Stripe\Product::retrieve($product);
            }

            $baseName = (string) ($product->name ?? 'Item');
            $baseDesc = trim((string) ($product->description ?? ''));
            $prodImages = is_array($product->images) ? $product->images : (array) ($product->images ?? []);

            $images = [];
            if ($idx === 0 && $listingImageUrl !== null) {
                $images[] = $listingImageUrl;
            }
            foreach ($prodImages as $img) {
                if (is_string($img) && $img !== '' && count($images) < 8) {
                    $images[] = $img;
                }
            }

            $prefix = $idx === 0 ? $listingBlock : '';
            $fullDesc = trim($prefix . ($baseDesc !== '' ? ($prefix !== '' ? "\n\n" : '') . $baseDesc : ''));
            $fullDesc = Str::limit($fullDesc, 4500, '');

            $priceData = [
                'currency' => $price->currency,
                'product_data' => [
                    'name' => Str::limit($baseName, 250, ''),
                    'description' => $fullDesc,
                    'images' => array_values(array_slice($images, 0, 8)),
                    'metadata' => [
                        'list_id' => (string) $list->id,
                    ],
                ],
            ];

            if ($price->unit_amount !== null) {
                $priceData['unit_amount'] = $price->unit_amount;
            } elseif ($price->unit_amount_decimal !== null) {
                $priceData['unit_amount_decimal'] = (string) $price->unit_amount_decimal;
            } else {
                $out[] = $row;

                continue;
            }

            if ($price->tax_behavior !== null) {
                $priceData['tax_behavior'] = $price->tax_behavior;
            }

            if ($price->type === 'recurring' && $price->recurring) {
                $priceData['recurring'] = [
                    'interval' => $price->recurring->interval,
                    'interval_count' => (int) ($price->recurring->interval_count ?? 1),
                ];
            }

            $out[] = [
                'price_data' => $priceData,
                'quantity' => max(1, (int) ($row['quantity'] ?? 1)),
            ];
        }

        return $out;
    }

    /**
     * @return array{description: string, metadata: array<string, string>}
     */
    public function stripeSubscriptionContextFromListing(ListBusiness $list): array
    {
        $ref = $this->listingCheckoutPublicRef($list);
        $company = trim((string) ($list->company_name ?? ''));
        $title = trim((string) ($list->title ?? ''));

        $parts = ['Listing ID ' . $ref];
        if ($company !== '') {
            $parts[] = $company;
        }
        if ($title !== '') {
            $parts[] = $title;
        }
        $description = Str::limit(implode(' · ', $parts), 500, '');

        $metadata = array_filter([
            'list_id' => (string) $list->id,
            'listing_id' => Str::limit($ref, 500, ''),
            'listing_company' => $company !== '' ? Str::limit($company, 500, '') : null,
            'listing_title' => $title !== '' ? Str::limit($title, 500, '') : null,
        ], static fn ($v) => $v !== null && $v !== '');

        return [
            'description' => $description,
            'metadata' => $metadata,
        ];
    }

    private function listingCheckoutPublicRef(ListBusiness $list): string
    {
        $publicListingId = trim((string) ($list->listing_id ?? ''));

        return $publicListingId !== '' ? $publicListingId : (string) (int) $list->id;
    }

    private function listingCheckoutProductDescriptionBlock(ListBusiness $list): string
    {
        $ref = $this->listingCheckoutPublicRef($list);
        $company = trim((string) ($list->company_name ?? ''));
        $title = trim((string) ($list->title ?? ''));

        $lines = [
            'LISTING',
            'Listing ID: ' . $ref,
            'Company: ' . ($company !== '' ? $company : '—'),
            'Title: ' . ($title !== '' ? $title : '—'),
            '',
        ];

        return implode("\n", $lines);
    }

    private function listingCheckoutSubmitMessage(ListBusiness $list): string
    {
        $ref = $this->listingCheckoutPublicRef($list);
        $company = trim((string) ($list->company_name ?? ''));
        $title = trim((string) ($list->title ?? ''));

        return implode("\n\n", [
            'You are paying for this listing',
            "Listing ID\n" . $ref,
            "Company\n" . ($company !== '' ? $company : '—'),
            "Title\n" . ($title !== '' ? $title : '—'),
        ]);
    }

    /**
     * Stripe-acceptable logo URL: direct HTTPS link with allowed extension, or signed JPEG proxy for local WebP/other files.
     */
    private function stripeCheckoutListingBrandingLogoUrl(ListBusiness $list): ?string
    {
        if (ListImageUrlResolver::resolveLocalFilesystemPath($list->image, $list->user_id) !== null) {
            return URL::temporarySignedRoute(
                'stripe.checkout-listing-cover',
                now()->addDays(30),
                ['listId' => $list->id],
                true
            );
        }

        $coverUrl = ListImageUrlResolver::resolve($list->image, $list->user_id);
        if (is_string($coverUrl) && str_starts_with($coverUrl, 'https://') && $this->isStripeCheckoutBrandingLogoUrl($coverUrl)) {
            return $coverUrl;
        }

        return null;
    }

    /**
     * Stripe Checkout branding logo URLs must be HTTPS and the path must end with an allowed raster/vector suffix.
     */
    private function isStripeCheckoutBrandingLogoUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['path'])) {
            return false;
        }

        $path = strtolower($parts['path']);
        $allowed = ['.png', '.jpg', '.jpeg', '.jfif', '.pjpeg', '.pjp', '.svg'];

        foreach ($allowed as $suffix) {
            if (str_ends_with($path, $suffix)) {
                return true;
            }
        }

        return false;
    }
}
