<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Modules\Setting\Services\StateService;
use Illuminate\Support\Facades\Log;

class StateController extends BaseController
{
    protected $stateService;

    public function __construct(StateService $stateService)
    {
        $this->stateService = $stateService;
    }
    
    /**
     * @OA\Get(
     *     path="/settings/state",
     *     summary="show list of states",
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
     *         description="Search by state name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
    *      @OA\Parameter(
    *         name="country_id",
    *         in="query",
    *         description="Filter states by country id",
    *         required=false,
    *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="State list retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Failed to get state list"
     *     )
     * )
     */
    public function index()
    {
        try {
            $perPage = request()->get('per_page', 15);

            $search = request()->get('search', null);
            $country_id = request()->get('country_id', null);
            $states = $this->stateService->getPaginatedCountries($perPage, $search, $country_id);

            return $this->successResponse($states);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }
    
}
