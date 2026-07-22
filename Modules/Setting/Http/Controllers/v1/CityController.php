<?php

namespace Modules\Setting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Setting\Services\CityService;

class CityController extends BaseController
{
    protected $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    /**
     * @OA\Get(
     *     path="/settings/city",
     *     summary="show list of cities",
     *     tags={"Zone"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by city name",
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
     *     @OA\Parameter(
     *         name="state_id",
     *         in="query",
     *         description="Filter city by state id",
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
            $country_id = request()->get('country_id', null);
            $state_id = request()->get('state_id', null);

            if($country_id == null){
                return $this->errorResponse("please import country_id");
            }
            if($state_id == null){
                return $this->errorResponse("please import city_id");
            }

            $search = request()->get('search', null);
            
            $states = $this->cityService->getPaginatedCountries($search, $country_id, $state_id);

            return $this->successResponse($states);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }
}
