<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\AccountService;
use Modules\Accounting\Http\Requests\Account\AccountRequest;
use Modules\Accounting\Http\Requests\Account\AccountUpdateRequest;

class AccountController extends BaseController
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * @OA\Get(
     *     path="/account",
     *     summary="نمایش لیست حساب‌ها",
     *     tags={"Account"},
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
     *         description="جستجو بر اساس نام یا کد حساب",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست حساب‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌ها"
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

            $accounts = $this->accountService->getPaginate($perPage, $filters);

            return $this->successResponse($accounts, 'لیست حساب‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/account",
     *     summary="ایجاد حساب جدید",
     *     tags={"Account"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "account_code", "account_name", "account_category"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="account_code", type="string", maxLength=50, example="1.1.1", description="کد حساب (ساختار سلسله‌مراتبی)"),
     *             @OA\Property(property="account_name", type="string", maxLength=100, example="صندوق", description="نام حساب"),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null, description="شناسه حساب والد"),
     *             @OA\Property(property="account_category", type="string", enum={"asset","liability","equity","revenue","expense"}, example="asset", description="دسته‌بندی حساب"),
     *             @OA\Property(property="account_type", type="string", enum={"header","detail"}, default="detail", example="detail", description="نوع حساب (کل یا جزئی)"),
     *             @OA\Property(property="normal_balance", type="string", enum={"debit","credit"}, default="debit", example="debit", description="مانده عادی (بدهکار یا بستانکار)"),
     *             @OA\Property(property="currency_id", type="integer", nullable=true, example=1, description="شناسه ارز"),
     *             @OA\Property(property="allow_posting", type="boolean", default=true, example=true, description="مجاز به ثبت سند"),
     *             @OA\Property(property="is_system", type="boolean", default=false, example=false, description="حساب سیستمی"),
     *             @OA\Property(property="is_active", type="boolean", default=true, example=true, description="وضعیت فعال"),
     *             @OA\Property(property="sort_order", type="integer", default=0, example=0, description="ترتیب نمایش")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="حساب با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد حساب"
     *     )
     * )
     */
    public function create(AccountRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $account = $this->accountService->create($validatedData);

            return $this->successResponse($account, 'حساب با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in create: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/{id}",
     *     summary="نمایش اطلاعات یک حساب",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات حساب"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $account = $this->accountService->find($id);
            
            if (!$account) {
                return $this->errorResponse('حساب مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($account, 'اطلاعات حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/account/{id}",
     *     summary="به‌روزرسانی حساب",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="account_code", type="string", maxLength=50, example="1.1.1", description="کد حساب"),
     *             @OA\Property(property="account_name", type="string", maxLength=100, example="صندوق", description="نام حساب"),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null, description="شناسه حساب والد"),
     *             @OA\Property(property="account_category", type="string", enum={"asset","liability","equity","revenue","expense"}, example="asset", description="دسته‌بندی حساب"),
     *             @OA\Property(property="account_type", type="string", enum={"header","detail"}, example="detail", description="نوع حساب"),
     *             @OA\Property(property="normal_balance", type="string", enum={"debit","credit"}, example="debit", description="مانده عادی"),
     *             @OA\Property(property="currency_id", type="integer", nullable=true, example=1, description="شناسه ارز"),
     *             @OA\Property(property="allow_posting", type="boolean", example=true, description="مجاز به ثبت سند"),
     *             @OA\Property(property="is_system", type="boolean", example=false, description="حساب سیستمی"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال"),
     *             @OA\Property(property="sort_order", type="integer", example=0, description="ترتیب نمایش")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="حساب با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی حساب"
     *     )
     * )
     */
    public function update(AccountUpdateRequest $request, $id)
    {
        try {
            
            $validatedData = $request->validated();
            $account = $this->accountService->update($id, $validatedData);

            return $this->successResponse($account, 'حساب با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/account/{id}",
     *     summary="حذف حساب",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="حساب با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف حساب"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->accountService->delete($id);

            return $this->successResponse(null, 'حساب با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/account/{id}/toggle-status",
     *     summary="تغییر وضعیت فعال/غیرفعال حساب",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="وضعیت حساب با موفقیت تغییر یافت"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تغییر وضعیت حساب"
     *     )
     * )
     */
    public function toggleStatus($id)
    {
        try {
            
            $account = $this->accountService->toggleStatus($id);

            return $this->successResponse($account, 'وضعیت حساب با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in toggleStatus: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/account/{id}/toggle-posting",
     *     summary="تغییر وضعیت مجاز به ثبت سند",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه حساب",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="وضعیت ثبت سند حساب با موفقیت تغییر یافت"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تغییر وضعیت ثبت سند"
     *     )
     * )
     */
    public function togglePosting($id)
    {
        try {
            

            $account = $this->accountService->togglePosting($id);

            return $this->successResponse($account, 'وضعیت ثبت سند حساب با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in togglePosting: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت ثبت سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/active",
     *     summary="دریافت تمام حساب‌های فعال",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست حساب‌های فعال با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌های فعال"
     *     )
     * )
     */
    public function getActive(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $accounts = $this->accountService->getActiveAccounts($companyId);
            return $this->successResponse($accounts, 'لیست حساب‌های فعال با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getActive: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌های فعال: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/by-category/{category}",
     *     summary="دریافت حساب‌ها بر اساس دسته‌بندی",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="دسته‌بندی حساب",
     *         required=true,
     *         @OA\Schema(type="string", enum={"asset","liability","equity","revenue","expense"})
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
     *         description="لیست حساب‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="دسته‌بندی حساب نامعتبر است"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌ها"
     *     )
     * )
     */
    public function getByCategory(Request $request, $category)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            // اعتبارسنجی category
            $validCategories = ['asset', 'liability', 'equity', 'revenue', 'expense'];
            if (!in_array($category, $validCategories)) {
                return $this->errorResponse('دسته‌بندی حساب نامعتبر است', 422);
            }

            $accounts = $this->accountService->getByCategory($companyId, $category);
            return $this->successResponse($accounts, 'لیست حساب‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCategory: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/tree",
     *     summary="دریافت درخت حساب‌ها",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="درخت حساب‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت درخت حساب‌ها"
     *     )
     * )
     */
    public function getTree(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $tree = $this->accountService->getTree($companyId);
            return $this->successResponse($tree, 'درخت حساب‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTree: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت درخت حساب‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/children/{parent_id}",
     *     summary="دریافت حساب‌های فرزند یک حساب",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="path",
     *         description="شناسه حساب والد",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         description="لیست حساب‌های فرزند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب والد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌های فرزند"
     *     )
     * )
     */
    public function getChildren(Request $request, $parent_id)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            // بررسی وجود حساب والد
            if (!$this->accountService->exists($parent_id)) {
                return $this->errorResponse('حساب والد مورد نظر یافت نشد', 404);
            }

            $children = $this->accountService->getChildren($companyId, $parent_id);
            return $this->successResponse($children, 'لیست حساب‌های فرزند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getChildren: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌های فرزند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/statistics",
     *     summary="دریافت آمار حساب‌ها",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار حساب‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار حساب‌ها"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $statistics = $this->accountService->getStatistics($companyId);
            return $this->successResponse($statistics, 'آمار حساب‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار حساب‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/headers",
     *     summary="دریافت حساب‌های کل (سرگروه)",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست حساب‌های کل با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌های کل"
     *     )
     * )
     */
    public function getHeaders(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $headers = $this->accountService->getHeaders($companyId);
            return $this->successResponse($headers, 'لیست حساب‌های کل با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getHeaders: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌های کل: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account/details",
     *     summary="دریافت حساب‌های جزئی",
     *     tags={"Account"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست حساب‌های جزئی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست حساب‌های جزئی"
     *     )
     * )
     */
    public function getDetails(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $details = $this->accountService->getDetails($companyId);
            return $this->successResponse($details, 'لیست حساب‌های جزئی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDetails: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست حساب‌های جزئی: ' . $ex->getMessage(), 500);
        }
    }
}