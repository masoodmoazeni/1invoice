<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Services\CountryService;

class CountryController extends BaseController
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }
    
    /**
     * @OA\Get(
     *     path="/settings/country",
     *     summary="show list of countries",
     *     tags={"Zone"},
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
     *         description="Search by country name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Country list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get country list"
     *     )
     * )
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 15);

            $search = request()->get('search', null);
            $countries = $this->countryService->getPaginatedCountries($perPage, $search);

            return $this->successResponse($countries);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/settings/zone/all",
     *     summary="show list of countries",
     *     tags={"Zone"},
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
     *         description="Search by country name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Country list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get country list"
     *     )
     * )
     */
    public function all()
    {
        try {
            $data = $this->countryService->getAllHierarchy();
            return $this->successResponse($data, 'Hierarchy retrieved successfully');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse('Failed to get settings: ' . $ex->getMessage(), 500);
        }
    }
}
