<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Str;
use Modules\Setting\Entities\FaqCategory;
use Modules\Setting\Entities\FaqItem;
use Modules\Setting\Repositories\FaqRepository;

class FaqService
{
    public function __construct(protected FaqRepository $repo)
    {
    }

    public function getPublishedGrouped(): array
    {
        return $this->repo->getPublishedGrouped();
    }

    public function paginateCategories(int $perPage = 15, array $filters = [])
    {
        return $this->repo->paginateCategories($perPage, $filters);
    }

    public function paginateItems(int $perPage = 15, array $filters = [])
    {
        return $this->repo->paginateItems($perPage, $filters);
    }

    public function findCategory(int $id): FaqCategory
    {
        $category = $this->repo->findCategoryById($id);

        if (!$category) {
            throw new \Exception('FAQ category not found');
        }

        return $category;
    }

    public function findItem(int $id): FaqItem
    {
        $item = $this->repo->findItemById($id);

        if (!$item) {
            throw new \Exception('FAQ item not found');
        }

        return $item;
    }

    public function createCategory(array $data): FaqCategory
    {
        $data['slug'] = $this->resolveUniqueCategorySlug($data['slug'] ?? $data['title']);

        return $this->repo->createCategory($data);
    }

    public function updateCategory(int $id, array $data): FaqCategory
    {
        $category = $this->findCategory($id);

        if (array_key_exists('slug', $data) && filled($data['slug'])) {
            $data['slug'] = $this->resolveUniqueCategorySlug($data['slug'], $id);
        } else {
            unset($data['slug']);
        }

        return $this->repo->updateCategory($category, $data);
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->findCategory($id);
        $this->repo->deleteCategory($category);
    }

    public function createItem(array $data): FaqItem
    {
        $this->findCategory($data['faq_category_id']);

        return $this->repo->createItem($data);
    }

    public function updateItem(int $id, array $data): FaqItem
    {
        $item = $this->findItem($id);

        if (isset($data['faq_category_id'])) {
            $this->findCategory($data['faq_category_id']);
        }

        return $this->repo->updateItem($item, $data);
    }

    public function deleteItem(int $id): void
    {
        $item = $this->findItem($id);
        $this->repo->deleteItem($item);
    }

    public function reorderCategories(array $orderedIds): void
    {
        $this->repo->reorderCategories($orderedIds);
    }

    public function reorderItems(int $categoryId, array $orderedIds): void
    {
        $this->findCategory($categoryId);
        $this->repo->reorderItems($categoryId, $orderedIds);
    }

    public function getSettings()
    {
        return $this->repo->getSettings();
    }

    public function updateSettings(array $data)
    {
        $settings = $this->repo->getSettings();

        return $this->repo->updateSettings($settings, $data);
    }

    private function resolveUniqueCategorySlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $counter = 1;

        while (
            FaqCategory::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
