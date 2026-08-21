<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\AccountTemplateLineService;
use Modules\Accounting\Http\Requests\AccountTemplateLine\AccountTemplateLineRequest;
use Modules\Accounting\Http\Requests\AccountTemplateLine\AccountTemplateLineUpdateRequest;

class AccountTemplateLineController extends BaseController
{
    protected $accountTemplateLineService;

    public function __construct(AccountTemplateLineService $accountTemplateLineService)
    {
        $this->accountTemplateLineService = $accountTemplateLineService;
    }

    /**
     * @OA\Get(
     *     path="/account-template-line",
     *     summary="نمایش لیست ردیف‌های قالب حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="template_id", in="query", description="شناسه قالب", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="category", in="query", description="دسته‌بندی حساب", required=false, @OA\Schema(type="string", enum={"asset","liability","equity","revenue","expense"})),
     *     @OA\Parameter(name="type", in="query", description="نوع حساب", required=false, @OA\Schema(type="string", enum={"detail","header"})),
     *     @OA\Parameter(name="hierarchical", in="query", description="نمایش به صورت سلسله‌مراتبی", required=false, @OA\Schema(type="boolean", default=false)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام یا کد حساب", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="لیست ردیف‌های قالب حساب با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست"),
     *     @OA\Response(response=500, description="خطای داخلی سرور")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $templateId = $request->get('template_id');
            $category = $request->get('category');
            $type = $request->get('type');
            $hierarchical = $request->get('hierarchical', false);
            $search = $request->get('search');

            $filters = [];
            if ($templateId) {
                $filters['template_id'] = $templateId;
            }
            if ($category) {
                $filters['category'] = $category;
            }
            if ($type) {
                $filters['type'] = $type;
            }
            if ($search) {
                $filters['search'] = $search;
            }

            if ($hierarchical && $templateId) {
                $data = $this->accountTemplateLineService->getHierarchical($templateId);
                return $this->successResponse($data, 'ساختار سلسله‌مراتبی ردیف‌های قالب حساب با موفقیت دریافت شد');
            }

            $accountTemplateLines = $this->accountTemplateLineService->getPaginate($perPage, $filters);

            return $this->successResponse($accountTemplateLines, 'لیست ردیف‌های قالب حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ردیف‌های قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/account-template-line",
     *     summary="ایجاد ردیف جدید در قالب حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"template_id","account_code","account_name","category","type","normal_balance"},
     *             @OA\Property(property="template_id", type="integer", example=1, description="شناسه قالب"),
     *             @OA\Property(property="account_code", type="string", maxLength=50, example="1.1.1", description="کد حساب"),
     *             @OA\Property(property="account_name", type="string", maxLength=100, example="حساب جاری", description="نام حساب"),
     *             @OA\Property(property="parent_code", type="string", maxLength=50, example="1.1", nullable=true, description="کد حساب والد"),
     *             @OA\Property(property="category", type="string", enum={"asset","liability","equity","revenue","expense"}, example="asset", description="دسته‌بندی حساب"),
     *             @OA\Property(property="type", type="string", enum={"detail","header"}, example="detail", description="نوع حساب"),
     *             @OA\Property(property="normal_balance", type="string", enum={"debit","credit"}, example="debit", description="مانده عادی")
     *         )
     *     ),
     *     @OA\Response(response=201, description="ردیف قالب حساب با موفقیت ایجاد شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد ردیف")
     * )
     */
    public function store(AccountTemplateLineRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $accountTemplateLine = $this->accountTemplateLineService->create($validatedData);

            return $this->successResponse($accountTemplateLine, 'ردیف قالب حساب با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد ردیف قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template-line/{id}",
     *     summary="نمایش اطلاعات یک ردیف قالب حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ردیف", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="اطلاعات ردیف قالب حساب با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="ردیف قالب حساب یافت نشد")
     * )
     */
    public function show($id)
    {
        try {
            $accountTemplateLine = $this->accountTemplateLineService->find($id);
            
            if (!$accountTemplateLine) {
                return $this->errorResponse('ردیف قالب حساب مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($accountTemplateLine, 'اطلاعات ردیف قالب حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات ردیف قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/account-template-line/{id}",
     *     summary="به‌روزرسانی ردیف قالب حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ردیف", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="account_code", type="string", maxLength=50, example="1.1.1", description="کد حساب"),
     *             @OA\Property(property="account_name", type="string", maxLength=100, example="حساب جاری", description="نام حساب"),
     *             @OA\Property(property="parent_code", type="string", maxLength=50, example="1.1", nullable=true, description="کد حساب والد"),
     *             @OA\Property(property="category", type="string", enum={"asset","liability","equity","revenue","expense"}, example="asset", description="دسته‌بندی حساب"),
     *             @OA\Property(property="type", type="string", enum={"detail","header"}, example="detail", description="نوع حساب"),
     *             @OA\Property(property="normal_balance", type="string", enum={"debit","credit"}, example="debit", description="مانده عادی")
     *         )
     *     ),
     *     @OA\Response(response=200, description="ردیف قالب حساب با موفقیت به‌روزرسانی شد"),
     *     @OA\Response(response=404, description="ردیف قالب حساب یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function update(AccountTemplateLineUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $accountTemplateLine = $this->accountTemplateLineService->update($id, $validatedData);

            return $this->successResponse($accountTemplateLine, 'ردیف قالب حساب با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی ردیف قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/account-template-line/{id}",
     *     summary="حذف ردیف قالب حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ردیف", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="ردیف قالب حساب با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="ردیف قالب حساب یافت نشد"),
     *     @OA\Response(response=400, description="امکان حذف این ردیف وجود ندارد زیرا دارای زیرمجموعه است")
     * )
     */
    public function destroy($id)
    {
        try {
            // بررسی وجود زیرمجموعه‌ها قبل از حذف
            $accountTemplateLine = $this->accountTemplateLineService->find($id);
            
            if ($accountTemplateLine) {
                $children = $this->accountTemplateLineService->getChildren(
                    $accountTemplateLine->template_id,
                    $accountTemplateLine->account_code
                );
                
                if ($children->count() > 0) {
                    return $this->errorResponse(
                        'امکان حذف این ردیف وجود ندارد زیرا دارای زیرمجموعه است',
                        400
                    );
                }
            }
            
            $this->accountTemplateLineService->delete($id);

            return $this->successResponse(null, 'ردیف قالب حساب با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف ردیف قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template-line/by-template/{templateId}",
     *     summary="دریافت ردیف‌های یک قالب خاص",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="templateId", in="path", description="شناسه قالب", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="hierarchical", in="query", description="نمایش به صورت سلسله‌مراتبی", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Response(response=200, description="لیست ردیف‌های قالب با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="قالب یافت نشد")
     * )
     */
    public function getByTemplate($templateId)
    {
        try {
            $hierarchical = request()->get('hierarchical', true);
            
            if ($hierarchical) {
                $data = $this->accountTemplateLineService->getHierarchical($templateId);
            } else {
                $data = $this->accountTemplateLineService->getAll($templateId);
            }
            
            return $this->successResponse($data, 'لیست ردیف‌های قالب حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByTemplate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ردیف‌های قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template-line/by-category",
     *     summary="دریافت ردیف‌ها بر اساس دسته‌بندی",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="template_id", in="query", description="شناسه قالب", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="category", in="query", description="دسته‌بندی حساب", required=true, @OA\Schema(type="string", enum={"asset","liability","equity","revenue","expense"})),
     *     @OA\Response(response=200, description="لیست ردیف‌ها با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function getByCategory(Request $request)
    {
        try {
            $request->validate([
                'template_id' => 'required|exists:account_templates,id',
                'category' => 'required|in:asset,liability,equity,revenue,expense'
            ]);
            
            $data = $this->accountTemplateLineService->getByCategory(
                $request->template_id,
                $request->category
            );
            
            return $this->successResponse($data, 'لیست ردیف‌ها بر اساس دسته‌بندی با موفقیت دریافت شد');
        } catch (\Illuminate\Validation\ValidationException $ex) {
            return $this->errorResponse('خطا در اعتبارسنجی: ' . $ex->getMessage(), 422);
        } catch (\Exception $ex) {
            Log::error('Error in getByCategory: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template-line/children",
     *     summary="دریافت زیرمجموعه‌های یک کد حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="template_id", in="query", description="شناسه قالب", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="parent_code", in="query", description="کد حساب والد", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="لیست زیرمجموعه‌ها با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function getChildren(Request $request)
    {
        try {
            $request->validate([
                'template_id' => 'required|exists:account_templates,id',
                'parent_code' => 'required|string|max:50'
            ]);
            
            $data = $this->accountTemplateLineService->getChildren(
                $request->template_id,
                $request->parent_code
            );
            
            return $this->successResponse($data, 'لیست زیرمجموعه‌ها با موفقیت دریافت شد');
        } catch (\Illuminate\Validation\ValidationException $ex) {
            return $this->errorResponse('خطا در اعتبارسنجی: ' . $ex->getMessage(), 422);
        } catch (\Exception $ex) {
            Log::error('Error in getChildren: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت زیرمجموعه‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template-line/tree/{templateId}",
     *     summary="دریافت ساختار درختی کامل قالب حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="templateId", in="path", description="شناسه قالب", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="ساختار درختی قالب حساب با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="قالب یافت نشد")
     * )
     */
    public function getTree($templateId)
    {
        try {
            $data = $this->accountTemplateLineService->getHierarchical($templateId);
            
            return $this->successResponse($data, 'ساختار درختی قالب حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTree: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ساختار درختی قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/account-template-line/statistics",
     *     summary="دریافت آمار ردیف‌های قالب حساب",
     *     tags={"AccountTemplateLine"},
     *     @OA\Parameter(name="template_id", in="query", description="شناسه قالب (اختیاری)", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="آمار ردیف‌ها با موفقیت دریافت شد")
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $templateId = $request->get('template_id');
            $statistics = $this->accountTemplateLineService->getStatistics($templateId);
            
            return $this->successResponse($statistics, 'آمار ردیف‌های قالب حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار ردیف‌های قالب حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/account-template-line/bulk",
     *     summary="ایجاد چندین ردیف به صورت گروهی",
     *     tags={"AccountTemplateLine"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"template_id","lines"},
     *             @OA\Property(property="template_id", type="integer", example=1, description="شناسه قالب"),
     *             @OA\Property(
     *                 property="lines",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="account_code", type="string", example="1.1.1"),
     *                     @OA\Property(property="account_name", type="string", example="حساب جاری"),
     *                     @OA\Property(property="parent_code", type="string", example="1.1"),
     *                     @OA\Property(property="category", type="string", enum={"asset","liability","equity","revenue","expense"}, example="asset"),
     *                     @OA\Property(property="type", type="string", enum={"detail","header"}, example="detail"),
     *                     @OA\Property(property="normal_balance", type="string", enum={"debit","credit"}, example="debit")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="ردیف‌های قالب حساب با موفقیت ایجاد شدند"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد ردیف‌ها")
     * )
     */
    public function bulkStore(Request $request)
    {
        try {
            $request->validate([
                'template_id' => 'required|exists:account_templates,id',
                'lines' => 'required|array|min:1',
                'lines.*.account_code' => 'required|string|max:50',
                'lines.*.account_name' => 'required|string|max:100',
                'lines.*.parent_code' => 'nullable|string|max:50',
                'lines.*.category' => 'required|in:asset,liability,equity,revenue,expense',
                'lines.*.type' => 'required|in:detail,header',
                'lines.*.normal_balance' => 'required|in:debit,credit'
            ]);
            
            $templateId = $request->template_id;
            $lines = $request->lines;
            
            $created = $this->accountTemplateLineService->bulkCreate($templateId, $lines);
            
            return $this->successResponse(
                $created, 
                'تعداد ' . count($created) . ' ردیف قالب حساب با موفقیت ایجاد شد',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $ex) {
            return $this->errorResponse('خطا در اعتبارسنجی: ' . $ex->getMessage(), 422);
        } catch (\Exception $ex) {
            Log::error('Error in bulkStore: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد گروهی ردیف‌های قالب حساب: ' . $ex->getMessage(), 500);
        }
    }
}
