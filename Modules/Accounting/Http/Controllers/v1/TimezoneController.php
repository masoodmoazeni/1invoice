<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Http\Requests\TimezoneRequest;
use Modules\Accounting\Services\TimezoneService;

class TimezoneController extends BaseController
{
    protected $timezoneService;

    public function __construct(TimezoneService $timezoneService)
    {
        $this->timezoneService = $timezoneService;
    }

    /**
     * @OA\Get(
     *     path="/timezone",
     *     summary="نمایش لیست مناطق زمانی",
     *     tags={"Timezone"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام منطقه زمانی", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="country_id", in="query", description="فیلتر بر اساس شناسه کشور", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="is_default", in="query", description="فیلتر بر اساس پیش‌فرض بودن", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort_by", in="query", description="فیلد مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"id","name","utc_offset","country_id","created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="ترتیب مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc")),
     *     @OA\Response(response=200, description="لیست مناطق زمانی با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست مناطق زمانی"),
     *     @OA\Response(response=500, description="خطای داخلی سرور")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only(['search', 'country_id', 'is_default', 'sort_by', 'sort_order']);
            
            $filters = array_filter($filters, function ($value) {
                return $value !== null && $value !== '';
            });

            $timezones = $this->timezoneService->getPaginate($perPage, $filters);

            return $this->successResponse($timezones, 'لیست مناطق زمانی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مناطق زمانی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/timezone",
     *     summary="ایجاد منطقه زمانی جدید",
     *     tags={"Timezone"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"country_id","name","utc_offset"}, @OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"), @OA\Property(property="name", type="string", maxLength=100, example="Asia/Tehran", description="نام منطقه زمانی"), @OA\Property(property="utc_offset", type="string", maxLength=10, example="+03:30", description="مقدار UTC Offset"), @OA\Property(property="is_default", type="boolean", example=false, description="پیش‌فرض بودن"))),
     *     @OA\Response(response=201, description="منطقه زمانی با موفقیت ایجاد شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد منطقه زمانی")
     * )
     */
    public function create(TimezoneRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $timezone = $this->timezoneService->create($validatedData);

            return $this->successResponse($timezone, 'منطقه زمانی با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in create: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد منطقه زمانی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/timezone/{id}",
     *     summary="نمایش اطلاعات یک منطقه زمانی",
     *     tags={"Timezone"},
     *     @OA\Parameter(name="id", in="path", description="شناسه منطقه زمانی", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="اطلاعات منطقه زمانی با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="منطقه زمانی یافت نشد")
     * )
     */
    public function show($id)
    {
        try {
            $timezone = $this->timezoneService->findById($id);
            
            if (!$timezone) {
                return $this->errorResponse('منطقه زمانی مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($timezone, 'اطلاعات منطقه زمانی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات منطقه زمانی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/timezone/{id}",
     *     summary="به‌روزرسانی منطقه زمانی",
     *     tags={"Timezone"},
     *     @OA\Parameter(name="id", in="path", description="شناسه منطقه زمانی", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(@OA\Property(property="country_id", type="integer", example=1, description="شناسه کشور"), @OA\Property(property="name", type="string", maxLength=100, example="Asia/Tehran", description="نام منطقه زمانی"), @OA\Property(property="utc_offset", type="string", maxLength=10, example="+03:30", description="مقدار UTC Offset"), @OA\Property(property="is_default", type="boolean", example=false, description="پیش‌فرض بودن"))),
     *     @OA\Response(response=200, description="منطقه زمانی با موفقیت به‌روزرسانی شد"),
     *     @OA\Response(response=404, description="منطقه زمانی یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            if (!$this->timezoneService->exists($id)) {
                return $this->errorResponse('منطقه زمانی مورد نظر یافت نشد', 404);
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'country_id' => 'sometimes|exists:countries,id',
                'name' => 'sometimes|string|max:100|unique:timezones,name,' . $id,
                'utc_offset' => 'sometimes|string|max:10',
                'is_default' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $timezone = $this->timezoneService->update($id, $request->all());

            return $this->successResponse($timezone, 'منطقه زمانی با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی منطقه زمانی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/timezone/{id}",
     *     summary="حذف منطقه زمانی",
     *     tags={"Timezone"},
     *     @OA\Parameter(name="id", in="path", description="شناسه منطقه زمانی", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="منطقه زمانی با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="منطقه زمانی یافت نشد")
     * )
     */
    public function destroy($id)
    {
        try {
            if (!$this->timezoneService->exists($id)) {
                return $this->errorResponse('منطقه زمانی مورد نظر یافت نشد', 404);
            }

            $this->timezoneService->delete($id);

            return $this->successResponse(null, 'منطقه زمانی با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف منطقه زمانی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/timezone/{id}/toggle-default",
     *     summary="تغییر وضعیت پیش‌فرض منطقه زمانی",
     *     tags={"Timezone"},
     *     @OA\Parameter(name="id", in="path", description="شناسه منطقه زمانی", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="وضعیت پیش‌فرض منطقه زمانی با موفقیت تغییر یافت"),
     *     @OA\Response(response=404, description="منطقه زمانی یافت نشد")
     * )
     */
    public function toggleDefault($id)
    {
        try {
            if (!$this->timezoneService->exists($id)) {
                return $this->errorResponse('منطقه زمانی مورد نظر یافت نشد', 404);
            }

            $timezone = $this->timezoneService->toggleDefault($id);

            return $this->successResponse($timezone, 'وضعیت پیش‌فرض منطقه زمانی با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in toggleDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت پیش‌فرض منطقه زمانی: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/timezone/by-country/{countryId}",
     *     summary="دریافت مناطق زمانی یک کشور",
     *     tags={"Timezone"},
     *     @OA\Parameter(name="countryId", in="path", description="شناسه کشور", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="لیست مناطق زمانی کشور با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="کشور یافت نشد")
     * )
     */
    public function getByCountry($countryId)
    {
        try {
            $timezones = $this->timezoneService->getByCountryId($countryId);
            return $this->successResponse($timezones, 'لیست مناطق زمانی کشور با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCountry: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست مناطق زمانی کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/timezone/default/{countryId}",
     *     summary="دریافت منطقه زمانی پیش‌فرض یک کشور",
     *     tags={"Timezone"},
     *     @OA\Parameter(name="countryId", in="path", description="شناسه کشور", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="منطقه زمانی پیش‌فرض کشور با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="منطقه زمانی پیش‌فرض یافت نشد")
     * )
     */
    public function getDefault($countryId)
    {
        try {
            $timezone = $this->timezoneService->getDefaultByCountryId($countryId);
            
            if (!$timezone) {
                return $this->errorResponse('منطقه زمانی پیش‌فرض برای این کشور یافت نشد', 404);
            }

            return $this->successResponse($timezone, 'منطقه زمانی پیش‌فرض کشور با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت منطقه زمانی پیش‌فرض کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/timezone/statistics",
     *     summary="دریافت آمار مناطق زمانی",
     *     tags={"Timezone"},
     *     @OA\Response(response=200, description="آمار مناطق زمانی با موفقیت دریافت شد")
     * )
     */
    public function statistics()
    {
        try {
            $statistics = $this->timezoneService->getStatistics();
            return $this->successResponse($statistics, 'آمار مناطق زمانی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار مناطق زمانی: ' . $ex->getMessage(), 500);
        }
    }
}