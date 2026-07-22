<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\CategoryRepository;

class CategoryService
{
    protected $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function getPaginatedCategories($perPage = 15, $search = null)
    {
        return $this->categories->paginateWithSearch($perPage, $search, null);
    }

    public function getPaginatedSubcategories($perPage = 15, $search = null, $category_id = null)
    {
        return $this->categories->paginateWithSearch($perPage, $search, $category_id);
    }

    public function getAllCategoriesWithSubcategories($search = null)
    {
        return $this->categories->getAllWithChildren($search);
    }

    public function getAllCategoriesWithSubcategoriesWithCountList($ids = [], $category_id = [], $subcategory_id = [])
    {
        return $this->categories->getAllWithChildrenWithCountList($ids, $category_id, $subcategory_id);
    }
}
