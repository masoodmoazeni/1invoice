<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\DimensionValueService;
use Modules\Accounting\Http\Requests\DimensionValue\DimensionValueRequest;
use Modules\Accounting\Http\Requests\DimensionValue\DimensionValueUpdateRequest;

class DimensionValueController extends BaseController
{
    protected $dimensionValueService;

    public function __construct(DimensionValueService $dimensionValueService)
    {
        $this->dimensionValueService = $dimensionValueService;
    }

    /**
     * @OA\Get(
     *     path="/dimension-value",
     *     summary="نمایش لیست مقادیر ابعاد",
     *     tags={"DimensionValue"},
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
     *         description="جستجو بر اساس نام یا کد مقدار",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="شناسه والد (برای فیلتر فرزندان)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="only_root",
     *         in="query",
     *         description="فقط مقادیر ریشه",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مقادیر با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مقادیر"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $perPage = $request->get('per_page', 15);
            $search = $request->get('search', null);
            $parentId = $request->get('parent_id', null);
            $onlyRoot = $request->get('only_root', false);

            $filters = [
                'dimension_id' => $dimensionId,
                'search' => $search,
                'parent_id' => $parentId,
                'only_root' => $onlyRoot,
            ];

            $dimensionValues = $this->dimensionValueService->getPaginate($perPage, $filters);

            return $this->successResponse($dimensionValues, 'لیست مقادیر با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('accounting::create');
    }

    /**
     * @OA\Post(
     *     path="/dimension-value",
     *     summary="ایجاد مقدار جدید برای بعد",
     *     tags={"DimensionValue"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dimension_id", "name"},
     *             @OA\Property(property="dimension_id", type="integer", example=1, description="شناسه بعد"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="COST_001", description="کد مقدار (اختیاری - در صورت عدم وارد شدن خودکار تولید می‌شود)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مرکز هزینه اصلی", description="نام مقدار"),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null, description="شناسه والد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="مقدار با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد مقدار"
     *     )
     * )
     */
    public function store(DimensionValueRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // اگر کد وارد نشده، به صورت خودکار تولید می‌شود
            if (!isset($validatedData['code']) || empty($validatedData['code'])) {
                $validatedData['code'] = $this->dimensionValueService->generateCode(
                    $validatedData['name'],
                    $validatedData['dimension_id'],
                    $validatedData['parent_id'] ?? null
                );
            }

            $dimensionValue = $this->dimensionValueService->create($validatedData);

            return $this->successResponse($dimensionValue, 'مقدار با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد مقدار: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/{id}",
     *     summary="نمایش اطلاعات یک مقدار",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مقدار",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات مقدار با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مقدار یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات مقدار"
     *     )
     * )
     */
    public function show($id, Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $with = ['dimension', 'parent', 'children'];
            $dimensionValue = $this->dimensionValueService->find($id, $dimensionId, $with);

            if (!$dimensionValue) {
                return $this->errorResponse('مقدار مورد نظر یافت نشد', 404);
            }

            // دریافت آمار تراکنش‌ها
            $statistics = $this->dimensionValueService->getStatistics($id, $dimensionId);

            $result = [
                'dimension_value' => $dimensionValue,
                'statistics' => $statistics,
                'full_path' => $dimensionValue->full_path,
                'level' => $dimensionValue->level,
                'is_root' => $dimensionValue->isRoot(),
                'is_leaf' => $dimensionValue->isLeaf(),
                'has_transactions' => $dimensionValue->hasTransactions(),
            ];

            return $this->successResponse($result, 'اطلاعات مقدار با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات مقدار: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('accounting::edit');
    }

    /**
     * @OA\Put(
     *     path="/dimension-value/{id}",
     *     summary="به‌روزرسانی مقدار",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مقدار",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="COST_001", description="کد مقدار"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="مرکز هزینه اصلی", description="نام مقدار"),
     *             @OA\Property(property="parent_id", type="integer", nullable=true, example=null, description="شناسه والد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مقدار با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مقدار یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی مقدار"
     *     )
     * )
     */
    public function update(DimensionValueUpdateRequest $request, $id)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $validatedData = $request->validated();
            $dimensionValue = $this->dimensionValueService->update($id, $dimensionId, $validatedData);

            return $this->successResponse($dimensionValue, 'مقدار با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی مقدار: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/dimension-value/{id}",
     *     summary="حذف مقدار",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مقدار",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مقدار با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مقدار یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="مقدار دارای فرزند یا تراکنش است و قابل حذف نمی‌باشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف مقدار"
     *     )
     * )
     */
    public function destroy($id, Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $this->dimensionValueService->delete($id, $dimensionId);

            return $this->successResponse(null, 'مقدار با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف مقدار: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/tree",
     *     summary="دریافت درخت مقادیر یک بعد",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="شناسه والد (اختیاری - برای دریافت زیرشاخه)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="درخت مقادیر با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت درخت مقادیر"
     *     )
     * )
     */
    public function getTree(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $parentId = $request->get('parent_id', null);
            $tree = $this->dimensionValueService->getTree($dimensionId, $parentId);

            return $this->successResponse($tree, 'درخت مقادیر با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTree: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت درخت مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/flat-tree",
     *     summary="دریافت درخت تخت مقادیر یک بعد",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="شناسه والد (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="درخت تخت مقادیر با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت درخت تخت مقادیر"
     *     )
     * )
     */
    public function getFlatTree(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $parentId = $request->get('parent_id', null);
            $flatTree = $this->dimensionValueService->getFlatTree($dimensionId, $parentId);

            return $this->successResponse($flatTree, 'درخت تخت مقادیر با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getFlatTree: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت درخت تخت مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/children/{parent_id}",
     *     summary="دریافت فرزندان یک مقدار",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="path",
     *         description="شناسه والد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست فرزندان با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="والد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست فرزندان"
     *     )
     * )
     */
    public function getChildren($parent_id, Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $children = $this->dimensionValueService->getChildren($dimensionId, $parent_id);

            return $this->successResponse($children, 'لیست فرزندان با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getChildren: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست فرزندان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/root",
     *     summary="دریافت مقادیر ریشه یک بعد",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مقادیر ریشه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مقادیر ریشه"
     *     )
     * )
     */
    public function getRoot(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $rootValues = $this->dimensionValueService->getRootValues($dimensionId);

            return $this->successResponse($rootValues, 'لیست مقادیر ریشه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getRoot: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مقادیر ریشه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/list",
     *     summary="دریافت لیست مقادیر برای استفاده در dropdown",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="flat",
     *         in="query",
     *         description="نمایش به صورت تخت با تورفتگی",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="with_code",
     *         in="query",
     *         description="نمایش نام به همراه کد",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مقادیر با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مقادیر"
     *     )
     * )
     */
    public function getList(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $flat = $request->get('flat', true);
            $withCode = $request->get('with_code', false);

            $list = $this->dimensionValueService->getList($dimensionId, $flat, $withCode);

            return $this->successResponse($list, 'لیست مقادیر با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/statistics",
     *     summary="دریافت آمار مقادیر یک بعد",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار مقادیر با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار مقادیر"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $statistics = $this->dimensionValueService->getStatistics($dimensionId);

            return $this->successResponse($statistics, 'آمار مقادیر با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/with-transactions",
     *     summary="دریافت مقادیر دارای تراکنش",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="تاریخ شروع (فرمت: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="تاریخ پایان (فرمت: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مقادیر دارای تراکنش با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مقادیر"
     *     )
     * )
     */
    public function getWithTransactions(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $startDate = $request->get('start_date', null);
            $endDate = $request->get('end_date', null);

            $values = $this->dimensionValueService->getWithTransactions($dimensionId, $startDate, $endDate);

            return $this->successResponse($values, 'لیست مقادیر دارای تراکنش با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getWithTransactions: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/without-transactions",
     *     summary="دریافت مقادیر بدون تراکنش",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست مقادیر بدون تراکنش با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست مقادیر"
     *     )
     * )
     */
    public function getWithoutTransactions(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $values = $this->dimensionValueService->getWithoutTransactions($dimensionId);

            return $this->successResponse($values, 'لیست مقادیر بدون تراکنش با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getWithoutTransactions: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/dimension-value/copy",
     *     summary="کپی کردن مقادیر از یک بعد به بعد دیگر",
     *     tags={"DimensionValue"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"source_dimension_id", "target_dimension_id"},
     *             @OA\Property(property="source_dimension_id", type="integer", example=1, description="شناسه بعد مبدا"),
     *             @OA\Property(property="target_dimension_id", type="integer", example=2, description="شناسه بعد مقصد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مقادیر با موفقیت کپی شدند"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در کپی کردن مقادیر"
     *     )
     * )
     */
    public function copy(Request $request)
    {
        try {
            $sourceDimensionId = $request->get('source_dimension_id');
            $targetDimensionId = $request->get('target_dimension_id');

            if (!$sourceDimensionId || !$targetDimensionId) {
                return $this->errorResponse('شناسه بعد مبدا و مقصد الزامی است', 422);
            }

            if ($sourceDimensionId == $targetDimensionId) {
                return $this->errorResponse('بعد مبدا و مقصد نمی‌توانند یکسان باشند', 422);
            }

            $count = $this->dimensionValueService->copyFromDimension($sourceDimensionId, $targetDimensionId);

            return $this->successResponse(
                ['copied_count' => $count],
                "تعداد {$count} مقدار با موفقیت کپی شدند"
            );
        } catch (\Exception $ex) {
            Log::error('Error in copy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در کپی کردن مقادیر: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dimension-value/path/{id}",
     *     summary="دریافت مسیر کامل یک مقدار",
     *     tags={"DimensionValue"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه مقدار",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مسیر مقدار با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مقدار یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت مسیر مقدار"
     *     )
     * )
     */
    public function getPath($id, Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');

            if (!$dimensionId) {
                return $this->errorResponse('شناسه بعد الزامی است', 422);
            }

            $path = $this->dimensionValueService->getPath($id, $dimensionId);

            return $this->successResponse(['path' => $path], 'مسیر مقدار با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getPath: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت مسیر مقدار: ' . $ex->getMessage(), 500);
        }
    }
}
