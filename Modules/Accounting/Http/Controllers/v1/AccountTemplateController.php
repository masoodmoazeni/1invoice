<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\AccountTemplateService;
use Modules\Accounting\Http\Requests\AccountTemplate\AccountTemplateRequest;
use Modules\Accounting\Http\Requests\AccountTemplate\AccountTemplateUpdateRequest;

class AccountTemplateController extends BaseController
{
    protected $accountTemplateService;

    public function __construct(AccountTemplateService $accountTemplateService)
    {
        $this->accountTemplateService = $accountTemplateService;
    }

    /**
     * @OA\Get(
     *     path="/account-template",
     *     summary="نمایش لیست قالب‌های حساب",
     *     tags={"AccountTemplate"},
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
     *         description="جستجو بر اساس نام قالب",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست قالب‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست قالب‌های حساب"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = request()->get('search', null);

            $filters = [];
            if ($search) {
                $filters['search'] = $search;
            }

            $templates = $this->accountTemplateService->getPaginate($perPage, $filters);

            return $this->successResponse($templates, 'لیست قالب‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست قالب‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/account-template",
     *     summary="ایجاد قالب حساب جدید",
     *     tags={"AccountTemplate"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"country_id", "name"},
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="نمودار حساب‌های استاندارد ایران", description="نام قالب"),
     *             @OA\Property(property="version", type="string", maxLength=20, example="1.0.0", description="نسخه قالب")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="قالب حساب با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد قالب حساب"
     *     )
     * )
     */
    public function create(AccountTemplateRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $template = $this->accountTemplateService->create($validatedData);

            return $this->successResponse($template, 'قالب حساب با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in create: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template/{id}",
     *     summary="نمایش اطلاعات یک قالب حساب",
     *     tags={"AccountTemplate"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه قالب حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات قالب حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="قالب حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات قالب حساب"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $template = $this->accountTemplateService->find($id);
            
            if (!$template) {
                return $this->errorResponse('قالب حساب مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($template, 'اطلاعات قالب حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/account-template/{id}",
     *     summary="به‌روزرسانی قالب حساب",
     *     tags={"AccountTemplate"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه قالب حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="نمودار حساب‌های استاندارد ایران", description="نام قالب"),
     *             @OA\Property(property="version", type="string", maxLength=20, example="1.0.0", description="نسخه قالب")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="قالب حساب با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="قالب حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی قالب حساب"
     *     )
     * )
     */
    public function update(AccountTemplateUpdateRequest $request, $id)
    {
        try {

            $validatedData = $request->validated();
            $template = $this->accountTemplateService->update($id, $validatedData);

            return $this->successResponse($template, 'قالب حساب با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/account-template/{id}",
     *     summary="حذف قالب حساب",
     *     tags={"AccountTemplate"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه قالب حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="قالب حساب با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="قالب حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف قالب حساب"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            
            $this->accountTemplateService->delete($id);

            return $this->successResponse(null, 'قالب حساب با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template/by-country/{country_id}",
     *     summary="دریافت قالب‌های حساب بر اساس کشور",
     *     tags={"AccountTemplate"},
     *     @OA\Parameter(
     *         name="country_id",
     *         in="path",
     *         description="شناسه کشور",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست قالب‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="کشور یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست قالب‌های حساب"
     *     )
     * )
     */
    public function getByCountry($country_id)
    {
        try {
            $templates = $this->accountTemplateService->getByCountry($country_id);
            
            if ($templates->isEmpty()) {
                return $this->errorResponse('هیچ قالبی برای این کشور یافت نشد', 404);
            }

            return $this->successResponse($templates, 'لیست قالب‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCountry: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست قالب‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template/statistics",
     *     summary="دریافت آمار قالب‌های حساب",
     *     tags={"AccountTemplate"},
     *     @OA\Response(
     *         response=200,
     *         description="آمار قالب‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار قالب‌های حساب"
     *     )
     * )
     */
    public function statistics()
    {
        try {
            $statistics = $this->accountTemplateService->getStatistics();
            return $this->successResponse($statistics, 'آمار قالب‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار قالب‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template/latest-version",
     *     summary="دریافت آخرین نسخه قالب‌ها",
     *     tags={"AccountTemplate"},
     *     @OA\Parameter(
     *         name="country_id",
     *         in="query",
     *         description="شناسه کشور (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آخرین نسخه قالب‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آخرین نسخه قالب‌ها"
     *     )
     * )
     */
    public function getLatestVersion(Request $request)
    {
        try {
            $countryId = $request->get('country_id');
            $templates = $this->accountTemplateService->getLatestVersion($countryId);
            
            return $this->successResponse($templates, 'آخرین نسخه قالب‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getLatestVersion: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آخرین نسخه قالب‌ها: ' . $ex->getMessage(), 500);
        }
    }
}