<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Http\Requests\CountryRequest;
use Modules\Accounting\Services\CountryService;

class CountryController extends BaseController
{
    protected $countryService;

    public function __construct(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * @OA\Get(
     *     path="/country",
     *     summary="نمایش لیست کشورها",
     *     tags={"Country"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام کشور", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_active", in="query", description="فیلتر بر اساس وضعیت فعال", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort_by", in="query", description="فیلد مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"id","iso2","iso3","name","capital","created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="ترتیب مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc")),
     *     @OA\Response(response=200, description="لیست کشورها با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست کشورها"),
     *     @OA\Response(response=500, description="خطای داخلی سرور")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only(['search', 'is_active', 'sort_by', 'sort_order']);
            
            // حذف فیلترهای null
            $filters = array_filter($filters, function ($value) {
                return $value !== null && $value !== '';
            });

            $countries = $this->countryService->getPaginate($perPage, $filters);

            return $this->successResponse($countries, 'لیست کشورها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست کشورها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/country",
     *     summary="ایجاد کشور جدید",
     *     tags={"Country"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"iso2", "iso3", "name"},
     *             @OA\Property(property="iso2", type="string", maxLength=2, example="IR", description="کد دو حرفی کشور"),
     *             @OA\Property(property="iso3", type="string", maxLength=3, example="IRN", description="کد سه حرفی کشور"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="ایران", description="نام کامل کشور"),
     *             @OA\Property(property="numeric_code", type="string", maxLength=10, example="+98", nullable=true, description="کد عددی کشور"),
     *             @OA\Property(property="phone_code", type="string", maxLength=10, example="+98", nullable=true, description="کد تلفن کشور"),
     *             @OA\Property(property="capital", type="string", maxLength=100, nullable=true, example="تهران", description="نام پایتخت"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201, 
     *         description="کشور با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422, 
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500, 
     *         description="خطا در ایجاد کشور"
     *     )
     * )
     */
    public function create(CountryRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $country = $this->countryService->create($validatedData);

            return $this->successResponse($country, 'کشور با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in create: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/country/{id}",
     *     summary="نمایش اطلاعات یک کشور",
     *     tags={"Country"},
     *     @OA\Parameter(name="id", in="path", description="شناسه کشور", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات کشور با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="کشور یافت نشد"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $country = $this->countryService->findById($id);
            
            if (!$country) {
                return $this->errorResponse('کشور مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($country, 'اطلاعات کشور با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/country/{id}",
     *     summary="به‌روزرسانی کشور",
     *     tags={"Country"},
     *     @OA\Parameter(name="id", in="path", description="شناسه کشور", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="iso2", type="string", maxLength=2, example="IR", description="کد دو حرفی کشور"),
     *             @OA\Property(property="iso3", type="string", maxLength=3, example="IRN", description="کد سه حرفی کشور"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="ایران اسلامی", description="نام کامل کشور"),
     *             @OA\Property(property="numeric_code", type="string", maxLength=10, example="+98", nullable=true, description="کد عددی کشور"),
     *             @OA\Property(property="phone_code", type="string", maxLength=10, example="+98", nullable=true, description="کد تلفن کشور"),
     *             @OA\Property(property="capital", type="string", maxLength=100, nullable=true, example="تهران", description="نام پایتخت"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="کشور با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="کشور یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            // بررسی وجود کشور
            if (!$this->countryService->exists($id)) {
                return $this->errorResponse('کشور مورد نظر یافت نشد', 404);
            }

            // اعتبارسنجی داده‌ها
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'iso2' => 'sometimes|string|max:2|unique:countries,iso2,' . $id,
                'iso3' => 'sometimes|string|max:3|unique:countries,iso3,' . $id,
                'name' => 'sometimes|string|max:100',
                'numeric_code' => 'nullable|string|max:10',
                'phone_code' => 'nullable|string|max:10',
                'capital' => 'nullable|string|max:100',
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $country = $this->countryService->update($id, $request->all());

            return $this->successResponse($country, 'کشور با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/country/{id}",
     *     summary="حذف کشور",
     *     tags={"Country"},
     *     @OA\Parameter(name="id", in="path", description="شناسه کشور", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="کشور با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="کشور یافت نشد")
     * )
     */
    public function destroy($id)
    {
        try {
            if (!$this->countryService->exists($id)) {
                return $this->errorResponse('کشور مورد نظر یافت نشد', 404);
            }

            $this->countryService->delete($id);

            return $this->successResponse(null, 'کشور با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/country/{id}/toggle-status",
     *     summary="تغییر وضعیت فعال/غیرفعال کشور",
     *     tags={"Country"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه کشور",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="وضعیت کشور با موفقیت تغییر یافت"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="کشور یافت نشد"
     *     )
     * )
     */
    public function toggleStatus($id)
    {
        try {
            if (!$this->countryService->exists($id)) {
                return $this->errorResponse('کشور مورد نظر یافت نشد', 404);
            }

            $country = $this->countryService->toggleStatus($id);

            return $this->successResponse($country, 'وضعیت کشور با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in toggleStatus: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت کشور: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/country/active",
     *     summary="دریافت تمام کشورهای فعال",
     *     tags={"Country"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست کشورهای فعال با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getActive()
    {
        try {
            $countries = $this->countryService->getActiveCountries();
            return $this->successResponse($countries, 'لیست کشورهای فعال با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getActive: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست کشورهای فعال: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/country/statistics",
     *     summary="دریافت آمار کشورها",
     *     tags={"Country"},
     *     @OA\Response(
     *         response=200,
     *         description="آمار کشورها با موفقیت دریافت شد"
     *     )
     * )
     */
    public function statistics()
    {
        try {
            $statistics = $this->countryService->getStatistics();
            return $this->successResponse($statistics, 'آمار کشورها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار کشورها: ' . $ex->getMessage(), 500);
        }
    }
}