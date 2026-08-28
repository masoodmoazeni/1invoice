<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\NumberSequenceService;
use Modules\Accounting\Http\Requests\NumberSequence\NumberSequenceRequest;
use Modules\Accounting\Http\Requests\NumberSequence\NumberSequenceUpdateRequest;

class NumberSequenceController extends BaseController
{
    protected $numberSequenceService;

    public function __construct(NumberSequenceService $numberSequenceService)
    {
        $this->numberSequenceService = $numberSequenceService;
    }

    /**
     * @OA\Get(
     *     path="/number-sequence",
     *     summary="نمایش لیست شماره‌گذاری‌ها",
     *     tags={"NumberSequence"},
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
     *         description="جستجو بر اساس ماژول یا پیشوند",
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
     *         name="module",
     *         in="query",
     *         description="ماژول",
     *         required=false,
     *         @OA\Schema(type="string", enum={"sales", "purchase", "accounting", "inventory", "hr", "bank", "custom"})
     *     ),
     *     @OA\Parameter(
     *         name="reset_type",
     *         in="query",
     *         description="نوع ریست",
     *         required=false,
     *         @OA\Schema(type="string", enum={"daily", "monthly", "yearly", "never"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست شماره‌گذاری‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست شماره‌گذاری‌ها"
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
                'module',
                'reset_type'
            ]);

            // حذف فیلترهای خالی
            $filters = array_filter($filters, function($value) {
                return !is_null($value) && $value !== '';
            });

            $sequences = $this->numberSequenceService->getPaginate($perPage, $filters);

            return $this->successResponse($sequences, 'لیست شماره‌گذاری‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شماره‌گذاری‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/number-sequence",
     *     summary="ایجاد شماره‌گذاری جدید",
     *     tags={"NumberSequence"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "module"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="module", type="string", enum={"sales", "purchase", "accounting", "inventory", "hr", "bank", "custom"}, example="sales", description="ماژول"),
     *             @OA\Property(property="prefix", type="string", maxLength=20, example="INV-", description="پیشوند شماره"),
     *             @OA\Property(property="suffix", type="string", maxLength=20, example="-A", description="پسوند شماره"),
     *             @OA\Property(property="padding", type="integer", example=5, description="تعداد ارقام شماره"),
     *             @OA\Property(property="reset_type", type="string", enum={"daily", "monthly", "yearly", "never"}, example="yearly", description="نوع ریست")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="شماره‌گذاری با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="شماره‌گذاری با این مشخصات قبلاً ثبت شده است"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد شماره‌گذاری"
     *     )
     * )
     */
    public function store(NumberSequenceRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $sequence = $this->numberSequenceService->create($validatedData);

            return $this->successResponse($sequence, 'شماره‌گذاری با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد شماره‌گذاری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/{id}",
     *     summary="نمایش اطلاعات یک شماره‌گذاری",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات شماره‌گذاری با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات شماره‌گذاری"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $sequence = $this->numberSequenceService->find($id);
            
            if (!$sequence) {
                return $this->errorResponse('شماره‌گذاری مورد نظر یافت نشد', 404);
            }

            // دریافت اطلاعات تکمیلی
            $details = $this->numberSequenceService->getDetails($id);

            $data = [
                'sequence' => $sequence,
                'details' => $details
            ];

            return $this->successResponse($data, 'اطلاعات شماره‌گذاری با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات شماره‌گذاری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/number-sequence/{id}",
     *     summary="به‌روزرسانی شماره‌گذاری",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="prefix", type="string", maxLength=20, example="INV-", description="پیشوند شماره"),
     *             @OA\Property(property="suffix", type="string", maxLength=20, example="-A", description="پسوند شماره"),
     *             @OA\Property(property="padding", type="integer", example=5, description="تعداد ارقام شماره"),
     *             @OA\Property(property="reset_type", type="string", enum={"daily", "monthly", "yearly", "never"}, example="yearly", description="نوع ریست"),
     *             @OA\Property(property="current_number", type="integer", example=10, description="شماره جاری")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شماره‌گذاری با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="شماره‌گذاری با این مشخصات قبلاً ثبت شده است"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی شماره‌گذاری"
     *     )
     * )
     */
    public function update(NumberSequenceUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $sequence = $this->numberSequenceService->update($id, $validatedData);

            return $this->successResponse($sequence, 'شماره‌گذاری با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی شماره‌گذاری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/number-sequence/{id}",
     *     summary="حذف شماره‌گذاری",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شماره‌گذاری با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف شماره‌گذاری"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->numberSequenceService->delete($id);

            return $this->successResponse(null, 'شماره‌گذاری با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف شماره‌گذاری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/by-module/{module}",
     *     summary="دریافت شماره‌گذاری‌های یک ماژول",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="module",
     *         in="path",
     *         description="ماژول",
     *         required=true,
     *         @OA\Schema(type="string", enum={"sales", "purchase", "accounting", "inventory", "hr", "bank", "custom"})
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
     *         description="شماره‌گذاری‌های ماژول با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ماژول یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت شماره‌گذاری‌های ماژول"
     *     )
     * )
     */
    public function getByModule(Request $request, $module)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $sequences = $this->numberSequenceService->getByModule($request->company_id, $module);

            if ($sequences->isEmpty()) {
                return $this->errorResponse('هیچ شماره‌گذاری برای این ماژول یافت نشد', 404);
            }

            return $this->successResponse($sequences, 'شماره‌گذاری‌های ماژول با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByModule: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت شماره‌گذاری‌های ماژول: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/generate/{id}",
     *     summary="تولید شماره بعدی",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شماره بعدی با موفقیت تولید شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تولید شماره بعدی"
     *     )
     * )
     */
    public function generateNumber($id)
    {
        try {
            $number = $this->numberSequenceService->generateNumber($id);

            return $this->successResponse(['number' => $number], 'شماره بعدی با موفقیت تولید شد');
        } catch (\Exception $ex) {
            Log::error('Error in generateNumber: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تولید شماره بعدی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/next-number/{id}",
     *     summary="دریافت شماره بعدی (بدون افزایش)",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شماره بعدی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت شماره بعدی"
     *     )
     * )
     */
    public function getNextNumber($id)
    {
        try {
            $number = $this->numberSequenceService->getNextNumber($id);

            return $this->successResponse(['next_number' => $number], 'شماره بعدی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getNextNumber: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت شماره بعدی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/number-sequence/{id}/reset",
     *     summary="ریست کردن شماره به مقدار مشخص",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="number", type="integer", example=0, description="شماره جدید")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شماره با موفقیت ریست شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ریست کردن شماره"
     *     )
     * )
     */
    public function resetNumber(Request $request, $id)
    {
        try {
            $validator = validator($request->all(), [
                'number' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $this->numberSequenceService->resetNumber($id, $request->number);

            return $this->successResponse(null, 'شماره با موفقیت ریست شد');
        } catch (\Exception $ex) {
            Log::error('Error in resetNumber: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ریست کردن شماره: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/number-sequence/create-default",
     *     summary="ایجاد شماره‌گذاری‌های پیش‌فرض",
     *     tags={"NumberSequence"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="شماره‌گذاری‌های پیش‌فرض با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد شماره‌گذاری‌های پیش‌فرض"
     *     )
     * )
     */
    public function createDefault(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $sequences = $this->numberSequenceService->createDefaultSequences($request->company_id);

            return $this->successResponse($sequences, 'شماره‌گذاری‌های پیش‌فرض با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in createDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد شماره‌گذاری‌های پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/statistics",
     *     summary="دریافت آمار شماره‌گذاری‌ها",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار شماره‌گذاری‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار شماره‌گذاری‌ها"
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

            $statistics = $this->numberSequenceService->getStatistics($request->company_id);

            return $this->successResponse($statistics, 'آمار شماره‌گذاری‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار شماره‌گذاری‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/list",
     *     summary="دریافت لیست شماره‌گذاری‌ها برای انتخاب (drop-down)",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="module",
     *         in="query",
     *         description="ماژول (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"sales", "purchase", "accounting", "inventory", "hr", "bank", "custom"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست شماره‌گذاری‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست شماره‌گذاری‌ها"
     *     )
     * )
     */
    public function getList(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'company_id' => 'required|exists:companies,id',
                'module' => 'nullable|string|in:' . implode(',', \Modules\Accounting\Entities\NumberSequence::$modules),
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $list = $this->numberSequenceService->getList($request->company_id, $request->module);

            return $this->successResponse($list, 'لیست شماره‌گذاری‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست شماره‌گذاری‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/modules",
     *     summary="دریافت لیست ماژول‌های معتبر",
     *     tags={"NumberSequence"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست ماژول‌ها با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getModules()
    {
        try {
            $modules = \Modules\Accounting\Entities\NumberSequence::$moduleLabels;
            return $this->successResponse($modules, 'لیست ماژول‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getModules: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ماژول‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/number-sequence/reset-types",
     *     summary="دریافت لیست انواع ریست معتبر",
     *     tags={"NumberSequence"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع ریست با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getResetTypes()
    {
        try {
            $resetTypes = \Modules\Accounting\Entities\NumberSequence::$resetLabels;
            return $this->successResponse($resetTypes, 'لیست انواع ریست با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getResetTypes: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع ریست: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/number-sequence/{id}/update-padding",
     *     summary="به‌روزرسانی تعداد ارقام شماره",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="padding", type="integer", example=6, description="تعداد ارقام جدید")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تعداد ارقام با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی تعداد ارقام"
     *     )
     * )
     */
    public function updatePadding(Request $request, $id)
    {
        try {
            $validator = validator($request->all(), [
                'padding' => 'required|integer|min:1|max:20',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $this->numberSequenceService->updatePadding($id, $request->padding);

            return $this->successResponse(null, 'تعداد ارقام با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in updatePadding: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی تعداد ارقام: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/number-sequence/{id}/update-current-number",
     *     summary="به‌روزرسانی شماره جاری",
     *     tags={"NumberSequence"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="current_number", type="integer", example=50, description="شماره جاری جدید")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شماره جاری با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی شماره جاری"
     *     )
     * )
     */
    public function updateCurrentNumber(Request $request, $id)
    {
        try {
            $validator = validator($request->all(), [
                'current_number' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $this->numberSequenceService->updateCurrentNumber($id, $request->current_number);

            return $this->successResponse(null, 'شماره جاری با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in updateCurrentNumber: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی شماره جاری: ' . $ex->getMessage(), 500);
        }
    }
}