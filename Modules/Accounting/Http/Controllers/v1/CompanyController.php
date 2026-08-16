<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Http\Requests\CompanyRequest;
use Modules\Accounting\Services\CompanyService;

class CompanyController extends BaseController
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * @OA\Get(
     *     path="/company",
     *     summary="show list of companies",
     *     tags={"Company"},
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
     *         description="Search by company name",
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

            $filters = [];
            if ($search) {
                $filters['search'] = $search;
            }

            $companies = $this->companyService->getPaginate($perPage, $filters);

            return $this->successResponse($companies);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse($ex->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/company",
     *     summary="create new company",
     *     tags={"Company"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name", "fiscal_year_start_month"},
     *             @OA\Property(property="code", type="string", maxLength=50, example="COMP-001", description="کد یکتای شرکت"),
     *             @OA\Property(property="name", type="string", maxLength=255, example="شرکت نمونه", description="نام شرکت"),
     *             @OA\Property(property="legal_name", type="string", maxLength=255, nullable=true, example="شرکت نمونه با مسئولیت محدود", description="نام حقوقی شرکت"),
     *             @OA\Property(property="country_id", type="integer", nullable=true, example=1, description="شناسه کشور"),
     *             @OA\Property(property="base_currency_id", type="integer", nullable=true, example=1, description="شناسه ارز پایه"),
     *             @OA\Property(property="language_id", type="integer", nullable=true, example=1, description="شناسه زبان"),
     *             @OA\Property(property="timezone_id", type="integer", nullable=true, example=1, description="شناسه منطقه زمانی"),
     *             @OA\Property(property="tax_number", type="string", maxLength=50, nullable=true, example="1234567890", description="شماره مالیاتی"),
     *             @OA\Property(property="registration_number", type="string", maxLength=50, nullable=true, example="12345", description="شماره ثبت شرکت"),
     *             @OA\Property(property="phone", type="string", maxLength=20, nullable=true, example="021-12345678", description="شماره تلفن"),
     *             @OA\Property(property="mobile", type="string", maxLength=20, nullable=true, example="09121234567", description="شماره همراه"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, nullable=true, example="info@company.com", description="آدرس ایمیل"),
     *             @OA\Property(property="website", type="string", format="url", maxLength=255, nullable=true, example="https://www.company.com", description="آدرس وبسایت"),
     *             @OA\Property(property="address", type="string", nullable=true, example="تهران، خیابان اصلی، پلاک ۱۲۳", description="آدرس کامل"),
     *             @OA\Property(property="postal_code", type="string", maxLength=20, nullable=true, example="1234567890", description="کد پستی"),
     *             @OA\Property(property="city", type="string", maxLength=100, nullable=true, example="تهران", description="شهر"),
     *             @OA\Property(property="state", type="string", maxLength=100, nullable=true, example="تهران", description="استان"),
     *             @OA\Property(property="logo", type="string", maxLength=255, nullable=true, example="uploads/company/logo.png", description="مسیر فایل لوگو"),
     *             @OA\Property(property="fiscal_year_start_month", type="integer", minimum=1, maximum=12, example=1, description="ماه شروع سال مالی (1-12)"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Company created successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Failed to create company")
     * )
     */
    public function create(CompanyRequest $request)
    {
        try {
            $validatedData = $request->validated();
        
            $company = $this->companyService->create($validatedData);

            return $this->successResponse($company, 'company created successfully');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->errorResponse('Failed to create company: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('accounting::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('accounting::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
