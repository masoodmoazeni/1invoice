<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\BankAccountService;
use Modules\Accounting\Http\Requests\BankAccount\BankAccountRequest;
use Modules\Accounting\Http\Requests\BankAccount\BankAccountUpdateRequest;

class BankAccountController extends BaseController
{
    protected $bankAccountService;

    public function __construct(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    /**
     * @OA\Get(
     *     path="/bank-account",
     *     summary="نمایش لیست حساب‌های بانکی",
     *     tags={"BankAccount"},
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
     *         description="جستجو بر اساس نام بانک یا شماره حساب",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست حساب‌های بانکی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌های بانکی"
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

            $bankAccounts = $this->bankAccountService->getPaginate($perPage, $filters);

            return $this->successResponse($bankAccounts, 'لیست حساب‌های بانکی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌های بانکی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/bank-account",
     *     summary="ایجاد حساب بانکی جدید",
     *     tags={"BankAccount"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "bank_name", "account_number", "currency_id"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="bank_name", type="string", maxLength=100, example="بانک ملی", description="نام بانک"),
     *             @OA\Property(property="branch_name", type="string", maxLength=100, example="شعبه مرکزی", description="نام شعبه"),
     *             @OA\Property(property="account_number", type="string", maxLength=50, example="1234567890", description="شماره حساب"),
     *             @OA\Property(property="iban", type="string", maxLength=34, example="IR820540101680020000101001", description="شماره شبا"),
     *             @OA\Property(property="swift", type="string", maxLength=11, example="MELIIRTHXXX", description="کد سوئیفت"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="حساب پیش‌فرض"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="حساب بانکی با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد حساب بانکی"
     *     )
     * )
     */
    public function create(BankAccountRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $bankAccount = $this->bankAccountService->create($validatedData);

            return $this->successResponse($bankAccount, 'حساب بانکی با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد حساب بانکی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/bank-account/{id}",
     *     summary="نمایش اطلاعات یک حساب بانکی",
     *     tags={"BankAccount"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب بانکی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات حساب بانکی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب بانکی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات حساب بانکی"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $bankAccount = $this->bankAccountService->find($id);
            
            if (!$bankAccount) {
                return $this->errorResponse('حساب بانکی مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($bankAccount, 'اطلاعات حساب بانکی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات حساب بانکی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/bank-account/{id}",
     *     summary="به‌روزرسانی حساب بانکی",
     *     tags={"BankAccount"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب بانکی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="bank_name", type="string", maxLength=100, example="بانک ملی", description="نام بانک"),
     *             @OA\Property(property="branch_name", type="string", maxLength=100, example="شعبه مرکزی", description="نام شعبه"),
     *             @OA\Property(property="account_number", type="string", maxLength=50, example="1234567890", description="شماره حساب"),
     *             @OA\Property(property="iban", type="string", maxLength=34, example="IR820540101680020000101001", description="شماره شبا"),
     *             @OA\Property(property="swift", type="string", maxLength=11, example="MELIIRTHXXX", description="کد سوئیفت"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="حساب پیش‌فرض"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="حساب بانکی با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب بانکی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی حساب بانکی"
     *     )
     * )
     */
    public function update(BankAccountUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $bankAccount = $this->bankAccountService->update($id, $validatedData);

            return $this->successResponse($bankAccount, 'حساب بانکی با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی حساب بانکی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/bank-account/{id}",
     *     summary="حذف حساب بانکی",
     *     tags={"BankAccount"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب بانکی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="حساب بانکی با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب بانکی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف حساب بانکی"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->bankAccountService->delete($id);
            return $this->successResponse(null, 'حساب بانکی با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف حساب بانکی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/bank-account/by-company/{company_id}",
     *     summary="دریافت حساب‌های بانکی بر اساس شرکت",
     *     tags={"BankAccount"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="path",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست حساب‌های بانکی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرکت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌های بانکی"
     *     )
     * )
     */
    public function getByCompany($company_id)
    {
        try {
            $bankAccounts = $this->bankAccountService->getByCompany($company_id);
            
            if ($bankAccounts->isEmpty()) {
                return $this->errorResponse('هیچ حساب بانکی برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($bankAccounts, 'لیست حساب‌های بانکی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@getByCompany: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌های بانکی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/bank-account/default/{company_id}",
     *     summary="دریافت حساب بانکی پیش‌فرض شرکت",
     *     tags={"BankAccount"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="path",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="حساب بانکی پیش‌فرض با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب بانکی پیش‌فرض یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت حساب بانکی پیش‌فرض"
     *     )
     * )
     */
    public function getDefaultByCompany($company_id)
    {
        try {
            $bankAccount = $this->bankAccountService->getDefaultByCompany($company_id);
            
            if (!$bankAccount) {
                return $this->errorResponse('حساب بانکی پیش‌فرض برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($bankAccount, 'حساب بانکی پیش‌فرض با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@getDefaultByCompany: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت حساب بانکی پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/bank-account/{id}/set-default",
     *     summary="تنظیم حساب بانکی به عنوان پیش‌فرض",
     *     tags={"BankAccount"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب بانکی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="حساب بانکی با موفقیت به عنوان پیش‌فرض تنظیم شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب بانکی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تنظیم حساب بانکی پیش‌فرض"
     *     )
     * )
     */
    public function setDefault($id)
    {
        try {
            $bankAccount = $this->bankAccountService->setDefault($id);
            return $this->successResponse($bankAccount, 'حساب بانکی با موفقیت به عنوان پیش‌فرض تنظیم شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@setDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تنظیم حساب بانکی پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/bank-account/statistics",
     *     summary="دریافت آمار حساب‌های بانکی",
     *     tags={"BankAccount"},
     *     @OA\Response(
     *         response=200,
     *         description="آمار حساب‌های بانکی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار حساب‌های بانکی"
     *     )
     * )
     */
    public function statistics()
    {
        try {
            $statistics = $this->bankAccountService->getStatistics();
            return $this->successResponse($statistics, 'آمار حساب‌های بانکی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BankAccountController@statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار حساب‌های بانکی: ' . $ex->getMessage(), 500);
        }
    }
}