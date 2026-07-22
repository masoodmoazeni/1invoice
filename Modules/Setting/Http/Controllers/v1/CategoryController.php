<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Setting\Services\CategoryService;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     *     path="/categories/list",
     *     summary="show list of categories",
     *     tags={"Main Business Type"},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="category list retrieved successfully"),
     *     @OA\Response(response=422, description="Failed to get category list")
     * )
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 15);
            $search = request()->get('search', null);

            $categories = $this->categoryService->getPaginatedCategories($perPage, $search);

            return $this->successResponse($categories);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }


    /**
     * @OA\Get(
     *     path="/categories/sub/list",
     *     summary="show list of sub categories",
     *     tags={"Main Business Type"},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", description="Filter by parent category id", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="sub category list retrieved successfully"),
     *     @OA\Response(response=422, description="Failed to get subcategory list")
     * )
     */
    public function subcategory()
    {
        try {
            $perPage = request()->get('per_page', 15);
            $search = request()->get('search', null);
            $categoryId = request()->get('category_id', null);

            $subcategories = $this->categoryService->getPaginatedSubcategories($perPage, $search, $categoryId);

            return $this->successResponse($subcategories);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }


    /**
     * @OA\Get(
     *     path="/categories/all",
     *     summary="show list of categories",
     *     tags={"Main Business Type"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="category list retrieved successfully"),
     *     @OA\Response(response=422, description="Failed to get category list")
     * )
     */
    public function all()
    {
        try {
            $search = request()->get('search', null);
            $categories = $this->categoryService->getAllCategoriesWithSubcategories($search);

            return $this->successResponse($categories);

            return $this->successResponse($categories);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

}
