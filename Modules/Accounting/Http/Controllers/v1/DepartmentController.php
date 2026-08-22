<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\DepartmentService;

use Modules\Accounting\Http\Requests\Department\DepartmentRequest;
use Modules\Accounting\Http\Requests\Department\DepartmentUpdateRequest;

class DepartmentController extends BaseController
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

        /**
     * @OA\Get(
     *     path="/department",
     *     summary="نمایش لیست دپارتمان‌ها",
     *     tags={"Department"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام یا کد", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="company_id", in="query", description="فیلتر بر اساس شناسه شرکت", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="parent_id", in="query", description="فیلتر بر اساس دپارتمان والد", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="is_active", in="query", description="فیلتر بر اساس وضعیت فعال", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="has_children", in="query", description="فیلتر بر اساس داشتن فرزند", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="is_root", in="query", description="فیلتر بر اساس دپارتمان ریشه", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort_by", in="query", description="فیلد مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"id","code","name","is_active","created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="ترتیب مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc")),
     *     @OA\Response(response=200, description="لیست دپارتمان‌ها با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست دپارتمان‌ها"),
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
            
            if ($request->has('parent_id')) {
                $filters['parent_id'] = $request->get('parent_id');
            }
            
            if ($request->has('is_active')) {
                $filters['is_active'] = $request->get('is_active');
            }
            
            if ($request->has('has_children')) {
                $filters['has_children'] = $request->get('has_children');
            }
            
            if ($request->has('is_root')) {
                $filters['is_root'] = $request->get('is_root');
            }
            
            if ($request->has('sort_by')) {
                $filters['sort_by'] = $request->get('sort_by');
            }
            
            
            $departments = $this->departmentService->getPaginate($perPage, $filters);

            return $this->successResponse($departments, 'لیست دپارتمان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دپارتمان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/department",
     *     summary="ایجاد دپارتمان جدید",
     *     tags={"Department"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "name", "manager_id"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="HR", nullable=true, description="کد دپارتمان (در صورت عدم ارسال، خودکار تولید می‌شود)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="منابع انسانی", description="نام دپارتمان"),
     *             @OA\Property(property="parent_id", type="integer", example=null, nullable=true, description="شناسه دپارتمان والد (برای ساختار سلسله‌مراتبی)"),
     *             @OA\Property(property="manager_id", type="integer", example=5, description="شناسه مدیر دپارتمان"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=201, description="دپارتمان با موفقیت ایجاد شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد دپارتمان")
     * )
     */
    public function store(DepartmentRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // اگر کد ارسال نشده، به صورت خودکار تولید می‌شود
            if (!isset($validatedData['code']) || empty($validatedData['code'])) {
                $validatedData['code'] = $this->departmentService->generateDepartmentCode(
                    $validatedData['name'],
                    $validatedData['company_id']
                );
            }
            
            $department = $this->departmentService->create($validatedData);

            return $this->successResponse($department, 'دپارتمان با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد دپارتمان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/{id}",
     *     summary="نمایش اطلاعات یک دپارتمان",
     *     tags={"Department"},
     *     @OA\Parameter(name="id", in="path", description="شناسه دپارتمان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="اطلاعات دپارتمان با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد")
     * )
     */
    public function show($id)
    {
        try {
            $department = $this->departmentService->find($id);
            
            if (!$department) {
                return $this->errorResponse('دپارتمان مورد نظر یافت نشد', 404);
            }

            // بارگذاری روابط مربوطه
            $department->load(['parent', 'children', 'manager', 'company']);

            return $this->successResponse($department, 'اطلاعات دپارتمان با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات دپارتمان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/department/{id}",
     *     summary="به‌روزرسانی دپارتمان",
     *     tags={"Department"},
     *     @OA\Parameter(name="id", in="path", description="شناسه دپارتمان", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="HR", description="کد دپارتمان"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="منابع انسانی", description="نام دپارتمان"),
     *             @OA\Property(property="parent_id", type="integer", example=null, nullable=true, description="شناسه دپارتمان والد"),
     *             @OA\Property(property="manager_id", type="integer", example=5, description="شناسه مدیر دپارتمان"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=200, description="دپارتمان با موفقیت به‌روزرسانی شد"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function update(DepartmentUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $department = $this->departmentService->update($id, $validatedData);

            return $this->successResponse($department, 'دپارتمان با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی دپارتمان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/department/{id}",
     *     summary="حذف دپارتمان",
     *     tags={"Department"},
     *     @OA\Parameter(name="id", in="path", description="شناسه دپارتمان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="دپارتمان با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد"),
     *     @OA\Response(response=422, description="این دپارتمان دارای زیرمجموعه است و قابل حذف نمی‌باشد")
     * )
     */
    public function destroy($id)
    {
        try {
            // بررسی می‌کنیم که آیا دپارتمان دارای زیرمجموعه است یا خیر
            $hasChildren = $this->departmentService->hasChildren($id);
            
            if ($hasChildren) {
                return $this->errorResponse('این دپارتمان دارای زیرمجموعه است و قابل حذف نمی‌باشد', 422);
            }
            
            $this->departmentService->delete($id);

            return $this->successResponse(null, 'دپارتمان با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف دپارتمان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/department/{id}/toggle-status",
     *     summary="تغییر وضعیت فعال/غیرفعال دپارتمان",
     *     tags={"Department"},
     *     @OA\Parameter(name="id", in="path", description="شناسه دپارتمان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="وضعیت دپارتمان با موفقیت تغییر یافت"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد")
     * )
     */
    public function toggleStatus($id)
    {
        try {
            $department = $this->departmentService->toggleStatus($id);

            return $this->successResponse($department, 'وضعیت دپارتمان با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@toggleStatus: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت دپارتمان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/department/{id}/deactivate-with-children",
     *     summary="غیرفعال کردن دپارتمان به همراه تمام زیرمجموعه‌ها",
     *     tags={"Department"},
     *     @OA\Parameter(name="id", in="path", description="شناسه دپارتمان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="دپارتمان و زیرمجموعه‌ها با موفقیت غیرفعال شدند"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد")
     * )
     */
    public function deactivateWithChildren($id)
    {
        try {
            $department = $this->departmentService->deactivateWithChildren($id);

            return $this->successResponse($department, 'دپارتمان و زیرمجموعه‌ها با موفقیت غیرفعال شدند');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@deactivateWithChildren: ' . $ex->getMessage());
            return $this->errorResponse('خطا در غیرفعال کردن دپارتمان و زیرمجموعه‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/tree",
     *     summary="دریافت درختواره دپارتمان‌ها",
     *     tags={"Department"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="parent_id", in="query", description="شناسه دپارتمان والد (اختیاری)", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="درختواره دپارتمان‌ها با موفقیت دریافت شد")
     * )
     */
    public function getTree(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $parentId = $request->get('parent_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $tree = $this->departmentService->getTree($companyId, $parentId);
            
            return $this->successResponse($tree, 'درختواره دپارتمان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@getTree: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت درختواره دپارتمان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/flat-tree",
     *     summary="دریافت لیست تخت دپارتمان‌ها با تورفتگی",
     *     tags={"Department"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="لیست تخت دپارتمان‌ها با موفقیت دریافت شد")
     * )
     */
    public function getFlatTree(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $flatTree = $this->departmentService->getFlatTree($companyId);
            
            return $this->successResponse($flatTree, 'لیست تخت دپارتمان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@getFlatTree: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست تخت دپارتمان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/root",
     *     summary="دریافت دپارتمان‌های ریشه (بدون والد)",
     *     tags={"Department"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="لیست دپارتمان‌های ریشه با موفقیت دریافت شد")
     * )
     */
    public function getRootDepartments(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $departments = $this->departmentService->getRootDepartments($companyId);
            
            return $this->successResponse($departments, 'لیست دپارتمان‌های ریشه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@getRootDepartments: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دپارتمان‌های ریشه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/children/{id}",
     *     summary="دریافت زیرمجموعه‌های یک دپارتمان",
     *     tags={"Department"},
     *     @OA\Parameter(name="id", in="path", description="شناسه دپارتمان", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="only_active", in="query", description="فقط زیرمجموعه‌های فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Response(response=200, description="زیرمجموعه‌ها با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد")
     * )
     */
    public function getChildren($id, Request $request)
    {
        try {
            $department = $this->departmentService->find($id);
            
            if (!$department) {
                return $this->errorResponse('دپارتمان مورد نظر یافت نشد', 404);
            }
            
            $onlyActive = $request->get('only_active', true);
            $children = $this->departmentService->getChildren($id, $onlyActive);
            
            return $this->successResponse($children, 'زیرمجموعه‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@getChildren: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت زیرمجموعه‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/ancestors/{id}",
     *     summary="دریافت تمام والدهای یک دپارتمان (به صورت سلسله‌مراتبی)",
     *     tags={"Department"},
     *     @OA\Parameter(name="id", in="path", description="شناسه دپارتمان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="والدها با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد")
     * )
     */
    public function getAncestors($id)
    {
        try {
            $department = $this->departmentService->find($id);
            
            if (!$department) {
                return $this->errorResponse('دپارتمان مورد نظر یافت نشد', 404);
            }
            
            $ancestors = $this->departmentService->getAncestors($id);
            
            return $this->successResponse($ancestors, 'والدها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@getAncestors: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت والدها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/list",
     *     summary="دریافت لیست ساده دپارتمان‌ها برای استفاده در dropdown",
     *     tags={"Department"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="only_active", in="query", description="فقط دپارتمان‌های فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Parameter(name="flat", in="query", description="نمایش به صورت تخت با تورفتگی", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Response(response=200, description="لیست دپارتمان‌ها با موفقیت دریافت شد")
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
            $flat = $request->get('flat', true);
            
            $departments = $this->departmentService->getList($companyId, $onlyActive, $flat);
            
            return $this->successResponse($departments, 'لیست دپارتمان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دپارتمان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/by-level/{level}",
     *     summary="دریافت دپارتمان‌های یک سطح خاص",
     *     tags={"Department"},
     *     @OA\Parameter(name="level", in="path", description="سطح دپارتمان (0 برای ریشه)", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="لیست دپارتمان‌ها با موفقیت دریافت شد")
     * )
     */
    public function getByLevel($level, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $departments = $this->departmentService->getByLevel($companyId, $level);
            
            return $this->successResponse($departments, 'لیست دپارتمان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@getByLevel: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دپارتمان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/department/statistics",
     *     summary="دریافت آمار دپارتمان‌ها",
     *     tags={"Department"},
     *     @OA\Parameter(name="company_id", in="query", description="شناسه شرکت", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="آمار دپارتمان‌ها با موفقیت دریافت شد")
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }
            
            $statistics = $this->departmentService->getStatistics($companyId);
            
            return $this->successResponse($statistics, 'آمار دپارتمان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار دپارتمان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/department/move",
     *     summary="جابه‌جایی دپارتمان به یک والد جدید",
     *     tags={"Department"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"department_id", "new_parent_id"},
     *             @OA\Property(property="department_id", type="integer", example=1, description="شناسه دپارتمان مورد نظر"),
     *             @OA\Property(property="new_parent_id", type="integer", example=2, nullable=true, description="شناسه دپارتمان والد جدید (null برای ریشه)")
     *         )
     *     ),
     *     @OA\Response(response=200, description="دپارتمان با موفقیت جابه‌جا شد"),
     *     @OA\Response(response=404, description="دپارتمان یافت نشد"),
     *     @OA\Response(response=422, description="خطا در جابه‌جایی دپارتمان")
     * )
     */
    public function move(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'new_parent_id' => 'nullable|exists:departments,id',
            ]);
            
            $department = $this->departmentService->move(
                $request->department_id,
                $request->new_parent_id
            );
            
            return $this->successResponse($department, 'دپارتمان با موفقیت جابه‌جا شد');
        } catch (\Exception $ex) {
            Log::error('Error in DepartmentController@move: ' . $ex->getMessage());
            return $this->errorResponse('خطا در جابه‌جایی دپارتمان: ' . $ex->getMessage(), 500);
        }
    }
}
