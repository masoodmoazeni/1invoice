<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\TaxService;
use Modules\Accounting\Http\Requests\Tax\TaxRequest;
use Modules\Accounting\Http\Requests\Tax\TaxUpdateRequest;

class TaxController extends BaseController
{
    protected $taxService;

    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * @OA\Get(
     *     path="/tax",
     *     summary="نمایش لیست مالیات‌ها",
     *     tags={"Tax"},
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
     *         description="جستجو بر اساس نام یا کد مالیات",
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
     *         name="country_id",
     *         in="query",
     *         description="شناسه کشور (اختیاری)",
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
     *     @OA\Parameter(
     *         name="tax_kind",
     *         in="query",
     *         description="نوع مالیات (percentage/fixed)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"percentage", "fixed"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مالیات‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مالیات‌ها"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search', null);
            $companyId = $request->get('company_id', null);
            $countryId = $request->get('country_id', null);
            $status = $request->get('status', null);
            $taxKind = $request->get('tax_kind', null);

            $filters = [];
            if ($search) {
                $filters['search'] = $search;
            }
            if ($companyId) {
                $filters['company_id'] = $companyId;
            }
            if ($countryId) {
                $filters['country_id'] = $countryId;
            }
            if ($status) {
                $filters['status'] = $status;
            }
            if ($taxKind) {
                $filters['tax_kind'] = $taxKind;
            }

            $taxes = $this->taxService->getPaginate($perPage, $filters);

            return $this->successResponse($taxes, 'لیست مالیات‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مالیات‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax",
     *     summary="ایجاد مالیات جدید",
     *     tags={"Tax"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "country_id", "code", "name", "rate", "account_id"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="VAT-09", description="کد مالیات (منحصر به فرد برای هر شرکت)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مالیات بر ارزش افزوده", description="نام مالیات"),
     *             @OA\Property(property="description", type="string", nullable=true, example="مالیات بر ارزش افزوده ۹ درصد", description="توضیحات"),
     *             @OA\Property(property="rate", type="number", format="float", example=9.00, description="نرخ مالیات"),
     *             @OA\Property(property="tax_kind", type="string", enum={"percentage", "fixed"}, example="percentage", description="نوع مالیات"),
     *             @OA\Property(property="calculation_method", type="string", enum={"before_discount", "after_discount", "exclusive"}, example="before_discount", description="روش محاسبه"),
     *             @OA\Property(property="account_id", type="integer", example=1, description="شناسه حساب مالیاتی"),
     *             @OA\Property(property="is_inclusive", type="boolean", example=false, description="مالیات درون‌زا"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="مالیات پیش‌فرض"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="مالیات با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد مالیات"
     *     )
     * )
     */
    public function store(TaxRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $tax = $this->taxService->create($validatedData);

            return $this->successResponse($tax, 'مالیات با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد مالیات: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax/{id}",
     *     summary="نمایش اطلاعات یک مالیات",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات مالیات با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات مالیات"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $tax = $this->taxService->find($id);
            
            if (!$tax) {
                return $this->errorResponse('مالیات مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($tax, 'اطلاعات مالیات با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات مالیات: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/tax/{id}",
     *     summary="به‌روزرسانی مالیات",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="VAT-09", description="کد مالیات"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مالیات بر ارزش افزوده", description="نام مالیات"),
     *             @OA\Property(property="description", type="string", nullable=true, description="توضیحات"),
     *             @OA\Property(property="rate", type="number", format="float", example=9.00, description="نرخ مالیات"),
     *             @OA\Property(property="tax_kind", type="string", enum={"percentage", "fixed"}, description="نوع مالیات"),
     *             @OA\Property(property="calculation_method", type="string", enum={"before_discount", "after_discount", "exclusive"}, description="روش محاسبه"),
     *             @OA\Property(property="account_id", type="integer", example=1, description="شناسه حساب مالیاتی"),
     *             @OA\Property(property="is_inclusive", type="boolean", description="مالیات درون‌زا"),
     *             @OA\Property(property="is_default", type="boolean", description="مالیات پیش‌فرض"),
     *             @OA\Property(property="is_active", type="boolean", description="فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی مالیات"
     *     )
     * )
     */
    public function update(TaxUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $tax = $this->taxService->update($id, $validatedData);

            return $this->successResponse($tax, 'مالیات با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی مالیات: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/tax/{id}",
     *     summary="حذف مالیات",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف مالیات"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->taxService->delete($id);

            return $this->successResponse(null, 'مالیات با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف مالیات: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax/by-company/{company_id}",
     *     summary="دریافت مالیات‌های یک شرکت",
     *     tags={"Tax"},
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
     *         description="فقط مالیات‌های فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مالیات‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرکت یافت نشد یا مالیاتی وجود ندارد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مالیات‌ها"
     *     )
     * )
     */
    public function getByCompany($company_id, Request $request)
    {
        try {
            $onlyActive = $request->get('only_active', true);
            $taxes = $this->taxService->getByCompany($company_id, $onlyActive);
            
            if ($taxes->isEmpty()) {
                return $this->errorResponse('هیچ مالیاتی برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($taxes, 'لیست مالیات‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCompany: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مالیات‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax/by-country/{country_id}",
     *     summary="دریافت مالیات‌های یک کشور",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="country_id",
     *         in="path",
     *         description="شناسه کشور",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مالیات‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="کشور یافت نشد یا مالیاتی وجود ندارد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مالیات‌ها"
     *     )
     * )
     */
    public function getByCountry($country_id)
    {
        try {
            $taxes = $this->taxService->getByCountry($country_id);
            
            if ($taxes->isEmpty()) {
                return $this->errorResponse('هیچ مالیاتی برای این کشور یافت نشد', 404);
            }

            return $this->successResponse($taxes, 'لیست مالیات‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCountry: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مالیات‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax/statistics",
     *     summary="دریافت آمار مالیات‌ها",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار مالیات‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار مالیات‌ها"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id', null);
            $statistics = $this->taxService->getStatistics($companyId);
            
            return $this->successResponse($statistics, 'آمار مالیات‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار مالیات‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax/list",
     *     summary="دریافت لیست ساده مالیات‌ها برای استفاده در dropdown",
     *     tags={"Tax"},
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
     *         description="فقط مالیات‌های فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست ساده مالیات‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مالیات‌ها"
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
            $list = $this->taxService->getList($companyId, $onlyActive);
            
            return $this->successResponse($list, 'لیست مالیات‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مالیات‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax/default",
     *     summary="دریافت مالیات پیش‌فرض شرکت",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات پیش‌فرض با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات پیش‌فرض یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت مالیات پیش‌فرض"
     *     )
     * )
     */
    public function getDefaultTax(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $defaultTax = $this->taxService->getDefaultTax($companyId);
            
            if (!$defaultTax) {
                return $this->errorResponse('مالیات پیش‌فرضی یافت نشد', 404);
            }

            return $this->successResponse($defaultTax, 'مالیات پیش‌فرض با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDefaultTax: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت مالیات پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax/{id}/set-default",
     *     summary="تنظیم مالیات به‌عنوان پیش‌فرض",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات با موفقیت به‌عنوان پیش‌فرض تنظیم شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تنظیم مالیات پیش‌فرض"
     *     )
     * )
     */
    public function setDefault($id)
    {
        try {
            $tax = $this->taxService->setAsDefault($id);
            
            return $this->successResponse($tax, 'مالیات با موفقیت به‌عنوان پیش‌فرض تنظیم شد');
        } catch (\Exception $ex) {
            Log::error('Error in setDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تنظیم مالیات پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax/{id}/activate",
     *     summary="فعال کردن مالیات",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات با موفقیت فعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در فعال کردن مالیات"
     *     )
     * )
     */
    public function activate($id)
    {
        try {
            $tax = $this->taxService->activate($id);
            
            return $this->successResponse($tax, 'مالیات با موفقیت فعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in activate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در فعال کردن مالیات: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax/{id}/deactivate",
     *     summary="غیرفعال کردن مالیات",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات با موفقیت غیرفعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در غیرفعال کردن مالیات"
     *     )
     * )
     */
    public function deactivate($id)
    {
        try {
            $tax = $this->taxService->deactivate($id);
            
            return $this->successResponse($tax, 'مالیات با موفقیت غیرفعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in deactivate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در غیرفعال کردن مالیات: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax/calculate",
     *     summary="محاسبه مالیات برای یک مبلغ",
     *     tags={"Tax"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "amount"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="amount", type="number", format="float", example=1000000, description="مبلغ پایه"),
     *             @OA\Property(property="discount", type="number", format="float", example=100000, description="مبلغ تخفیف"),
     *             @OA\Property(property="tax_ids", type="array", @OA\Items(type="integer"), description="شناسه مالیات‌های خاص (اختیاری)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="محاسبه مالیات با موفقیت انجام شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در محاسبه مالیات"
     *     )
     * )
     */
    public function calculateTax(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'amount' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tax_ids' => 'nullable|array',
                'tax_ids.*' => 'integer|exists:taxes,id',
            ]);

            $result = $this->taxService->calculateTaxes(
                $validatedData['company_id'],
                $validatedData['amount'],
                $validatedData['discount'] ?? 0,
                $validatedData['tax_ids'] ?? null
            );

            return $this->successResponse($result, 'محاسبه مالیات با موفقیت انجام شد');
        } catch (\Exception $ex) {
            Log::error('Error in calculateTax: ' . $ex->getMessage());
            return $this->errorResponse('خطا در محاسبه مالیات: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax/{id}/full-info",
     *     summary="دریافت اطلاعات کامل یک مالیات",
     *     tags={"Tax"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات کامل مالیات با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات کامل مالیات"
     *     )
     * )
     */
    public function getFullInfo($id)
    {
        try {
            $fullInfo = $this->taxService->getFullInfo($id);
            
            if (!$fullInfo) {
                return $this->errorResponse('مالیات مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($fullInfo, 'اطلاعات کامل مالیات با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getFullInfo: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات کامل مالیات: ' . $ex->getMessage(), 500);
        }
    }
}