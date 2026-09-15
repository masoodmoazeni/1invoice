<?php

namespace Modules\Accounting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Http\Requests\ExchangeRate\ExchangeRateRequest;
use Modules\Accounting\Http\Requests\ExchangeRate\ExchangeRateUpdateRequest;
use Modules\System\Services\ExchangeRateService;

class ExchangeRateController extends BaseController
{
    protected $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * @OA\Get(
     *     path="/exchange-rate",
     *     summary="نمایش لیست نرخ‌های ارز",
     *     tags={"ExchangeRate"},
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
     *         description="جستجو بر اساس کد یا نام ارز",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="from_currency_id",
     *         in="query",
     *         description="شناسه ارز مبدا",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="to_currency_id",
     *         in="query",
     *         description="شناسه ارز مقصد",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="تاریخ شروع (فرمت: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="تاریخ پایان (فرمت: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="منبع نرخ ارز",
     *         required=false,
     *         @OA\Schema(type="string", enum={"manual", "api", "central_bank", "exchange_market", "import"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست نرخ‌های ارز با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست نرخ‌های ارز"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only([
                'search',
                'from_currency_id',
                'to_currency_id',
                'start_date',
                'end_date',
                'source'
            ]);

            // حذف فیلترهای خالی
            $filters = array_filter($filters, function($value) {
                return !is_null($value) && $value !== '';
            });

            $exchangeRates = $this->exchangeRateService->getPaginate($perPage, $filters);

            return $this->successResponse($exchangeRates, 'لیست نرخ‌های ارز با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست نرخ‌های ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/exchange-rate",
     *     summary="ایجاد نرخ ارز جدید",
     *     tags={"ExchangeRate"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_currency_id", "to_currency_id", "rate", "effective_date"},
     *             @OA\Property(property="from_currency_id", type="integer", example=1, description="شناسه ارز مبدا"),
     *             @OA\Property(property="to_currency_id", type="integer", example=2, description="شناسه ارز مقصد"),
     *             @OA\Property(property="rate", type="number", format="float", example=42000.000000, description="نرخ ارز"),
     *             @OA\Property(property="effective_date", type="string", format="date", example="2026-08-28", description="تاریخ اعتبار"),
     *             @OA\Property(property="source", type="string", enum={"manual", "api", "central_bank", "exchange_market", "import"}, example="manual", description="منبع نرخ ارز")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="نرخ ارز با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="نرخ ارز برای این تاریخ و جفت ارز قبلاً ثبت شده است"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد نرخ ارز"
     *     )
     * )
     */
    public function store(ExchangeRateRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $exchangeRate = $this->exchangeRateService->create($validatedData);

            return $this->successResponse($exchangeRate, 'نرخ ارز با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد نرخ ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange-rate/{id}",
     *     summary="نمایش اطلاعات یک نرخ ارز",
     *     tags={"ExchangeRate"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نرخ ارز",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات نرخ ارز با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نرخ ارز یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات نرخ ارز"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $exchangeRate = $this->exchangeRateService->find($id);

            if (!$exchangeRate) {
                return $this->errorResponse('نرخ ارز مورد نظر یافت نشد', 404);
            }

            // دریافت اطلاعات تکمیلی
            $statistics = $this->exchangeRateService->getDetails($id);

            $data = [
                'exchange_rate' => $exchangeRate,
                'statistics' => $statistics
            ];

            return $this->successResponse($data, 'اطلاعات نرخ ارز با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات نرخ ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/exchange-rate/{id}",
     *     summary="به‌روزرسانی نرخ ارز",
     *     tags={"ExchangeRate"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نرخ ارز",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="rate", type="number", format="float", example=42500.000000, description="نرخ ارز"),
     *             @OA\Property(property="effective_date", type="string", format="date", example="2026-08-28", description="تاریخ اعتبار"),
     *             @OA\Property(property="source", type="string", enum={"manual", "api", "central_bank", "exchange_market", "import"}, example="api", description="منبع نرخ ارز")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نرخ ارز با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نرخ ارز یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی نرخ ارز"
     *     )
     * )
     */
    public function update(ExchangeRateUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $exchangeRate = $this->exchangeRateService->update($id, $validatedData);

            return $this->successResponse($exchangeRate, 'نرخ ارز با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی نرخ ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/exchange-rate/{id}",
     *     summary="حذف نرخ ارز",
     *     tags={"ExchangeRate"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نرخ ارز",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نرخ ارز با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نرخ ارز یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف نرخ ارز"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->exchangeRateService->delete($id);

            return $this->successResponse(null, 'نرخ ارز با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف نرخ ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange-rate/latest",
     *     summary="دریافت آخرین نرخ ارز",
     *     tags={"ExchangeRate"},
     *     @OA\Parameter(
     *         name="from_currency_id",
     *         in="query",
     *         description="شناسه ارز مبدا",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="to_currency_id",
     *         in="query",
     *         description="شناسه ارز مقصد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آخرین نرخ ارز با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نرخ ارزی یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آخرین نرخ ارز"
     *     )
     * )
     */
    public function getLatestRate(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'from_currency_id' => 'required|exists:currencies,id',
                'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $exchangeRate = $this->exchangeRateService->getLatestRate(
                $request->from_currency_id,
                $request->to_currency_id
            );

            if (!$exchangeRate) {
                return $this->errorResponse('نرخ ارزی برای این جفت ارز یافت نشد', 404);
            }

            return $this->successResponse($exchangeRate, 'آخرین نرخ ارز با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getLatestRate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آخرین نرخ ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange-rate/statistics",
     *     summary="دریافت آمار نرخ‌های ارز",
     *     tags={"ExchangeRate"},
     *     @OA\Parameter(
     *         name="from_currency_id",
     *         in="query",
     *         description="شناسه ارز مبدا",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="to_currency_id",
     *         in="query",
     *         description="شناسه ارز مقصد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="تاریخ شروع (فرمت: Y-m-d)",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="تاریخ پایان (فرمت: Y-m-d)",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار نرخ‌های ارز با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار نرخ‌های ارز"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'from_currency_id' => 'required|exists:currencies,id',
                'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $statistics = $this->exchangeRateService->getStatistics(
                $request->from_currency_id,
                $request->to_currency_id,
                $request->start_date,
                $request->end_date
            );

            return $this->successResponse($statistics, 'آمار نرخ‌های ارز با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار نرخ‌های ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange-rate/chart-data",
     *     summary="دریافت داده‌های نمودار نرخ ارز",
     *     tags={"ExchangeRate"},
     *     @OA\Parameter(
     *         name="from_currency_id",
     *         in="query",
     *         description="شناسه ارز مبدا",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="to_currency_id",
     *         in="query",
     *         description="شناسه ارز مقصد",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="تاریخ شروع (فرمت: Y-m-d)",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="تاریخ پایان (فرمت: Y-m-d)",
     *         required=true,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="داده‌های نمودار با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت داده‌های نمودار"
     *     )
     * )
     */
    public function chartData(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'from_currency_id' => 'required|exists:currencies,id',
                'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $chartData = $this->exchangeRateService->getChartData(
                $request->from_currency_id,
                $request->to_currency_id,
                $request->start_date,
                $request->end_date
            );

            return $this->successResponse($chartData, 'داده‌های نمودار با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in chartData: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت داده‌های نمودار: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/exchange-rate/sync",
     *     summary="همگام‌سازی گروهی نرخ‌های ارز",
     *     tags={"ExchangeRate"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rates"},
     *             @OA\Property(
     *                 property="rates",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"from_currency_id", "to_currency_id", "rate"},
     *                     @OA\Property(property="from_currency_id", type="integer", example=1),
     *                     @OA\Property(property="to_currency_id", type="integer", example=2),
     *                     @OA\Property(property="rate", type="number", format="float", example=42000.000000)
     *                 )
     *             ),
     *             @OA\Property(property="source", type="string", enum={"manual", "api", "central_bank", "exchange_market", "import"}, example="api"),
     *             @OA\Property(property="effective_date", type="string", format="date", example="2026-08-28")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="همگام‌سازی نرخ‌های ارز با موفقیت انجام شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در همگام‌سازی نرخ‌های ارز"
     *     )
     * )
     */
    public function sync(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'rates' => 'required|array|min:1',
                'rates.*.from_currency_id' => 'required|exists:currencies,id',
                'rates.*.to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
                'rates.*.rate' => 'required|numeric|min:0.000001',
                'source' => 'nullable|string|in:' . implode(',', array_keys(\Modules\System\Entities\ExchangeRate::$sources)),
                'effective_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $result = $this->exchangeRateService->syncRates(
                $request->rates,
                $request->source,
                $request->effective_date
            );

            return $this->successResponse($result, 'همگام‌سازی نرخ‌های ارز با موفقیت انجام شد');
        } catch (\Exception $ex) {
            Log::error('Error in sync: ' . $ex->getMessage());
            return $this->errorResponse('خطا در همگام‌سازی نرخ‌های ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange-rate/sources",
     *     summary="دریافت لیست منابع معتبر نرخ ارز",
     *     tags={"ExchangeRate"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست منابع با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getSources()
    {
        try {
            $sources = \Modules\System\Entities\ExchangeRate::$sources;
            return $this->successResponse($sources, 'لیست منابع با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getSources: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست منابع: ' . $ex->getMessage(), 500);
        }
    }
}
