<?php

namespace Modules\Accounting\Http\Controllers\v1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Http\Requests\Currency\CurrencyRequest;
use Modules\Accounting\Http\Requests\Currency\CurrencyUpdateRequest;
use Modules\System\Services\CurrencyService;

class CurrencyController extends BaseController
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

        /**
     * @OA\Get(
     *     path="/currency",
     *     summary="نمایش لیست ارزها",
     *     tags={"Currency"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام، کد یا نماد ارز", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="code", in="query", description="فیلتر بر اساس کد ارز", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_active", in="query", description="فیلتر بر اساس وضعیت فعال", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="decimal_places", in="query", description="فیلتر بر اساس تعداد ارقام اعشاری", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort_by", in="query", description="فیلد مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"id","code","name","decimal_places","is_active","created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="ترتیب مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc")),
     *     @OA\Response(response=200, description="لیست ارزها با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست ارزها"),
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

            if ($request->has('code')) {
                $filters['code'] = $request->get('code');
            }

            if ($request->has('is_active')) {
                $filters['is_active'] = $request->get('is_active');
            }

            if ($request->has('decimal_places')) {
                $filters['decimal_places'] = $request->get('decimal_places');
            }

            if ($request->has('sort_by')) {
                $filters['sort_by'] = $request->get('sort_by');
            }


            $currencies = $this->currencyService->getPaginate($perPage, $filters);

            return $this->successResponse($currencies, 'لیست ارزها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ارزها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/currency",
     *     summary="ایجاد ارز جدید",
     *     tags={"Currency"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name"},
     *             @OA\Property(property="code", type="string", maxLength=5, example="USD", description="کد ارز بر اساس ISO 4217"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="US Dollar", description="نام کامل ارز به انگلیسی"),
     *             @OA\Property(property="symbol", type="string", maxLength=10, example="$", nullable=true, description="نماد ارز"),
     *             @OA\Property(property="decimal_places", type="integer", example=2, default=2, description="تعداد ارقام اعشاری"),
     *             @OA\Property(property="rounding", type="number", format="float", example=0.01, default=0.01, description="مقدار گرد کردن"),
     *             @OA\Property(property="is_active", type="boolean", example=true, default=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=201, description="ارز با موفقیت ایجاد شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد ارز")
     * )
     */
    public function store(CurrencyRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // تبدیل کد به حروف بزرگ
            if (isset($validatedData['code'])) {
                $validatedData['code'] = strtoupper($validatedData['code']);
            }

            $currency = $this->currencyService->create($validatedData);

            return $this->successResponse($currency, 'ارز با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency/{id}",
     *     summary="نمایش اطلاعات یک ارز",
     *     tags={"Currency"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ارز", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="اطلاعات ارز با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="ارز یافت نشد")
     * )
     */
    public function show($id)
    {
        try {
            $currency = $this->currencyService->find($id);

            if (!$currency) {
                return $this->errorResponse('ارز مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($currency, 'اطلاعات ارز با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/currency/{id}",
     *     summary="به‌روزرسانی ارز",
     *     tags={"Currency"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ارز", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=5, example="USD", description="کد ارز بر اساس ISO 4217"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="US Dollar", description="نام کامل ارز به انگلیسی"),
     *             @OA\Property(property="symbol", type="string", maxLength=10, example="$", nullable=true, description="نماد ارز"),
     *             @OA\Property(property="decimal_places", type="integer", example=2, description="تعداد ارقام اعشاری"),
     *             @OA\Property(property="rounding", type="number", format="float", example=0.01, description="مقدار گرد کردن"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(response=200, description="ارز با موفقیت به‌روزرسانی شد"),
     *     @OA\Response(response=404, description="ارز یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function update(CurrencyUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            // تبدیل کد به حروف بزرگ
            if (isset($validatedData['code'])) {
                $validatedData['code'] = strtoupper($validatedData['code']);
            }

            $currency = $this->currencyService->update($id, $validatedData);

            return $this->successResponse($currency, 'ارز با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/currency/{id}",
     *     summary="حذف ارز",
     *     tags={"Currency"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ارز", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="ارز با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="ارز یافت نشد")
     * )
     */
    public function destroy($id)
    {
        try {
            // بررسی می‌کنیم که آیا ارز در حال استفاده است یا خیر
            $isUsed = $this->currencyService->isCurrencyInUse($id);

            if ($isUsed) {
                return $this->errorResponse('این ارز در حال استفاده است و قابل حذف نمی‌باشد', 422);
            }

            $this->currencyService->delete($id);

            return $this->successResponse(null, 'ارز با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/currency/{id}/toggle-status",
     *     summary="تغییر وضعیت فعال/غیرفعال ارز",
     *     tags={"Currency"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ارز", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="وضعیت ارز با موفقیت تغییر یافت"),
     *     @OA\Response(response=404, description="ارز یافت نشد")
     * )
     */
    public function toggleStatus($id)
    {
        try {
            $currency = $this->currencyService->toggleStatus($id);

            return $this->successResponse($currency, 'وضعیت ارز با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@toggleStatus: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency/active",
     *     summary="دریافت تمام ارزهای فعال",
     *     tags={"Currency"},
     *     @OA\Response(response=200, description="لیست ارزهای فعال با موفقیت دریافت شد")
     * )
     */
    public function getActive()
    {
        try {
            $currencies = $this->currencyService->getActiveCurrencies();

            return $this->successResponse($currencies, 'لیست ارزهای فعال با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@getActive: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ارزهای فعال: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency/default",
     *     summary="دریافت ارز پیش‌فرض",
     *     tags={"Currency"},
     *     @OA\Response(response=200, description="ارز پیش‌فرض با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="ارز پیش‌فرضی یافت نشد")
     * )
     */
    public function getDefault()
    {
        try {
            $currency = $this->currencyService->getDefaultCurrency();

            if (!$currency) {
                return $this->errorResponse('ارز پیش‌فرضی یافت نشد', 404);
            }

            return $this->successResponse($currency, 'ارز پیش‌فرض با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@getDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ارز پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency/list",
     *     summary="دریافت لیست ساده ارزها برای استفاده در dropdown",
     *     tags={"Currency"},
     *     @OA\Parameter(name="only_active", in="query", description="فقط ارزهای فعال", required=false, @OA\Schema(type="boolean", default=true)),
     *     @OA\Parameter(name="with_code", in="query", description="استفاده از کد به عنوان کلید", required=false, @OA\Schema(type="boolean", default=false)),
     *     @OA\Response(response=200, description="لیست ارزها با موفقیت دریافت شد")
     * )
     */
    public function getList(Request $request)
    {
        try {
            $onlyActive = $request->get('only_active', true);
            $withCode = $request->get('with_code', false);

            $currencies = $this->currencyService->getList($onlyActive, $withCode);

            return $this->successResponse($currencies, 'لیست ارزها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ارزها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency/by-code/{code}",
     *     summary="دریافت اطلاعات ارز بر اساس کد",
     *     tags={"Currency"},
     *     @OA\Parameter(name="code", in="path", description="کد ارز", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="اطلاعات ارز با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="ارز یافت نشد")
     * )
     */
    public function getByCode($code)
    {
        try {
            $currency = $this->currencyService->getByCode(strtoupper($code));

            if (!$currency) {
                return $this->errorResponse('ارز مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($currency, 'اطلاعات ارز با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@getByCode: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات ارز: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency/decimal-places/{places}",
     *     summary="دریافت ارزها بر اساس تعداد ارقام اعشاری",
     *     tags={"Currency"},
     *     @OA\Parameter(name="places", in="path", description="تعداد ارقام اعشاری", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="لیست ارزها با موفقیت دریافت شد")
     * )
     */
    public function getByDecimalPlaces($places, Request $request)
    {
        try {
            $onlyActive = $request->get('only_active', true);
            $currencies = $this->currencyService->getByDecimalPlaces($places, $onlyActive);

            return $this->successResponse($currencies, 'لیست ارزها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@getByDecimalPlaces: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ارزها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/currency/statistics",
     *     summary="دریافت آمار ارزها",
     *     tags={"Currency"},
     *     @OA\Response(response=200, description="آمار ارزها با موفقیت دریافت شد")
     * )
     */
    public function statistics()
    {
        try {
            $statistics = $this->currencyService->getStatistics();

            return $this->successResponse($statistics, 'آمار ارزها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار ارزها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/currency/{id}/format",
     *     summary="فرمت کردن مبلغ بر اساس ارز",
     *     tags={"Currency"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ارز", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example=1234.56, description="مبلغ مورد نظر برای فرمت"),
     *             @OA\Property(property="with_symbol", type="boolean", example=true, default=true, description="نمایش با نماد ارز")
     *         )
     *     ),
     *     @OA\Response(response=200, description="مبلغ با موفقیت فرمت شد"),
     *     @OA\Response(response=404, description="ارز یافت نشد")
     * )
     */
    public function formatAmount(Request $request, $id)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric',
                'with_symbol' => 'boolean',
            ]);

            $currency = $this->currencyService->find($id);

            if (!$currency) {
                return $this->errorResponse('ارز مورد نظر یافت نشد', 404);
            }

            $formatted = $this->currencyService->formatAmount(
                $currency,
                $request->amount,
                $request->get('with_symbol', true)
            );

            return $this->successResponse([
                'original' => $request->amount,
                'formatted' => $formatted,
                'currency' => $currency->code,
            ], 'مبلغ با موفقیت فرمت شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@formatAmount: ' . $ex->getMessage());
            return $this->errorResponse('خطا در فرمت کردن مبلغ: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/currency/{id}/round",
     *     summary="گرد کردن مبلغ بر اساس ارز",
     *     tags={"Currency"},
     *     @OA\Parameter(name="id", in="path", description="شناسه ارز", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="number", format="float", example=1234.567, description="مبلغ مورد نظر برای گرد کردن")
     *         )
     *     ),
     *     @OA\Response(response=200, description="مبلغ با موفقیت گرد شد"),
     *     @OA\Response(response=404, description="ارز یافت نشد")
     * )
     */
    public function roundAmount(Request $request, $id)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric',
            ]);

            $currency = $this->currencyService->find($id);

            if (!$currency) {
                return $this->errorResponse('ارز مورد نظر یافت نشد', 404);
            }

            $rounded = $this->currencyService->roundAmount($currency, $request->amount);

            return $this->successResponse([
                'original' => $request->amount,
                'rounded' => $rounded,
                'currency' => $currency->code,
                'rounding_rule' => $currency->rounding,
            ], 'مبلغ با موفقیت گرد شد');
        } catch (\Exception $ex) {
            Log::error('Error in CurrencyController@roundAmount: ' . $ex->getMessage());
            return $this->errorResponse('خطا در گرد کردن مبلغ: ' . $ex->getMessage(), 500);
        }
    }
}
