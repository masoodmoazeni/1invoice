<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Services\PageBuilderService;

class PageBuilderController extends BaseController
{
    protected $service;

    public function __construct(PageBuilderService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/pages",
     *     summary="show list of pagebuilder",
     *     tags={"PageBuilder"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="draft | temp | published | archived",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="pagebuilder list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get pagebuilder list"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);

            $filters = $request->only([
                'status',
            ]);

            $pages = $this->service->paginate($perPage, $filters);

            return $this->successResponse($pages);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/pages/detail/{slug}",
     *     summary="Show page details",
     *     tags={"PageBuilder"},
     *     @OA\Parameter(name="slug", in="path", description="slug of the Sales to show", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Show page  successfully"),
     *     @OA\Response(response=404, description="Sales not found")
     * )
     */
    public function showSlug($slug)
    {
        try {
            $page = $this->service->findPublishedBySlug($slug);

            if (!$page) {
                return $this->errorResponse('Page not found', 404);
            }

            return $this->successResponse($page->toPublishedPayload());
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/pages/slugs",
     *     summary="List page slugs (no pagination)",
     *     tags={"PageBuilder"},
     *     @OA\Parameter(name="status", in="query", description="draft | temp | published | archived", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Slugs retrieved successfully")
     * )
     */
    public function slugs(Request $request)
    {
        try {
            $filters = $request->only(['status', 'type']);
            if (empty($filters['status'])) {
                $filters['status'] = 'published';
            }

            return $this->successResponse($this->service->pluckSlugs($filters));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }
}
