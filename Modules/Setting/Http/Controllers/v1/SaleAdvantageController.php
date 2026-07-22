<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Modules\Setting\Services\SaleAdvantageService;

class SaleAdvantageController extends BaseController
{
    protected $saleAdvantageService;

    public function __construct(SaleAdvantageService $saleAdvantageService)
    {
        $this->saleAdvantageService = $saleAdvantageService;
    }

    /**
     * @OA\Get(
     *     path="/types/sale",
     *     summary="show list of sales",
     *     tags={"Type Of Sale"},
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
     *         name="search",
     *         in="query",
     *         description="Search by sale name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sale list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get sale list"
     *     )
     * )
     */
    public function sale()
    {
        try {
            $perPage = request()->get('per_page', 15);

            $search = request()->get('search', null);
            $data = $this->saleAdvantageService->getPagination($perPage, $search, 'sale');

            return $this->successResponse($data);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }


    /**
     * @OA\Get(
     *     path="/types/occupancy",
     *     summary="show list of occupancy",
     *     tags={"Type Of Occupancy"},
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
     *         name="search",
     *         in="query",
     *         description="Search by occupancy name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="occupancy list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get occupancy list"
     *     )
     * )
     */
    public function occupancy()
    {
        try {
            $perPage = request()->get('per_page', 15);

            $search = request()->get('search', null);
            $data = $this->saleAdvantageService->getPagination($perPage, $search,'occupancy');

            return $this->successResponse($data);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/types/business",
     *     summary="show list of business",
     *     tags={"Type Of Business"},
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
     *         name="search",
     *         in="query",
     *         description="Search by business name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="business list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get business list"
     *     )
     * )
     */
    public function business()
    {
        try {
            $perPage = request()->get('per_page', 15);

            $search = request()->get('search', null);
            $data = $this->saleAdvantageService->getPagination($perPage, $search,'business');

            return $this->successResponse($data);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/types/all",
     *     summary="show list of business",
     *     tags={"Type Of All"},
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
     *         name="search",
     *         in="query",
     *         description="Search by business name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="business list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get business list"
     *     )
     * )
     */
    public function all()
    {
        try {
            $data = $this->saleAdvantageService->getAllTypesTree();
            return $this->successResponse($data);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }
}
