<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\PaymentTermService;
use Modules\Accounting\Http\Requests\PaymentTerm\PaymentTermRequest;
use Modules\Accounting\Http\Requests\PaymentTerm\PaymentTermUpdateRequest;

class PaymentTermController extends BaseController
{
    protected $paymentTermService;

    public function __construct(PaymentTermService $paymentTermService)
    {
        $this->paymentTermService = $paymentTermService;
    }

    /**
     * @OA\Get(
     *     path="/payment-term",
     *     summary="نمایش لیست شرایط پرداخت",
     *     tags={"PaymentTerm"},
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
     *         description="جستجو بر اساس نام یا کد شرط پرداخت",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="وضعیت (active/inactive)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست شرایط پرداخت با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست شرایط پرداخت"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search', null);
            $companyId = $request->get('company_id', null);
            $status = $request->get('status', null);

            $filters = [];
            if ($search) {
                $filters['search'] = $search;
            }
            if ($companyId) {
                $filters['company_id'] = $companyId;
            }
            if ($status) {
                $filters['status'] = $status;
            }

            $paymentTerms = $this->paymentTermService->getPaginate($perPage, $filters);

            return $this->successResponse($paymentTerms, 'لیست شرایط پرداخت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شرایط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/payment-term",
     *     summary="ایجاد شرط پرداخت جدید",
     *     tags={"PaymentTerm"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "code", "name"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="NET30", description="کد شرط پرداخت (منحصر به فرد برای هر شرکت)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="۳۰ روزه", description="نام شرط پرداخت"),
     *             @OA\Property(property="description", type="string", nullable=true, example="پرداخت تا ۳۰ روز پس از تاریخ فاکتور", description="توضیحات"),
     *             @OA\Property(property="due_days", type="integer", example=30, description="تعداد روزهای سررسید"),
     *             @OA\Property(property="is_immediate", type="boolean", example=false, description="پرداخت فوری (نقدی)"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="شرط پیش‌فرض"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="شرط پرداخت با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد شرط پرداخت"
     *     )
     * )
     */
    public function store(PaymentTermRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $paymentTerm = $this->paymentTermService->create($validatedData);

            return $this->successResponse($paymentTerm, 'شرط پرداخت با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد شرط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/{id}",
     *     summary="نمایش اطلاعات یک شرط پرداخت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شرط پرداخت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات شرط پرداخت با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات شرط پرداخت"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $paymentTerm = $this->paymentTermService->find($id);
            
            if (!$paymentTerm) {
                return $this->errorResponse('شرط پرداخت مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($paymentTerm, 'اطلاعات شرط پرداخت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات شرط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/payment-term/{id}",
     *     summary="به‌روزرسانی شرط پرداخت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شرط پرداخت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="NET30", description="کد شرط پرداخت"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="۳۰ روزه", description="نام شرط پرداخت"),
     *             @OA\Property(property="description", type="string", nullable=true, example="پرداخت تا ۳۰ روز پس از تاریخ فاکتور", description="توضیحات"),
     *             @OA\Property(property="due_days", type="integer", example=30, description="تعداد روزهای سررسید"),
     *             @OA\Property(property="is_immediate", type="boolean", example=false, description="پرداخت فوری (نقدی)"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="شرط پیش‌فرض"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرط پرداخت با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی شرط پرداخت"
     *     )
     * )
     */
    public function update(PaymentTermUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $paymentTerm = $this->paymentTermService->update($id, $validatedData);

            return $this->successResponse($paymentTerm, 'شرط پرداخت با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی شرط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/payment-term/{id}",
     *     summary="حذف شرط پرداخت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شرط پرداخت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرط پرداخت با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف شرط پرداخت"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->paymentTermService->delete($id);

            return $this->successResponse(null, 'شرط پرداخت با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف شرط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/by-company/{company_id}",
     *     summary="دریافت شرایط پرداخت بر اساس شرکت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="path",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="فقط شرایط فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست شرایط پرداخت با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرکت یافت نشد یا شرایط پرداختی وجود ندارد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست شرایط پرداخت"
     *     )
     * )
     */
    public function getByCompany($company_id, Request $request)
    {
        try {
            $onlyActive = $request->get('only_active', true);
            $paymentTerms = $this->paymentTermService->getByCompany($company_id, $onlyActive);
            
            if ($paymentTerms->isEmpty()) {
                return $this->errorResponse('هیچ شرط پرداختی برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($paymentTerms, 'لیست شرایط پرداخت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCompany: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شرایط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/statistics",
     *     summary="دریافت آمار شرایط پرداخت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار شرایط پرداخت با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار شرایط پرداخت"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id', null);
            $statistics = $this->paymentTermService->getStatistics($companyId);
            
            return $this->successResponse($statistics, 'آمار شرایط پرداخت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار شرایط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/list",
     *     summary="دریافت لیست ساده شرایط پرداخت برای استفاده در dropdown",
     *     tags={"PaymentTerm"},
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
     *         description="فقط شرایط فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="with_code",
     *         in="query",
     *         description="نمایش با کد",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست ساده شرایط پرداخت با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست شرایط پرداخت"
     *     )
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
            $withCode = $request->get('with_code', false);
            
            if ($withCode) {
                $list = $this->paymentTermService->getListWithCode($companyId, $onlyActive);
            } else {
                $list = $this->paymentTermService->getList($companyId, $onlyActive);
            }
            
            return $this->successResponse($list, 'لیست شرایط پرداخت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شرایط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/grouped-list",
     *     summary="دریافت لیست گروه‌بندی شده شرایط پرداخت",
     *     tags={"PaymentTerm"},
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
     *         description="فقط شرایط فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست گروه‌بندی شده شرایط پرداخت با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست گروه‌بندی شده شرایط پرداخت"
     *     )
     * )
     */
    public function getGroupedList(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $onlyActive = $request->get('only_active', true);
            $groupedList = $this->paymentTermService->getGroupedList($companyId, $onlyActive);
            
            return $this->successResponse($groupedList, 'لیست گروه‌بندی شده شرایط پرداخت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getGroupedList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست گروه‌بندی شده شرایط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/default",
     *     summary="دریافت شرط پرداخت پیش‌فرض شرکت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرط پرداخت پیش‌فرض با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت پیش‌فرض یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت شرط پرداخت پیش‌فرض"
     *     )
     * )
     */
    public function getDefaultTerm(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $defaultTerm = $this->paymentTermService->getDefaultTerm($companyId);
            
            if (!$defaultTerm) {
                return $this->errorResponse('شرط پرداخت پیش‌فرضی یافت نشد', 404);
            }

            return $this->successResponse($defaultTerm, 'شرط پرداخت پیش‌فرض با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDefaultTerm: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت شرط پرداخت پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/immediate",
     *     summary="دریافت شرط پرداخت فوری (نقدی)",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرط پرداخت فوری با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت فوری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت شرط پرداخت فوری"
     *     )
     * )
     */
    public function getImmediateTerm(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $immediateTerm = $this->paymentTermService->getImmediateTerm($companyId);
            
            if (!$immediateTerm) {
                return $this->errorResponse('شرط پرداخت فوری یافت نشد', 404);
            }

            return $this->successResponse($immediateTerm, 'شرط پرداخت فوری با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getImmediateTerm: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت شرط پرداخت فوری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/payment-term/{id}/set-default",
     *     summary="تنظیم شرط پرداخت به‌عنوان پیش‌فرض",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شرط پرداخت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرط پرداخت با موفقیت به‌عنوان پیش‌فرض تنظیم شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تنظیم شرط پرداخت پیش‌فرض"
     *     )
     * )
     */
    public function setDefault($id)
    {
        try {
            $paymentTerm = $this->paymentTermService->setAsDefault($id);
            
            return $this->successResponse($paymentTerm, 'شرط پرداخت با موفقیت به‌عنوان پیش‌فرض تنظیم شد');
        } catch (\Exception $ex) {
            Log::error('Error in setDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تنظیم شرط پرداخت پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/payment-term/{id}/activate",
     *     summary="فعال کردن شرط پرداخت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شرط پرداخت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرط پرداخت با موفقیت فعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در فعال کردن شرط پرداخت"
     *     )
     * )
     */
    public function activate($id)
    {
        try {
            $paymentTerm = $this->paymentTermService->activate($id);
            
            return $this->successResponse($paymentTerm, 'شرط پرداخت با موفقیت فعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in activate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در فعال کردن شرط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/payment-term/{id}/deactivate",
     *     summary="غیرفعال کردن شرط پرداخت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شرط پرداخت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرط پرداخت با موفقیت غیرفعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در غیرفعال کردن شرط پرداخت"
     *     )
     * )
     */
    public function deactivate($id)
    {
        try {
            $paymentTerm = $this->paymentTermService->deactivate($id);
            
            return $this->successResponse($paymentTerm, 'شرط پرداخت با موفقیت غیرفعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in deactivate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در غیرفعال کردن شرط پرداخت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/payment-term/create-defaults/{company_id}",
     *     summary="ایجاد شرایط پرداخت پیش‌فرض برای شرکت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="path",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شرایط پرداخت پیش‌فرض با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد شرایط پرداخت پیش‌فرض"
     *     )
     * )
     */
    public function createDefaultTerms($company_id)
    {
        try {
            $terms = $this->paymentTermService->createDefaultTerms($company_id);
            
            return $this->successResponse($terms, 'شرایط پرداخت پیش‌فرض با موفقیت ایجاد شد');
        } catch (\Exception $ex) {
            Log::error('Error in createDefaultTerms: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد شرایط پرداخت پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/payment-term/{id}/report",
     *     summary="دریافت گزارش کامل از یک شرط پرداخت",
     *     tags={"PaymentTerm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شرط پرداخت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="گزارش شرط پرداخت با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرط پرداخت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت گزارش شرط پرداخت"
     *     )
     * )
     */
    public function getReportInfo($id)
    {
        try {
            $reportInfo = $this->paymentTermService->getReportInfo($id);
            
            if (!$reportInfo) {
                return $this->errorResponse('شرط پرداخت مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($reportInfo, 'گزارش شرط پرداخت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getReportInfo: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت گزارش شرط پرداخت: ' . $ex->getMessage(), 500);
        }
    }
}