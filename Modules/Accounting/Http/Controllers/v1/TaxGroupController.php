<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\TaxGroupService;
use Modules\Accounting\Http\Requests\TaxGroup\TaxGroupRequest;
use Modules\Accounting\Http\Requests\TaxGroup\TaxGroupUpdateRequest;

class TaxGroupController extends BaseController
{
    protected $taxGroupService;

    public function __construct(TaxGroupService $taxGroupService)
    {
        $this->taxGroupService = $taxGroupService;
    }

    /**
     * @OA\Get(
     *     path="/tax-group",
     *     summary="نمایش لیست گروه‌های مالیاتی",
     *     tags={"TaxGroup"},
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
     *         description="جستجو بر اساس نام یا کد گروه مالیاتی",
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
     *     @OA\Response(
     *         response=200,
     *         description="لیست گروه‌های مالیاتی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست گروه‌های مالیاتی"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search', null);
            $companyId = $request->get('company_id', null);

            $filters = [];
            if ($search) {
                $filters['search'] = $search;
            }
            if ($companyId) {
                $filters['company_id'] = $companyId;
            }

            $taxGroups = $this->taxGroupService->getPaginate($perPage, $filters);

            return $this->successResponse($taxGroups, 'لیست گروه‌های مالیاتی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست گروه‌های مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax-group",
     *     summary="ایجاد گروه مالیاتی جدید",
     *     tags={"TaxGroup"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "code", "name"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="TAX-STD-001", description="کد گروه مالیاتی (منحصر به فرد برای هر شرکت)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مالیات‌های استاندارد", description="نام گروه مالیاتی"),
     *             @OA\Property(property="description", type="string", nullable=true, example="گروه مالیاتی استاندارد برای محصولات عادی", description="توضیحات")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="گروه مالیاتی با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد گروه مالیاتی"
     *     )
     * )
     */
    public function store(TaxGroupRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $taxGroup = $this->taxGroupService->create($validatedData);

            return $this->successResponse($taxGroup, 'گروه مالیاتی با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد گروه مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax-group/{id}",
     *     summary="نمایش اطلاعات یک گروه مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه گروه مالیاتی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات گروه مالیاتی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="گروه مالیاتی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات گروه مالیاتی"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $taxGroup = $this->taxGroupService->find($id);
            
            if (!$taxGroup) {
                return $this->errorResponse('گروه مالیاتی مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($taxGroup, 'اطلاعات گروه مالیاتی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات گروه مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/tax-group/{id}",
     *     summary="به‌روزرسانی گروه مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه گروه مالیاتی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="TAX-STD-001", description="کد گروه مالیاتی"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مالیات‌های استاندارد", description="نام گروه مالیاتی"),
     *             @OA\Property(property="description", type="string", nullable=true, example="گروه مالیاتی استاندارد برای محصولات عادی", description="توضیحات")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="گروه مالیاتی با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="گروه مالیاتی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی گروه مالیاتی"
     *     )
     * )
     */
    public function update(TaxGroupUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $taxGroup = $this->taxGroupService->update($id, $validatedData);

            return $this->successResponse($taxGroup, 'گروه مالیاتی با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی گروه مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/tax-group/{id}",
     *     summary="حذف گروه مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه گروه مالیاتی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="گروه مالیاتی با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="گروه مالیاتی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف گروه مالیاتی"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->taxGroupService->delete($id);

            return $this->successResponse(null, 'گروه مالیاتی با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف گروه مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax-group/by-company/{company_id}",
     *     summary="دریافت گروه‌های مالیاتی بر اساس شرکت",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="path",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست گروه‌های مالیاتی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرکت یافت نشد یا گروه مالیاتی وجود ندارد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست گروه‌های مالیاتی"
     *     )
     * )
     */
    public function getByCompany($company_id)
    {
        try {
            $taxGroups = $this->taxGroupService->getByCompany($company_id);
            
            if ($taxGroups->isEmpty()) {
                return $this->errorResponse('هیچ گروه مالیاتی برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($taxGroups, 'لیست گروه‌های مالیاتی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCompany: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست گروه‌های مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax-group/statistics",
     *     summary="دریافت آمار گروه‌های مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار گروه‌های مالیاتی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار گروه‌های مالیاتی"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id', null);
            $statistics = $this->taxGroupService->getStatistics($companyId);
            
            return $this->successResponse($statistics, 'آمار گروه‌های مالیاتی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار گروه‌های مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax-group/list",
     *     summary="دریافت لیست ساده گروه‌های مالیاتی برای استفاده در dropdown",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست ساده گروه‌های مالیاتی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست گروه‌های مالیاتی"
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

            $list = $this->taxGroupService->getList($companyId);
            
            return $this->successResponse($list, 'لیست گروه‌های مالیاتی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست گروه‌های مالیاتی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tax-group/{id}/taxes",
     *     summary="دریافت لیست مالیات‌های یک گروه مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه گروه مالیاتی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مالیات‌های گروه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="گروه مالیاتی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مالیات‌های گروه"
     *     )
     * )
     */
    public function getTaxes($id)
    {
        try {
            $taxes = $this->taxGroupService->getTaxes($id);
            
            if (!$taxes) {
                return $this->errorResponse('گروه مالیاتی مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($taxes, 'لیست مالیات‌های گروه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTaxes: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مالیات‌های گروه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax-group/{id}/add-tax",
     *     summary="اضافه کردن مالیات به گروه مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه گروه مالیاتی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tax_id"},
     *             @OA\Property(property="tax_id", type="integer", example=1, description="شناسه مالیات"),
     *             @OA\Property(property="priority", type="integer", example=1, description="اولویت (عدد بزرگتر = اولویت بیشتر)"),
     *             @OA\Property(property="is_required", type="boolean", example=true, description="الزامی بودن مالیات")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات با موفقیت به گروه اضافه شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="گروه مالیاتی یا مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در اضافه کردن مالیات به گروه"
     *     )
     * )
     */
    public function addTax(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'tax_id' => 'required|integer|exists:taxes,id',
                'priority' => 'nullable|integer|min:0',
                'is_required' => 'nullable|boolean',
            ]);

            $result = $this->taxGroupService->addTax(
                $id, 
                $validatedData['tax_id'], 
                $validatedData['priority'] ?? 0, 
                $validatedData['is_required'] ?? true
            );

            return $this->successResponse($result, 'مالیات با موفقیت به گروه اضافه شد');
        } catch (\Exception $ex) {
            Log::error('Error in addTax: ' . $ex->getMessage());
            return $this->errorResponse('خطا در اضافه کردن مالیات به گروه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/tax-group/{id}/remove-tax/{tax_id}",
     *     summary="حذف مالیات از گروه مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه گروه مالیاتی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="tax_id",
     *         in="path",
     *         description="شناسه مالیات",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مالیات با موفقیت از گروه حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="گروه مالیاتی یا مالیات یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف مالیات از گروه"
     *     )
     * )
     */
    public function removeTax($id, $tax_id)
    {
        try {
            $result = $this->taxGroupService->removeTax($id, $tax_id);

            return $this->successResponse($result, 'مالیات با موفقیت از گروه حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in removeTax: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف مالیات از گروه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tax-group/{id}/duplicate",
     *     summary="کپی کردن گروه مالیاتی",
     *     tags={"TaxGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه گروه مالیاتی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name"},
     *             @OA\Property(property="code", type="string", maxLength=50, example="TAX-STD-001-COP", description="کد جدید برای گروه کپی شده"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="کپی مالیات‌های استاندارد", description="نام جدید برای گروه کپی شده")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="گروه مالیاتی با موفقیت کپی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="گروه مالیاتی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در کپی کردن گروه مالیاتی"
     *     )
     * )
     */
    public function duplicate(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|string|max:50',
                'name' => 'required|string|max:100',
            ]);

            $newGroup = $this->taxGroupService->duplicate(
                $id, 
                $validatedData['code'], 
                $validatedData['name']
            );

            return $this->successResponse($newGroup, 'گروه مالیاتی با موفقیت کپی شد');
        } catch (\Exception $ex) {
            Log::error('Error in duplicate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در کپی کردن گروه مالیاتی: ' . $ex->getMessage(), 500);
        }
    }
}