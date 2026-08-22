<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\BranchService;

use Modules\Accounting\Http\Requests\Branch\BranchRequest;
use Modules\Accounting\Http\Requests\Branch\BranchUpdateRequest;

class BranchController extends BaseController
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * @OA\Get(
     *     path="/branch",
     *     summary="نمایش لیست شعب",
     *     tags={"Branch"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام، کد، شهر یا آدرس", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="company_id", in="query", description="فیلتر بر اساس شناسه شرکت", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="country_id", in="query", description="فیلتر بر اساس شناسه کشور", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="city", in="query", description="فیلتر بر اساس شهر", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_active", in="query", description="فیلتر بر اساس وضعیت فعال", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="is_default", in="query", description="فیلتر بر اساس شعبه پیش‌فرض", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="has_manager", in="query", description="فیلتر بر اساس داشتن مدیر", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort_by", in="query", description="فیلد مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"id","code","name","city","is_active","is_default","created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="ترتیب مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc")),
     *     @OA\Response(response=200, description="لیست شعب با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست شعب"),
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
            
            if ($request->has('country_id')) {
                $filters['country_id'] = $request->get('country_id');
            }
            
            if ($request->has('city')) {
                $filters['city'] = $request->get('city');
            }
            
            if ($request->has('is_active')) {
                $filters['is_active'] = $request->get('is_active');
            }
            
            if ($request->has('is_default')) {
                $filters['is_default'] = $request->get('is_default');
            }

            $branches = $this->branchService->getPaginate($perPage, $filters);

            return $this->successResponse($branches, 'لیست شعب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شعب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/branch",
     *     summary="ایجاد شعبه جدید",
     *     tags={"Branch"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "name", "country_id", "manager_id"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="BR001", nullable=true, description="کد شعبه (در صورت عدم ارسال، خودکار تولید می‌شود)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="شعبه مرکزی", description="نام شعبه"),
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="city", type="string", maxLength=100, example="تهران", nullable=true, description="نام شهر"),
     *             @OA\Property(property="address", type="string", example="خیابان ولیعصر، پلاک ۱۲۳", nullable=true, description="آدرس کامل شعبه"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="021-12345678", nullable=true, description="شماره تلفن"),
     *             @OA\Property(property="email", type="string", maxLength=100, example="branch@company.com", nullable=true, description="آدرس ایمیل"),
     *             @OA\Property(property="manager_id", type="integer", example=5, description="شناسه مدیر شعبه"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="آیا به عنوان شعبه پیش‌فرض باشد؟"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=201, description="شعبه با موفقیت ایجاد شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد شعبه")
     * )
     */
    public function store(BranchRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // اگر کد ارسال نشده، به صورت خودکار تولید می‌شود
            if (!isset($validatedData['code']) || empty($validatedData['code'])) {
                $validatedData['code'] = $this->branchService->generateBranchCode(
                    $validatedData['name'],
                    $validatedData['company_id']
                );
            }
            
            $branch = $this->branchService->create($validatedData);

            return $this->successResponse($branch, 'شعبه با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد شعبه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/branch/{id}",
     *     summary="نمایش اطلاعات یک شعبه",
     *     tags={"Branch"},
     *     @OA\Parameter(name="id", in="path", description="شناسه شعبه", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="اطلاعات شعبه با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="شعبه یافت نشد")
     * )
     */
    public function show($id)
    {
        try {
            $branch = $this->branchService->find($id);
            
            if (!$branch) {
                return $this->errorResponse('شعبه مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($branch, 'اطلاعات شعبه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات شعبه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/branch/{id}",
     *     summary="به‌روزرسانی شعبه",
     *     tags={"Branch"},
     *     @OA\Parameter(name="id", in="path", description="شناسه شعبه", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="BR001", description="کد شعبه"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="شعبه مرکزی", description="نام شعبه"),
     *             @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"),
     *             @OA\Property(property="city", type="string", maxLength=100, example="تهران", nullable=true, description="نام شهر"),
     *             @OA\Property(property="address", type="string", example="خیابان ولیعصر، پلاک ۱۲۳", nullable=true, description="آدرس کامل شعبه"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="021-12345678", nullable=true, description="شماره تلفن"),
     *             @OA\Property(property="email", type="string", maxLength=100, example="branch@company.com", nullable=true, description="آدرس ایمیل"),
     *             @OA\Property(property="manager_id", type="integer", example=5, description="شناسه مدیر شعبه"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="آیا به عنوان شعبه پیش‌فرض باشد؟"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=200, description="شعبه با موفقیت به‌روزرسانی شد"),
     *     @OA\Response(response=404, description="شعبه یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function update(BranchUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $branch = $this->branchService->update($id, $validatedData);

            return $this->successResponse($branch, 'شعبه با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی شعبه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/branch/{id}",
     *     summary="حذف شعبه",
     *     tags={"Branch"},
     *     @OA\Parameter(name="id", in="path", description="شناسه شعبه", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="شعبه با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="شعبه یافت نشد")
     * )
     */
    public function destroy($id)
    {
        try {
            // بررسی می‌کنیم که آیا شعبه پیش‌فرض است یا خیر
            $branch = $this->branchService->find($id);
            
            if ($branch && $branch->is_default) {
                return $this->errorResponse('شعبه پیش‌فرض قابل حذف نیست. ابتدا شعبه دیگری را به عنوان پیش‌فرض انتخاب کنید.', 422);
            }
            
            $this->branchService->delete($id);

            return $this->successResponse(null, 'شعبه با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف شعبه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/branch/{id}/toggle-status",
     *     summary="تغییر وضعیت فعال/غیرفعال شعبه",
     *     tags={"Branch"},
     *     @OA\Parameter(name="id", in="path", description="شناسه شعبه", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="وضعیت شعبه با موفقیت تغییر یافت"),
     *     @OA\Response(response=404, description="شعبه یافت نشد")
     * )
     */
    public function toggleStatus($id)
    {
        try {
            $branch = $this->branchService->toggleStatus($id);

            return $this->successResponse($branch, 'وضعیت شعبه با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@toggleStatus: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت شعبه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/branch/{id}/set-default",
     *     summary="تنظیم شعبه به عنوان شعبه پیش‌فرض",
     *     tags={"Branch"},
     *     @OA\Parameter(name="id", in="path", description="شناسه شعبه", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="شعبه با موفقیت به عنوان پیش‌فرض تنظیم شد"),
     *     @OA\Response(response=404, description="شعبه یافت نشد"),
     *     @OA\Response(response=422, description="شعبه غیرفعال نمی‌تواند پیش‌فرض باشد")
     * )
     */
    public function setDefault($id)
    {
        try {
            $branch = $this->branchService->setAsDefault($id);

            return $this->successResponse($branch, 'شعبه با موفقیت به عنوان پیش‌فرض تنظیم شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@setDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تنظیم شعبه به عنوان پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/branch/active",
     *     summary="دریافت تمام شعب فعال",
     *     tags={"Branch"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت (اختیاری)", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="لیست شعب فعال با موفقیت دریافت شد")
     * )
     */
    public function getActive(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $branches = $this->branchService->getActiveBranches($companyId);
            
            return $this->successResponse($branches, 'لیست شعب فعال با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@getActive: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شعب فعال: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/branch/default",
     *     summary="دریافت شعبه پیش‌فرض شرکت",
     *     tags={"Branch"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="شعبه پیش‌فرض با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="شعبه پیش‌فرضی یافت نشد")
     * )
     */
    public function getDefault(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $branch = $this->branchService->getDefaultBranch($companyId);
            
            if (!$branch) {
                return $this->errorResponse('شعبه پیش‌فرضی برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($branch, 'شعبه پیش‌فرض با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@getDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت شعبه پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/branch/by-company/{companyId}",
     *     summary="دریافت شعب یک شرکت",
     *     tags={"Branch"},
     *     @OA\Parameter(name="companyId", in="path", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="only_active", in="query", description="فقط شعب فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Response(response=200, description="لیست شعب شرکت با موفقیت دریافت شد")
     * )
     */
    public function getByCompany($companyId, Request $request)
    {
        try {
            $onlyActive = $request->get('only_active', true);
            $branches = $this->branchService->getByCompany($companyId, $onlyActive);
            
            return $this->successResponse($branches, 'لیست شعب شرکت با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@getByCompany: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شعب شرکت: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/branch/by-country/{countryId}",
     *     summary="دریافت شعب یک کشور",
     *     tags={"Branch"},
     *     @OA\Parameter(name="countryId", in="path", description="شناسه کشور", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="only_active", in="query", description="فقط شعب فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Response(response=200, description="لیست شعب کشور با موفقیت دریافت شد")
     * )
     */
    public function getByCountry($countryId, Request $request)
    {
        try {
            $onlyActive = $request->get('only_active', true);
            $branches = $this->branchService->getByCountry($countryId, $onlyActive);
            
            return $this->successResponse($branches, 'لیست شعب کشور با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@getByCountry: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شعب کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/branch/statistics",
     *     summary="دریافت آمار شعب",
     *     tags={"Branch"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="آمار شعب با موفقیت دریافت شد")
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $statistics = $this->branchService->getStatistics($companyId);
            
            return $this->successResponse($statistics, 'آمار شعب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار شعب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/branch/list",
     *     summary="دریافت لیست ساده شعب برای استفاده در dropdown",
     *     tags={"Branch"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="only_active", in="query", description="فقط شعب فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Parameter(name="with_code", in="query", description="نمایش با کد", required=false, @OA\Schema(type="boolean", default=false)),
     *     @OA\Response(response=200, description="لیست شعب با موفقیت دریافت شد")
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
            
            $branches = $this->branchService->getList($companyId, $onlyActive, $withCode);
            
            return $this->successResponse($branches, 'لیست شعب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in BranchController@getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شعب: ' . $ex->getMessage(), 500);
        }
    }
}
