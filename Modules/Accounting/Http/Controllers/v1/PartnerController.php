<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\PartnerService;
use Modules\Accounting\Http\Requests\Partner\PartnerRequest;
use Modules\Accounting\Http\Requests\Partner\PartnerUpdateRequest;

class PartnerController extends BaseController
{
    protected $partnerService;

    public function __construct(PartnerService $partnerService)
    {
        $this->partnerService = $partnerService;
    }

    /**
     * @OA\Get(
     *     path="/partner",
     *     summary="نمایش لیست طرف‌های حساب",
     *     tags={"Partner"},
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
     *         description="جستجو بر اساس نام، کد، ایمیل یا تلفن",
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
     *         name="partner_type",
     *         in="query",
     *         description="نوع طرف حساب",
     *         required=false,
     *         @OA\Schema(type="string", enum={"customer", "vendor", "employee", "bank", "government", "other"})
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="وضعیت فعال/غیرفعال",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="شهر",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست طرف‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست طرف‌های حساب"
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
                'partner_type',
                'is_active',
                'city'
            ]);

            // حذف فیلترهای خالی
            $filters = array_filter($filters, function($value) {
                return !is_null($value) && $value !== '';
            });

            $partners = $this->partnerService->getPaginate($perPage, $filters);

            return $this->successResponse($partners, 'لیست طرف‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست طرف‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/partner",
     *     summary="ایجاد طرف حساب جدید",
     *     tags={"Partner"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "name", "partner_type"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="CUS00001", description="کد طرف حساب"),
     *             @OA\Property(property="partner_type", type="string", enum={"customer", "vendor", "employee", "bank", "government", "other"}, example="customer", description="نوع طرف حساب"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="شرکت الف", description="نام"),
     *             @OA\Property(property="legal_name", type="string", maxLength=100, example="شرکت الف با مسئولیت محدود", description="نام حقوقی"),
     *             @OA\Property(property="display_name", type="string", maxLength=100, example="الف", description="نام نمایشی"),
     *             @OA\Property(property="tax_number", type="string", maxLength=50, example="1234567890", description="شماره مالیاتی"),
     *             @OA\Property(property="registration_number", type="string", maxLength=50, example="987654321", description="شماره ثبت"),
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="state", type="string", maxLength=100, example="تهران", description="استان"),
     *             @OA\Property(property="city", type="string", maxLength=100, example="تهران", description="شهر"),
     *             @OA\Property(property="address", type="string", example="خیابان آزادی، پلاک ۱۲", description="آدرس"),
     *             @OA\Property(property="postal_code", type="string", maxLength=20, example="1234567890", description="کد پستی"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="02112345678", description="تلفن"),
     *             @OA\Property(property="mobile", type="string", maxLength=20, example="09121234567", description="موبایل"),
     *             @OA\Property(property="email", type="string", maxLength=100, example="info@example.com", description="ایمیل"),
     *             @OA\Property(property="website", type="string", maxLength=100, example="www.example.com", description="وب‌سایت"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="credit_limit", type="number", format="float", example=10000000.00, description="سقف اعتباری"),
     *             @OA\Property(property="payment_term_id", type="integer", example=1, description="شناسه شرایط پرداخت"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="طرف حساب با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="کد یا ایمیل تکراری است"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد طرف حساب"
     *     )
     * )
     */
    public function store(PartnerRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $partner = $this->partnerService->create($validatedData);

            return $this->successResponse($partner, 'طرف حساب با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/{id}",
     *     summary="نمایش اطلاعات یک طرف حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه طرف حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات طرف حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات طرف حساب"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $partner = $this->partnerService->find($id);
            
            if (!$partner) {
                return $this->errorResponse('طرف حساب مورد نظر یافت نشد', 404);
            }

            // دریافت اطلاعات تکمیلی
            $details = $this->partnerService->getDetails($id);

            $data = [
                'partner' => $partner,
                'details' => $details
            ];

            return $this->successResponse($data, 'اطلاعات طرف حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/partner/{id}",
     *     summary="به‌روزرسانی طرف حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه طرف حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="CUS00001", description="کد طرف حساب"),
     *             @OA\Property(property="partner_type", type="string", enum={"customer", "vendor", "employee", "bank", "government", "other"}, example="customer", description="نوع طرف حساب"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="شرکت الف", description="نام"),
     *             @OA\Property(property="legal_name", type="string", maxLength=100, example="شرکت الف با مسئولیت محدود", description="نام حقوقی"),
     *             @OA\Property(property="display_name", type="string", maxLength=100, example="الف", description="نام نمایشی"),
     *             @OA\Property(property="tax_number", type="string", maxLength=50, example="1234567890", description="شماره مالیاتی"),
     *             @OA\Property(property="registration_number", type="string", maxLength=50, example="987654321", description="شماره ثبت"),
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="state", type="string", maxLength=100, example="تهران", description="استان"),
     *             @OA\Property(property="city", type="string", maxLength=100, example="تهران", description="شهر"),
     *             @OA\Property(property="address", type="string", example="خیابان آزادی، پلاک ۱۲", description="آدرس"),
     *             @OA\Property(property="postal_code", type="string", maxLength=20, example="1234567890", description="کد پستی"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="02112345678", description="تلفن"),
     *             @OA\Property(property="mobile", type="string", maxLength=20, example="09121234567", description="موبایل"),
     *             @OA\Property(property="email", type="string", maxLength=100, example="info@example.com", description="ایمیل"),
     *             @OA\Property(property="website", type="string", maxLength=100, example="www.example.com", description="وب‌سایت"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="credit_limit", type="number", format="float", example=10000000.00, description="سقف اعتباری"),
     *             @OA\Property(property="payment_term_id", type="integer", example=1, description="شناسه شرایط پرداخت"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="طرف حساب با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="کد یا ایمیل تکراری است"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی طرف حساب"
     *     )
     * )
     */
    public function update(PartnerUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $partner = $this->partnerService->update($id, $validatedData);

            return $this->successResponse($partner, 'طرف حساب با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/partner/{id}",
     *     summary="حذف طرف حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه طرف حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="طرف حساب با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف طرف حساب"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->partnerService->delete($id);

            return $this->successResponse(null, 'طرف حساب با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/by-type/{type}",
     *     summary="دریافت طرف‌های حساب بر اساس نوع",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         description="نوع طرف حساب",
     *         required=true,
     *         @OA\Schema(type="string", enum={"customer", "vendor", "employee", "bank", "government", "other"})
     *     ),
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
     *         description="فقط طرف‌های حساب فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست طرف‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست طرف‌های حساب"
     *     )
     * )
     */
    public function getByType(Request $request, $type)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'only_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $onlyActive = $request->get('only_active', true);
            $partners = $this->partnerService->getByType($request->company_id, $type, $onlyActive);

            if ($partners->isEmpty()) {
                return $this->errorResponse('هیچ طرف حسابی برای این نوع یافت نشد', 404);
            }

            return $this->successResponse($partners, 'لیست طرف‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByType: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست طرف‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/by-code/{code}",
     *     summary="دریافت طرف حساب بر اساس کد",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="کد طرف حساب",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات طرف حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات طرف حساب"
     *     )
     * )
     */
    public function getByCode(Request $request, $code)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $partner = $this->partnerService->getByCode($request->company_id, $code);

            if (!$partner) {
                return $this->errorResponse('طرف حساب با این کد یافت نشد', 404);
            }

            return $this->successResponse($partner, 'اطلاعات طرف حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCode: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/partner/{id}/activate",
     *     summary="فعال کردن طرف حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه طرف حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="طرف حساب با موفقیت فعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در فعال کردن طرف حساب"
     *     )
     * )
     */
    public function activate($id)
    {
        try {
            $this->partnerService->activate($id);

            return $this->successResponse(null, 'طرف حساب با موفقیت فعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in activate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در فعال کردن طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/partner/{id}/deactivate",
     *     summary="غیرفعال کردن طرف حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه طرف حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="طرف حساب با موفقیت غیرفعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در غیرفعال کردن طرف حساب"
     *     )
     * )
     */
    public function deactivate($id)
    {
        try {
            $this->partnerService->deactivate($id);

            return $this->successResponse(null, 'طرف حساب با موفقیت غیرفعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in deactivate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در غیرفعال کردن طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/balance/{id}",
     *     summary="دریافت مانده حساب طرف حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه طرف حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مانده حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت مانده حساب"
     *     )
     * )
     */
    public function getBalance($id)
    {
        try {
            $balance = $this->partnerService->getBalance($id);

            return $this->successResponse(['balance' => $balance], 'مانده حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getBalance: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت مانده حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/statistics",
     *     summary="دریافت آمار طرف‌های حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار طرف‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار طرف‌های حساب"
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

            $statistics = $this->partnerService->getStatistics($request->company_id);

            return $this->successResponse($statistics, 'آمار طرف‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار طرف‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/list",
     *     summary="دریافت لیست طرف‌های حساب برای انتخاب (drop-down)",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="partner_type",
     *         in="query",
     *         description="نوع طرف حساب (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"customer", "vendor", "employee", "bank", "government", "other"})
     *     ),
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="فقط طرف‌های حساب فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="with_code",
     *         in="query",
     *         description="نمایش کد در کنار نام",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست طرف‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست طرف‌های حساب"
     *     )
     * )
     */
    public function getList(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'partner_type' => 'nullable|string|in:' . implode(',', \Modules\Accounting\Entities\Partner::$types),
                'only_active' => 'nullable|boolean',
                'with_code' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $onlyActive = $request->get('only_active', true);
            $withCode = $request->get('with_code', false);

            $list = $this->partnerService->getList(
                $request->company_id,
                $request->partner_type,
                $onlyActive,
                $withCode
            );

            return $this->successResponse($list, 'لیست طرف‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست طرف‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/types",
     *     summary="دریافت لیست انواع طرف حساب",
     *     tags={"Partner"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع طرف حساب با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getTypes()
    {
        try {
            $types = \Modules\Accounting\Entities\Partner::$typeLabels;
            return $this->successResponse($types, 'لیست انواع طرف حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTypes: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/top-customers",
     *     summary="دریافت مشتریان برتر",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="تعداد نتایج",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مشتریان برتر با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت مشتریان برتر"
     *     )
     * )
     */
    public function getTopCustomers(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'limit' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $limit = $request->get('limit', 10);
            $customers = $this->partnerService->getTopCustomers($request->company_id, $limit);

            return $this->successResponse($customers, 'مشتریان برتر با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTopCustomers: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت مشتریان برتر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/top-vendors",
     *     summary="دریافت تامین‌کنندگان برتر",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="تعداد نتایج",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تامین‌کنندگان برتر با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت تامین‌کنندگان برتر"
     *     )
     * )
     */
    public function getTopVendors(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'limit' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $limit = $request->get('limit', 10);
            $vendors = $this->partnerService->getTopVendors($request->company_id, $limit);

            return $this->successResponse($vendors, 'تامین‌کنندگان برتر با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTopVendors: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت تامین‌کنندگان برتر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/partner/generate-code",
     *     summary="تولید خودکار کد طرف حساب",
     *     tags={"Partner"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="partner_type",
     *         in="query",
     *         description="نوع طرف حساب",
     *         required=true,
     *         @OA\Schema(type="string", enum={"customer", "vendor", "employee", "bank", "government", "other"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="کد با موفقیت تولید شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تولید کد"
     *     )
     * )
     */
    public function generateCode(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'partner_type' => 'required|string|in:' . implode(',', \Modules\Accounting\Entities\Partner::$types),
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $code = $this->partnerService->generateCode(
                $request->company_id,
                $request->partner_type
            );

            return $this->successResponse(['code' => $code], 'کد با موفقیت تولید شد');
        } catch (\Exception $ex) {
            Log::error('Error in generateCode: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تولید کد: ' . $ex->getMessage(), 500);
        }
    }
}