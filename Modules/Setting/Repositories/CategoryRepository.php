<?php

namespace Modules\Setting\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Entities\Category;

class CategoryRepository
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function paginateWithSearch($perPage = 15, $search = null, $parent_id = null)
    {
        $query = $this->model->query();

        if (!empty($search)) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        if (!is_null($parent_id)) {
            $query->where('parent_id', $parent_id);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->paginate($perPage);
    }

    public function getAllWithChildren($search = null)
    {
        $query = $this->model
            ->with(['children' => function ($q) {
                $q->with('children');
            }])
            ->whereNull('parent_id');

        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->get();
    }

    public function getAllWithChildrenWithCountList(array $ids = [])
    {
        $categories = $this->model
            ->with(['children.children'])
            ->whereNull('parent_id')
            ->get();

        if (empty($ids)) {
            return $this->buildZeroCountTree($categories);
        }

        $counts = DB::table('list_categories')
            ->whereNull('deleted_at')
            ->when(!empty($ids), function ($query) use ($ids) {
                $query->whereIn('list_id', $ids);
            })
            ->select('subcategory_id', DB::raw('COUNT(DISTINCT list_id) as total'))
            ->groupBy('subcategory_id')
            ->pluck('total', 'subcategory_id')
            ->toArray();

        $result = [];

        foreach ($categories as $category) {

            $categoryListIdsCount = 0;
            $childrenArray = [];

            foreach ($category->children as $sub) {

                $grandChildrenArray = [];
                $subCount = $counts[$sub->id] ?? 0;

                $grandChildrenTotal = 0;

                foreach ($sub->children as $child) {

                    $childCount = $counts[$child->id] ?? 0;
                    $grandChildrenTotal += $childCount;

                    $childArray = $child->toArray();
                    $childArray['count'] = $childCount;

                    $grandChildrenArray[] = $childArray;
                }

                $finalSubCount = $subCount + $grandChildrenTotal;
                $categoryListIdsCount += $finalSubCount;

                $subArray = $sub->toArray();
                $subArray['count'] = $finalSubCount;
                $subArray['children'] = $grandChildrenArray;

                $childrenArray[] = $subArray;
            }

            $categoryArray = $category->toArray();
            $categoryArray['count'] = $categoryListIdsCount;
            $categoryArray['children'] = $childrenArray;

            $result[] = $categoryArray;
        }

        return $result;
    }

    private function buildZeroCountTree($categories)
    {
        $result = [];

        foreach ($categories as $category) {

            $childrenArray = [];

            foreach ($category->children as $sub) {

                $grandChildrenArray = [];

                foreach ($sub->children as $child) {

                    $childArray = $child->toArray();
                    $childArray['count'] = 0;

                    $grandChildrenArray[] = $childArray;
                }

                $subArray = $sub->toArray();
                $subArray['count'] = 0;
                $subArray['children'] = $grandChildrenArray;

                $childrenArray[] = $subArray;
            }

            $categoryArray = $category->toArray();
            $categoryArray['count'] = 0;
            $categoryArray['children'] = $childrenArray;

            $result[] = $categoryArray;
        }

        return $result;
    }
}
