<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\PackageOrderRepository;

class PackageOrderService
{
    protected PackageOrderRepository $repository;

    public function __construct(PackageOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function paginate(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginate($filters, $perPage);
    }


    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function findByUserId(int $id, int $userId)
    {
        return $this->repository->findByUserId($id, $userId);
    }

    public function deleteUnpaidTransaction(int $id, int $userId): bool
    {
        $order = $this->repository->findUnpaidByUserId($id, $userId);
        if (!$order) {
            return false;
        }

        return (bool) $order->delete();
    }

    public function getListPaymentDetail(int $listId): ?array
    {
        $orders = $this->repository->getLatestPaidByListId($listId);

        if ($orders->isEmpty()) {
            return [];
        }

        return $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'monthly_amount' => $order->monthly_amount,
                'status' => $order->status,
                'items' => $order->items, // cast array
                'stripe_session_id' => $order->stripe_session_id,
                'stripe_checkout_url' => $order->stripe_checkout_url,
                'stripe_customer_id' => $order->stripe_customer_id,
                'stripe_subscription_id' => $order->stripe_subscription_id,
                'starts_at' => $order->starts_at,
                'trial_ends_at' => $order->trial_ends_at,
                'ends_at' => $order->ends_at,
                'created_at' => $order->created_at,
            ];
        })->values()->toArray();
    }
}
