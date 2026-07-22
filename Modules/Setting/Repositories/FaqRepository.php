<?php

namespace Modules\Setting\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Setting\Entities\FaqCategory;
use Modules\Setting\Entities\FaqItem;
use Modules\Setting\Entities\FaqSetting;

class FaqRepository
{
    public function getPublishedGrouped(): array
    {
        return FaqCategory::query()
            ->where('status', FaqCategory::STATUS_PUBLISHED)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['publishedItems'])
            ->get()
            ->all();
    }

    public function paginateCategories(int $perPage = 15, array $filters = [])
    {
        $query = FaqCategory::query()->withCount('items');
        $this->applyCategoryFilters($query, $filters);

        return $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function paginateItems(int $perPage = 15, array $filters = [])
    {
        $query = FaqItem::query()->with('category');
        $this->applyItemFilters($query, $filters);

        return $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function findCategoryById(int $id): ?FaqCategory
    {
        return FaqCategory::with('items')->find($id);
    }

    public function findItemById(int $id): ?FaqItem
    {
        return FaqItem::with('category')->find($id);
    }

    public function createCategory(array $data): FaqCategory
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = (int) FaqCategory::max('sort_order') + 1;
        }

        return FaqCategory::create($data);
    }

    public function updateCategory(FaqCategory $category, array $data): FaqCategory
    {
        $category->update($data);

        return $category->fresh(['items']);
    }

    public function deleteCategory(FaqCategory $category): void
    {
        $category->delete();
    }

    public function createItem(array $data): FaqItem
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = (int) FaqItem::where('faq_category_id', $data['faq_category_id'])
                ->max('sort_order') + 1;
        }

        return FaqItem::create($data);
    }

    public function updateItem(FaqItem $item, array $data): FaqItem
    {
        $item->update($data);

        return $item->fresh(['category']);
    }

    public function deleteItem(FaqItem $item): void
    {
        $item->delete();
    }

    public function reorderCategories(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) {
                FaqCategory::where('id', $id)->update(['sort_order' => $index]);
            }
        });
    }

    public function reorderItems(int $categoryId, array $orderedIds): void
    {
        DB::transaction(function () use ($categoryId, $orderedIds) {
            foreach ($orderedIds as $index => $id) {
                FaqItem::where('id', $id)
                    ->where('faq_category_id', $categoryId)
                    ->update(['sort_order' => $index]);
            }
        });
    }

    public function getSettings(): FaqSetting
    {
        return FaqSetting::query()->firstOrCreate([]);
    }

    public function updateSettings(FaqSetting $settings, array $data): FaqSetting
    {
        $settings->update($data);

        return $settings->fresh();
    }

    private function applyCategoryFilters($query, array $filters): void
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }
    }

    private function applyItemFilters($query, array $filters): void
    {
        if (!empty($filters['faq_category_id'])) {
            $query->where('faq_category_id', $filters['faq_category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }
    }
}
