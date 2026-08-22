<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\DimensionService;

use Modules\Accounting\Http\Requests\Dimension\DimensionRequest;
use Modules\Accounting\Http\Requests\Dimension\DimensionUpdateRequest;

class DimensionController extends BaseController
{
    protected $dimensionService;

    public function __construct(DimensionService $dimensionService)
    {
        $this->dimensionService = $dimensionService;
    }

    /**
     * @OA\Get(
     *     path="/dimension",
     *     summary="نمایش لیست ابعاد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام یا کد", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="company_id", in="query", description="فیلتر بر اساس شناسه شرکت", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="type", in="query", description="فیلتر بر اساس نوع بعد", required=false, @OA\Schema(type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"})),
     *     @OA\Parameter(name="is_active", in="query", description="فیلتر بر اساس وضعیت فعال", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="has_transactions", in="query", description="فیلتر بر اساس داشتن تراکنش", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort_by", in="query", description="فیلد مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"id","code","name","type","is_active","created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="ترتیب مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc")),
     *     @OA\Response(response=200, description="لیست ابعاد مالی با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست ابعاد مالی"),
     *     @OA\Response(response=500, description="خطای داخلی سرور")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            
            $filters = [];
            
            if ($request->has('search')) {
                $filters['search'] = $request->get('search');
            }
            
            if ($request->has('company_id')) {
                $filters['company_id'] = $request->get('company_id');
            }
            
            if ($request->has('type')) {
                $filters['type'] = $request->get('type');
            }
            
            if ($request->has('is_active')) {
                $filters['is_active'] = $request->get('is_active');
            }
            
            if ($request->has('has_transactions')) {
                $filters['has_transactions'] = $request->get('has_transactions');
            }
            
            if ($request->has('sort_by')) {
                $filters['sort_by'] = $request->get('sort_by');
            }
            
            
            $dimensions = $this->dimensionService->getPaginate($perPage, $filters);

            return $this->successResponse($dimensions, 'لیست ابعاد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ابعاد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/dimension",
     *     summary="ایجاد بعد مالی جدید",
     *     tags={"Dimension"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "name", "type"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="CC_IT", nullable=true, description="کد بعد (در صورت عدم ارسال، خودکار تولید می‌شود)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مرکز هزینه فناوری اطلاعات", description="نام بعد"),
     *             @OA\Property(property="type", type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"}, example="cost_center", description="نوع بعد"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=201, description="بعد مالی با موفقیت ایجاد شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد بعد مالی")
     * )
     */
    public function store(DimensionRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // اگر کد ارسال نشده، به صورت خودکار تولید می‌شود
            if (!isset($validatedData['code']) || empty($validatedData['code'])) {
                $validatedData['code'] = $this->dimensionService->generateDimensionCode(
                    $validatedData['name'],
                    $validatedData['type'],
                    $validatedData['company_id']
                );
            }
            
            $dimension = $this->dimensionService->create($validatedData);

            return $this->successResponse($dimension, 'بعد مالی با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد بعد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/{id}",
     *     summary="نمایش اطلاعات یک بعد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="id", in="path", description="شناسه بعد مالی", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="اطلاعات بعد مالی با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="بعد مالی یافت نشد")
     * )
     */
    public function show($id)
    {
        try {
            $dimension = $this->dimensionService->find($id);
            
            if (!$dimension) {
                return $this->errorResponse('بعد مالی مورد نظر یافت نشد', 404);
            }

            // بارگذاری روابط مربوطه
            $dimension->load(['company']);

            return $this->successResponse($dimension, 'اطلاعات بعد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات بعد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/dimension/{id}",
     *     summary="به‌روزرسانی بعد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="id", in="path", description="شناسه بعد مالی", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="CC_IT", description="کد بعد"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مرکز هزینه فناوری اطلاعات", description="نام بعد"),
     *             @OA\Property(property="type", type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"}, example="cost_center", description="نوع بعد"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=200, description="بعد مالی با موفقیت به‌روزرسانی شد"),
     *     @OA\Response(response=404, description="بعد مالی یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function update(DimensionUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $dimension = $this->dimensionService->update($id, $validatedData);

            return $this->successResponse($dimension, 'بعد مالی با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی بعد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/dimension/{id}",
     *     summary="حذف بعد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="id", in="path", description="شناسه بعد مالی", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="بعد مالی با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="بعد مالی یافت نشد"),
     *     @OA\Response(response=422, description="این بعد مالی در حال استفاده است و قابل حذف نمی‌باشد")
     * )
     */
    public function destroy($id)
    {
        try {
            // بررسی می‌کنیم که آیا بعد مالی در حال استفاده است یا خیر
            $hasTransactions = $this->dimensionService->hasTransactions($id);
            
            if ($hasTransactions) {
                return $this->errorResponse('این بعد مالی در حال استفاده است و قابل حذف نمی‌باشد', 422);
            }
            
            $this->dimensionService->delete($id);

            return $this->successResponse(null, 'بعد مالی با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف بعد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/dimension/{id}/toggle-status",
     *     summary="تغییر وضعیت فعال/غیرفعال بعد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="id", in="path", description="شناسه بعد مالی", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="وضعیت بعد مالی با موفقیت تغییر یافت"),
     *     @OA\Response(response=404, description="بعد مالی یافت نشد")
     * )
     */
    public function toggleStatus($id)
    {
        try {
            $dimension = $this->dimensionService->toggleStatus($id);

            return $this->successResponse($dimension, 'وضعیت بعد مالی با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@toggleStatus: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت بعد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/active",
     *     summary="دریافت تمام ابعاد مالی فعال",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت (اختیاری)", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="type", in="query", description="نوع بعد (اختیاری)", required=false, @OA\Schema(type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"})),
     *     @OA\Response(response=200, description="لیست ابعاد مالی فعال با موفقیت دریافت شد")
     * )
     */
    public function getActive(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $type = $request->get('type');
            
            $dimensions = $this->dimensionService->getActiveDimensions($companyId, $type);
            
            return $this->successResponse($dimensions, 'لیست ابعاد مالی فعال با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@getActive: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ابعاد مالی فعال: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/by-type/{type}",
     *     summary="دریافت ابعاد مالی بر اساس نوع",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="type", in="path", description="نوع بعد", required=true, @OA\Schema(type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"})),
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="only_active", in="query", description="فقط ابعاد فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Response(response=200, description="لیست ابعاد مالی با موفقیت دریافت شد")
     * )
     */
    public function getByType($type, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $onlyActive = $request->get('only_active', true);
            $dimensions = $this->dimensionService->getByType($companyId, $type, $onlyActive);
            
            return $this->successResponse($dimensions, 'لیست ابعاد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@getByType: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ابعاد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/types",
     *     summary="دریافت لیست انواع ابعاد مالی",
     *     tags={"Dimension"},
     *     @OA\Response(response=200, description="لیست انواع ابعاد مالی با موفقیت دریافت شد")
     * )
     */
    public function getTypes()
    {
        try {
            $types = $this->dimensionService->getDimensionTypes();
            
            return $this->successResponse($types, 'لیست انواع ابعاد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@getTypes: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع ابعاد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/list",
     *     summary="دریافت لیست ساده ابعاد مالی برای استفاده در dropdown",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="only_active", in="query", description="فقط ابعاد فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Parameter(name="type", in="query", description="نوع بعد (اختیاری)", required=false, @OA\Schema(type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"})),
     *     @OA\Parameter(name="with_code", in="query", description="نمایش با کد", required=false, @OA\Schema(type="boolean", default=false)),
     *     @OA\Response(response=200, description="لیست ابعاد مالی با موفقیت دریافت شد")
     * )
     */
    public function getList(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $onlyActive = $request->get('only_active', true);
            $type = $request->get('type');
            $withCode = $request->get('with_code', false);
            
            $dimensions = $this->dimensionService->getList($companyId, $onlyActive, $type, $withCode);
            
            return $this->successResponse($dimensions, 'لیست ابعاد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ابعاد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/{id}/statistics",
     *     summary="دریافت آمار و اطلاعات یک بعد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="id", in="path", description="شناسه بعد مالی", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="start_date", in="query", description="تاریخ شروع (اختیاری)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="end_date", in="query", description="تاریخ پایان (اختیاری)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="آمار بعد مالی با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="بعد مالی یافت نشد")
     * )
     */
    public function getStatistics($id, Request $request)
    {
        try {
            $dimension = $this->dimensionService->find($id);
            
            if (!$dimension) {
                return $this->errorResponse('بعد مالی مورد نظر یافت نشد', 404);
            }
            
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $statistics = $this->dimensionService->getStatistics($id, $startDate, $endDate);
            
            return $this->successResponse($statistics, 'آمار بعد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@getStatistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار بعد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/has-transactions/{id}",
     *     summary="بررسی اینکه آیا بعد مالی دارای تراکنش است",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="id", in="path", description="شناسه بعد مالی", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="بررسی با موفقیت انجام شد"),
     *     @OA\Response(response=404, description="بعد مالی یافت نشد")
     * )
     */
    public function hasTransactions($id)
    {
        try {
            $dimension = $this->dimensionService->find($id);
            
            if (!$dimension) {
                return $this->errorResponse('بعد مالی مورد نظر یافت نشد', 404);
            }
            
            $hasTransactions = $this->dimensionService->hasTransactions($id);
            
            return $this->successResponse([
                'id' => $id,
                'has_transactions' => $hasTransactions,
            ], 'بررسی با موفقیت انجام شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@hasTransactions: ' . $ex->getMessage());
            return $this->errorResponse('خطا در بررسی تراکنش‌های بعد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/statistics",
     *     summary="دریافت آمار کلی ابعاد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="آمار کلی ابعاد مالی با موفقیت دریافت شد")
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $statistics = $this->dimensionService->getStatisticsOverview($companyId);
            
            return $this->successResponse($statistics, 'آمار کلی ابعاد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار کلی ابعاد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension/report",
     *     summary="دریافت گزارش ابعاد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", description="وضعیت", required=false, @OA\Schema(type="string", enum={"active","inactive","all"})),
     *     @OA\Parameter(name="type", in="query", description="نوع بعد", required=false, @OA\Schema(type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"})),
     *     @OA\Parameter(name="search", in="query", description="جستجو", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="گزارش ابعاد مالی با موفقیت دریافت شد")
     * )
     */
    public function getReport(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $filters = [
                'status' => $request->get('status', 'all'),
                'type' => $request->get('type'),
                'search' => $request->get('search'),
            ];
            
            $report = $this->dimensionService->getReport($companyId, $filters);
            
            return $this->successResponse($report, 'گزارش ابعاد مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@getReport: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت گزارش ابعاد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/dimension/bulk-create",
     *     summary="ایجاد چندین بعد مالی به صورت همزمان",
     *     tags={"Dimension"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dimensions"},
     *             @OA\Property(
     *                 property="dimensions",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"company_id", "name", "type"},
     *                     @OA\Property(property="company_id", type="integer", example=1),
     *                     @OA\Property(property="code", type="string", maxLength=50),
     *                     @OA\Property(property="name", type="string", maxLength=100),
     *                     @OA\Property(property="type", type="string", enum={"cost_center","project","department","customer","vendor","employee","product","region","custom"}),
     *                     @OA\Property(property="is_active", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="ابعاد مالی با موفقیت ایجاد شدند"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد ابعاد مالی")
     * )
     */
    public function bulkCreate(Request $request)
    {
        try {
            $request->validate([
                'dimensions' => 'required|array|min:1',
                'dimensions.*.company_id' => 'required|exists:companies,id',
                'dimensions.*.name' => 'required|string|max:100',
                'dimensions.*.type' => 'required|string|in:' . implode(',', $this->dimensionService->getValidTypes()),
                'dimensions.*.code' => 'nullable|string|max:50',
                'dimensions.*.is_active' => 'nullable|boolean',
            ]);
            
            $dimensions = $this->dimensionService->bulkCreate($request->dimensions);
            
            return $this->successResponse($dimensions, 'ابعاد مالی با موفقیت ایجاد شدند', 201);
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@bulkCreate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد ابعاد مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/dimension/{id}/duplicate",
     *     summary="کپی کردن یک بعد مالی",
     *     tags={"Dimension"},
     *     @OA\Parameter(name="id", in="path", description="شناسه بعد مالی", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=100, example="مرکز هزینه فناوری اطلاعات (کپی)", description="نام بعد جدید"),
     *             @OA\Property(property="code", type="string", maxLength=50, description="کد بعد جدید (اختیاری)")
     *         )
     *     ),
     *     @OA\Response(response=201, description="بعد مالی با موفقیت کپی شد"),
     *     @OA\Response(response=404, description="بعد مالی یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function duplicate($id, Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'code' => 'nullable|string|max:50',
            ]);
            
            $dimension = $this->dimensionService->duplicate($id, $request->all());
            
            return $this->successResponse($dimension, 'بعد مالی با موفقیت کپی شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in DimensionController@duplicate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در کپی بعد مالی: ' . $ex->getMessage(), 500);
        }
    }
}
