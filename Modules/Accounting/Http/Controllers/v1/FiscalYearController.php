<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\FiscalYearService;
use Modules\Accounting\Http\Requests\FiscalYear\FiscalYearRequest;
use Modules\Accounting\Http\Requests\FiscalYear\FiscalYearUpdateRequest;

class FiscalYearController extends BaseController
{
    protected $fiscalYearService;

    public function __construct(FiscalYearService $fiscalYearService)
    {
        $this->fiscalYearService = $fiscalYearService;
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year",
     *     summary="نمایش لیست سال‌های مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="شماره صفحه",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="تعداد آیتم در هر صفحه",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="جستجو بر اساس نام یا تاریخ سال مالی",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="وضعیت سال مالی",
     *         required=false,
     *         @OA\Schema(type="string", enum={"open", "closed", "pending", "locked"})
     *     ),
     *     @OA\Parameter(
     *         name="is_default",
     *         in="query",
     *         description="سال مالی پیش‌فرض",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="is_current",
     *         in="query",
     *         description="سال مالی جاری",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست سال‌های مالی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست سال‌های مالی"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only([
                'search',
                'company_id',
                'status',
                'is_default',
                'is_current'
            ]);

            // حذف فیلترهای خالی
            $filters = array_filter($filters, function($value) {
                return !is_null($value) && $value !== '';
            });

            $fiscalYears = $this->fiscalYearService->getPaginate($perPage, $filters);

            return $this->successResponse($fiscalYears, 'لیست سال‌های مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست سال‌های مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/fiscal-year",
     *     summary="ایجاد سال مالی جدید",
     *     tags={"FiscalYear"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "name", "start_date", "end_date"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="سال مالی ۱۴۰۳", description="نام سال مالی"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-03-21", description="تاریخ شروع"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-03-20", description="تاریخ پایان"),
     *             @OA\Property(property="status", type="string", enum={"open", "closed", "pending", "locked"}, example="pending", description="وضعیت سال مالی"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="سال مالی پیش‌فرض")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="سال مالی با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="تاریخ‌های سال مالی با سال مالی دیگری تداخل دارد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد سال مالی"
     *     )
     * )
     */
    public function store(FiscalYearRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $fiscalYear = $this->fiscalYearService->create($validatedData);

            return $this->successResponse($fiscalYear, 'سال مالی با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد سال مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year/{id}",
     *     summary="نمایش اطلاعات یک سال مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سال مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات سال مالی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات سال مالی"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $fiscalYear = $this->fiscalYearService->find($id);
            
            if (!$fiscalYear) {
                return $this->errorResponse('سال مالی مورد نظر یافت نشد', 404);
            }

            // دریافت اطلاعات تکمیلی
            $details = $this->fiscalYearService->getDetails($id);

            $data = [
                'fiscal_year' => $fiscalYear,
                'details' => $details
            ];

            return $this->successResponse($data, 'اطلاعات سال مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات سال مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/fiscal-year/{id}",
     *     summary="به‌روزرسانی سال مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سال مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=100, example="سال مالی ۱۴۰۳", description="نام سال مالی"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-03-21", description="تاریخ شروع"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-03-20", description="تاریخ پایان"),
     *             @OA\Property(property="status", type="string", enum={"open", "closed", "pending", "locked"}, example="open", description="وضعیت سال مالی"),
     *             @OA\Property(property="is_default", type="boolean", example=true, description="سال مالی پیش‌فرض")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="تاریخ‌های سال مالی با سال مالی دیگری تداخل دارد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی سال مالی"
     *     )
     * )
     */
    public function update(FiscalYearUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $fiscalYear = $this->fiscalYearService->update($id, $validatedData);

            return $this->successResponse($fiscalYear, 'سال مالی با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی سال مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/fiscal-year/{id}",
     *     summary="حذف سال مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سال مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف سال مالی"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->fiscalYearService->delete($id);

            return $this->successResponse(null, 'سال مالی با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف سال مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year/current",
     *     summary="دریافت سال مالی جاری یک شرکت",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی جاری با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی جاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت سال مالی جاری"
     *     )
     * )
     */
    public function getCurrent(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $fiscalYear = $this->fiscalYearService->getCurrentFiscalYear($request->company_id);

            if (!$fiscalYear) {
                return $this->errorResponse('سال مالی جاری برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($fiscalYear, 'سال مالی جاری با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getCurrent: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت سال مالی جاری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year/default",
     *     summary="دریافت سال مالی پیش‌فرض یک شرکت",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی پیش‌فرض با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی پیش‌فرض یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت سال مالی پیش‌فرض"
     *     )
     * )
     */
    public function getDefault(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $fiscalYear = $this->fiscalYearService->getDefaultFiscalYear($request->company_id);

            if (!$fiscalYear) {
                return $this->errorResponse('سال مالی پیش‌فرض برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($fiscalYear, 'سال مالی پیش‌فرض با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت سال مالی پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/fiscal-year/{id}/open",
     *     summary="باز کردن سال مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سال مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی با موفقیت باز شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="امکان باز کردن سال مالی وجود ندارد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در باز کردن سال مالی"
     *     )
     * )
     */
    public function open($id)
    {
        try {
            $result = $this->fiscalYearService->open($id);

            if (!$result) {
                return $this->errorResponse('امکان باز کردن سال مالی وجود ندارد', 400);
            }

            return $this->successResponse(null, 'سال مالی با موفقیت باز شد');
        } catch (\Exception $ex) {
            Log::error('Error in open: ' . $ex->getMessage());
            return $this->errorResponse('خطا در باز کردن سال مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/fiscal-year/{id}/close",
     *     summary="بستن سال مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سال مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی با موفقیت بسته شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="امکان بستن سال مالی وجود ندارد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در بستن سال مالی"
     *     )
     * )
     */
    public function close($id)
    {
        try {
            $result = $this->fiscalYearService->close($id);

            if (!$result) {
                return $this->errorResponse('امکان بستن سال مالی وجود ندارد', 400);
            }

            return $this->successResponse(null, 'سال مالی با موفقیت بسته شد');
        } catch (\Exception $ex) {
            Log::error('Error in close: ' . $ex->getMessage());
            return $this->errorResponse('خطا در بستن سال مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/fiscal-year/{id}/lock",
     *     summary="قفل کردن سال مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سال مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lock_date",
     *         in="query",
     *         description="تاریخ قفل شدن (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی با موفقیت قفل شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="امکان قفل کردن سال مالی وجود ندارد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در قفل کردن سال مالی"
     *     )
     * )
     */
    public function lock(Request $request, $id)
    {
        try {
            $validator = validator($request->all(), [
                'lock_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $result = $this->fiscalYearService->lock($id, $request->lock_date);

            if (!$result) {
                return $this->errorResponse('امکان قفل کردن سال مالی وجود ندارد', 400);
            }

            return $this->successResponse(null, 'سال مالی با موفقیت قفل شد');
        } catch (\Exception $ex) {
            Log::error('Error in lock: ' . $ex->getMessage());
            return $this->errorResponse('خطا در قفل کردن سال مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/fiscal-year/{id}/set-default",
     *     summary="تنظیم سال مالی به عنوان پیش‌فرض",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سال مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی با موفقیت به عنوان پیش‌فرض تنظیم شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تنظیم سال مالی پیش‌فرض"
     *     )
     * )
     */
    public function setDefault($id)
    {
        try {
            $result = $this->fiscalYearService->setAsDefault($id);

            if (!$result) {
                return $this->errorResponse('امکان تنظیم سال مالی به عنوان پیش‌فرض وجود ندارد', 400);
            }

            return $this->successResponse(null, 'سال مالی با موفقیت به عنوان پیش‌فرض تنظیم شد');
        } catch (\Exception $ex) {
            Log::error('Error in setDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تنظیم سال مالی پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year/statistics",
     *     summary="دریافت آمار سال‌های مالی",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار سال‌های مالی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار سال‌های مالی"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $statistics = $this->fiscalYearService->getStatistics($request->company_id);

            return $this->successResponse($statistics, 'آمار سال‌های مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار سال‌های مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year/list",
     *     summary="دریافت لیست سال‌های مالی برای انتخاب (drop-down)",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="فقط سال‌های مالی فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست سال‌های مالی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست سال‌های مالی"
     *     )
     * )
     */
    public function getList(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'only_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $onlyActive = $request->get('only_active', false);
            $list = $this->fiscalYearService->getList($request->company_id, $onlyActive);

            return $this->successResponse($list, 'لیست سال‌های مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست سال‌های مالی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year/statuses",
     *     summary="دریافت لیست وضعیت‌های معتبر سال مالی",
     *     tags={"FiscalYear"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست وضعیت‌ها با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getStatuses()
    {
        try {
            $statuses = \Modules\Accounting\Entities\FiscalYear::$statusLabels;
            return $this->successResponse($statuses, 'لیست وضعیت‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getStatuses: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست وضعیت‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/fiscal-year/by-date",
     *     summary="دریافت سال مالی بر اساس تاریخ مشخص",
     *     tags={"FiscalYear"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="تاریخ مورد نظر (فرمت: Y-m-d)",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سال مالی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سال مالی برای تاریخ مشخص یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت سال مالی"
     *     )
     * )
     */
    public function getByDate(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $fiscalYear = $this->fiscalYearService->getByDate(
                $request->company_id,
                $request->date
            );

            if (!$fiscalYear) {
                return $this->errorResponse('سال مالی برای تاریخ مشخص یافت نشد', 404);
            }

            return $this->successResponse($fiscalYear, 'سال مالی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByDate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت سال مالی: ' . $ex->getMessage(), 500);
        }
    }
}