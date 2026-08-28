<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\JournalEntryLineDimensionService;
use Modules\Accounting\Http\Requests\JournalEntryLineDimension\JournalEntryLineDimensionRequest;
use Modules\Accounting\Http\Requests\JournalEntryLineDimension\JournalEntryLineDimensionUpdateRequest;

class JournalEntryLineDimensionController extends BaseController
{
    protected $journalEntryLineDimensionService;

    public function __construct(JournalEntryLineDimensionService $journalEntryLineDimensionService)
    {
        $this->journalEntryLineDimensionService = $journalEntryLineDimensionService;
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension",
     *     summary="نمایش لیست ارتباطات ابعاد ردیف‌های سند",
     *     tags={"JournalEntryLineDimension"},
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
     *         name="journal_entry_line_id",
     *         in="query",
     *         description="شناسه ردیف سند",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_value_id",
     *         in="query",
     *         description="شناسه مقدار بعد",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_type",
     *         in="query",
     *         description="نوع بعد",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست ارتباطات ابعاد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست ارتباطات ابعاد"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only([
                'journal_entry_line_id',
                'dimension_value_id',
                'dimension_id',
                'dimension_type'
            ]);

            // حذف فیلترهای خالی
            $filters = array_filter($filters, function($value) {
                return !is_null($value) && $value !== '';
            });

            $dimensions = $this->journalEntryLineDimensionService->getPaginate($perPage, $filters);

            return $this->successResponse($dimensions, 'لیست ارتباطات ابعاد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ارتباطات ابعاد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/journal-entry-line-dimension",
     *     summary="ایجاد ارتباط بعد برای ردیف سند",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"journal_entry_line_id", "dimension_value_id"},
     *             @OA\Property(property="journal_entry_line_id", type="integer", example=1, description="شناسه ردیف سند"),
     *             @OA\Property(property="dimension_value_id", type="integer", example=5, description="شناسه مقدار بعد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="ارتباط بعد با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="ارتباط بعد برای این ردیف و مقدار بعد قبلاً ثبت شده است"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد ارتباط بعد"
     *     )
     * )
     */
    public function store(JournalEntryLineDimensionRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $dimension = $this->journalEntryLineDimensionService->create($validatedData);

            return $this->successResponse($dimension, 'ارتباط بعد با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد ارتباط بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/{id}",
     *     summary="نمایش اطلاعات یک ارتباط بعد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه ارتباط بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات ارتباط بعد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ارتباط بعد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات ارتباط بعد"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $dimension = $this->journalEntryLineDimensionService->find($id);
            
            if (!$dimension) {
                return $this->errorResponse('ارتباط بعد مورد نظر یافت نشد', 404);
            }

            // دریافت اطلاعات تکمیلی
            $details = $this->journalEntryLineDimensionService->getDetails($id);

            $data = [
                'dimension' => $dimension,
                'details' => $details
            ];

            return $this->successResponse($data, 'اطلاعات ارتباط بعد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات ارتباط بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/journal-entry-line-dimension/{id}",
     *     summary="به‌روزرسانی ارتباط بعد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه ارتباط بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="journal_entry_line_id", type="integer", example=1, description="شناسه ردیف سند"),
     *             @OA\Property(property="dimension_value_id", type="integer", example=5, description="شناسه مقدار بعد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ارتباط بعد با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ارتباط بعد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="ارتباط بعد برای این ردیف و مقدار بعد قبلاً ثبت شده است"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی ارتباط بعد"
     *     )
     * )
     */
    public function update(JournalEntryLineDimensionUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $dimension = $this->journalEntryLineDimensionService->update($id, $validatedData);

            return $this->successResponse($dimension, 'ارتباط بعد با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی ارتباط بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/journal-entry-line-dimension/{id}",
     *     summary="حذف ارتباط بعد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه ارتباط بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ارتباط بعد با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ارتباط بعد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف ارتباط بعد"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->journalEntryLineDimensionService->delete($id);

            return $this->successResponse(null, 'ارتباط بعد با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف ارتباط بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/by-line/{line_id}",
     *     summary="دریافت ابعاد یک ردیف سند",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="line_id",
     *         in="path",
     *         description="شناسه ردیف سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ابعاد ردیف با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ردیف سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ابعاد ردیف"
     *     )
     * )
     */
    public function getByLine($line_id)
    {
        try {
            $dimensions = $this->journalEntryLineDimensionService->getByLine($line_id);

            if ($dimensions->isEmpty()) {
                return $this->errorResponse('هیچ بعدی برای این ردیف سند یافت نشد', 404);
            }

            return $this->successResponse($dimensions, 'ابعاد ردیف با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByLine: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ابعاد ردیف: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/by-dimension-value/{dimension_value_id}",
     *     summary="دریافت ردیف‌های یک مقدار بعد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="dimension_value_id",
     *         in="path",
     *         description="شناسه مقدار بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های مقدار بعد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مقدار بعد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ردیف‌های مقدار بعد"
     *     )
     * )
     */
    public function getByDimensionValue($dimension_value_id)
    {
        try {
            $lines = $this->journalEntryLineDimensionService->getLinesByDimensionValue($dimension_value_id);

            if ($lines->isEmpty()) {
                return $this->errorResponse('هیچ ردیفی برای این مقدار بعد یافت نشد', 404);
            }

            return $this->successResponse($lines, 'ردیف‌های مقدار بعد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByDimensionValue: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌های مقدار بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/by-dimension/{dimension_id}",
     *     summary="دریافت ارتباطات یک بعد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="path",
     *         description="شناسه بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ارتباطات بعد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="بعد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ارتباطات بعد"
     *     )
     * )
     */
    public function getByDimension($dimension_id)
    {
        try {
            $dimensions = $this->journalEntryLineDimensionService->getByDimension($dimension_id);

            if ($dimensions->isEmpty()) {
                return $this->errorResponse('هیچ ارتباطی برای این بعد یافت نشد', 404);
            }

            return $this->successResponse($dimensions, 'ارتباطات بعد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByDimension: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ارتباطات بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/by-journal-entry/{entry_id}",
     *     summary="دریافت ابعاد یک سند کامل",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="entry_id",
     *         in="path",
     *         description="شناسه سند حسابداری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ابعاد سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ابعاد سند"
     *     )
     * )
     */
    public function getByJournalEntry($entry_id)
    {
        try {
            $dimensions = $this->journalEntryLineDimensionService->getByJournalEntry($entry_id);

            if ($dimensions->isEmpty()) {
                return $this->errorResponse('هیچ بعدی برای این سند یافت نشد', 404);
            }

            return $this->successResponse($dimensions, 'ابعاد سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByJournalEntry: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ابعاد سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/journal-entry-line-dimension/create-for-line",
     *     summary="ایجاد چندین ارتباط بعد برای یک ردیف",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"line_id", "dimension_value_ids"},
     *             @OA\Property(property="line_id", type="integer", example=1, description="شناسه ردیف سند"),
     *             @OA\Property(
     *                 property="dimension_value_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={5, 6, 7},
     *                 description="شناسه‌های مقادیر بعد"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="ارتباطات بعد با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد ارتباطات بعد"
     *     )
     * )
     */
    public function createForLine(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'line_id' => 'required|exists:journal_entry_lines,id',
                'dimension_value_ids' => 'required|array|min:1',
                'dimension_value_ids.*' => 'required|exists:dimension_values,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $dimensions = $this->journalEntryLineDimensionService->createForLine(
                $request->line_id,
                $request->dimension_value_ids
            );

            return $this->successResponse($dimensions, 'ارتباطات بعد با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in createForLine: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد ارتباطات بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/journal-entry-line-dimension/update-for-line/{line_id}",
     *     summary="به‌روزرسانی ارتباطات بعد یک ردیف",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="line_id",
     *         in="path",
     *         description="شناسه ردیف سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dimension_value_ids"},
     *             @OA\Property(
     *                 property="dimension_value_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={5, 6, 7},
     *                 description="شناسه‌های مقادیر بعد"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ارتباطات بعد با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ردیف سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی ارتباطات بعد"
     *     )
     * )
     */
    public function updateForLine(Request $request, $line_id)
    {
        try {
            $validator = validator($request->all(), [
                'dimension_value_ids' => 'required|array|min:1',
                'dimension_value_ids.*' => 'required|exists:dimension_values,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $dimensions = $this->journalEntryLineDimensionService->updateForLine(
                $line_id,
                $request->dimension_value_ids
            );

            return $this->successResponse($dimensions, 'ارتباطات بعد با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in updateForLine: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی ارتباطات بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/journal-entry-line-dimension/copy-from-line",
     *     summary="کپی ارتباطات بعد از یک ردیف به ردیف دیگر",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"source_line_id", "target_line_id"},
     *             @OA\Property(property="source_line_id", type="integer", example=1, description="شناسه ردیف مبدا"),
     *             @OA\Property(property="target_line_id", type="integer", example=2, description="شناسه ردیف مقصد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ارتباطات بعد با موفقیت کپی شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در کپی ارتباطات بعد"
     *     )
     * )
     */
    public function copyFromLine(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'source_line_id' => 'required|exists:journal_entry_lines,id',
                'target_line_id' => 'required|exists:journal_entry_lines,id|different:source_line_id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $dimensions = $this->journalEntryLineDimensionService->copyFromLine(
                $request->source_line_id,
                $request->target_line_id
            );

            return $this->successResponse($dimensions, 'ارتباطات بعد با موفقیت کپی شد');
        } catch (\Exception $ex) {
            Log::error('Error in copyFromLine: ' . $ex->getMessage());
            return $this->errorResponse('خطا در کپی ارتباطات بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/journal-entry-line-dimension/delete-for-line/{line_id}",
     *     summary="حذف تمام ارتباطات بعد یک ردیف",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="line_id",
     *         in="path",
     *         description="شناسه ردیف سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ارتباطات بعد با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ردیف سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف ارتباطات بعد"
     *     )
     * )
     */
    public function deleteForLine($line_id)
    {
        try {
            $this->journalEntryLineDimensionService->deleteForLine($line_id);

            return $this->successResponse(null, 'ارتباطات بعد با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in deleteForLine: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف ارتباطات بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/statistics",
     *     summary="دریافت آمار ارتباطات ابعاد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="dimension_id",
     *         in="query",
     *         description="شناسه بعد (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار ارتباطات ابعاد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار ارتباطات ابعاد"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $dimensionId = $request->get('dimension_id');
            $statistics = $this->journalEntryLineDimensionService->getStatistics($dimensionId);

            return $this->successResponse($statistics, 'آمار ارتباطات ابعاد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار ارتباطات ابعاد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/most-used",
     *     summary="دریافت ابعاد پرکاربرد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="تعداد نتایج",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ابعاد پرکاربرد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ابعاد پرکاربرد"
     *     )
     * )
     */
    public function getMostUsed(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $dimensions = $this->journalEntryLineDimensionService->getMostUsedDimensions($limit);

            return $this->successResponse($dimensions, 'ابعاد پرکاربرد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getMostUsed: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ابعاد پرکاربرد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/check-exists",
     *     summary="بررسی وجود ارتباط بعد",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="line_id",
     *         in="query",
     *         description="شناسه ردیف سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="dimension_value_id",
     *         in="query",
     *         description="شناسه مقدار بعد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نتیجه بررسی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در بررسی ارتباط بعد"
     *     )
     * )
     */
    public function checkExists(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'line_id' => 'required|exists:journal_entry_lines,id',
                'dimension_value_id' => 'required|exists:dimension_values,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $exists = $this->journalEntryLineDimensionService->checkExists(
                $request->line_id,
                $request->dimension_value_id
            );

            return $this->successResponse(['exists' => $exists], 'نتیجه بررسی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in checkExists: ' . $ex->getMessage());
            return $this->errorResponse('خطا در بررسی ارتباط بعد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line-dimension/dimension-values/{line_id}",
     *     summary="دریافت مقادیر بعد برای یک ردیف",
     *     tags={"JournalEntryLineDimension"},
     *     @OA\Parameter(
     *         name="line_id",
     *         in="path",
     *         description="شناسه ردیف سند",
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
     *         description="مقادیر بعد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مقداری برای این بعد یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت مقادیر بعد"
     *     )
     * )
     */
    public function getDimensionValuesForLine(Request $request, $line_id)
    {
        try {
            $validator = validator($request->all(), [
                'dimension_id' => 'required|exists:dimensions,id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $values = $this->journalEntryLineDimensionService->getDimensionValuesForLine(
                $line_id,
                $request->dimension_id
            );

            if ($values->isEmpty()) {
                return $this->errorResponse('هیچ مقداری برای این بعد در این ردیف یافت نشد', 404);
            }

            return $this->successResponse($values, 'مقادیر بعد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDimensionValuesForLine: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت مقادیر بعد: ' . $ex->getMessage(), 500);
        }
    }
}