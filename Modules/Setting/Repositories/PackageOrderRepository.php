<?php

namespace Modules\Setting\Repositories;

use Modules\Setting\Entities\PackageOrder;

class PackageOrderRepository
{
    protected PackageOrder $model;

    public function __construct(PackageOrder $model)
    {
        $this->model = $model;
    }

    public function paginate(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->query()->with(['user', 'list']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['user_search'])) {
            $search = trim($filters['user_search']);

            $keywords = preg_split('/\s+/', $search);

            $query->where(function ($q) use ($search, $keywords) {

                if (is_numeric($search)) {
                    $q->orWhere('user_id', $search);
                }

                $q->orWhereHas('user', function ($uq) use ($keywords) {
                    foreach ($keywords as $word) {
                        $uq->where(function ($wq) use ($word) {
                            $wq->where('firstname', 'LIKE', "%{$word}%")
                                ->orWhere('lastname', 'LIKE', "%{$word}%");
                        });
                    }
                });
            });
        }

        if (!empty($filters['item_search'])) {
            $itemSearch = mb_strtolower(trim($filters['item_search']));

            $query->whereRaw(
                "LOWER(items) LIKE ?",
                ["%{$itemSearch}%"]
            );
        }

        if (!empty($filters['package_id']) && $filters['package_id'] !== "all") {
            $packageId = (int) $filters['package_id'];

            $query->whereRaw(
                "JSON_CONTAINS(
                    JSON_EXTRACT(items, '$[*].package_id'),
                    ?
                )",
                [json_encode($packageId)]
            );
        }

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);

            $query->whereHas('list', function ($lq) use ($search) {
                $lq->where(function ($w) use ($search) {
                    $w->where('listing_id', 'LIKE', "%{$search}%")
                        ->orWhere('company_name', 'LIKE', "%{$search}%");
                });
            });
        }

        if (!empty($filters['list_id'])) {
            $query->where('list_id', $filters['list_id']);
        }
        if (!empty($filters['listing_id'])) {
            $query->whereHas('list', function ($q) use ($filters) {
                $q->where('listing_id', $filters['listing_id']);
            });
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        if (array_key_exists('package_type', $filters) && $filters['package_type'] !== null && $filters['package_type'] !== "all") {
            $query->where('package_type', $filters['package_type']);
        }


        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }


    public function find($id)
    {
        return $this->model->query()->with(['list'])->find($id);
    }

    public function findUnpaidByUserId(int $id, int $userId)
    {
        return $this->model->newQuery()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('status', 0)
            ->first();
    }

    public function findByUserId(int $id, int $userId)
    {
        return $this->model->newQuery()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * آخرین پرداخت موفق یک لیست
     */
    public function getLatestPaidByListId(int $listId)
    {
        return $this->model->where('list_id', $listId)->orderByDesc('id')->get();
    }
}
