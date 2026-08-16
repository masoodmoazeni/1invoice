<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\LanguageService;

use Modules\Accounting\Http\Requests\Language\LanguageRequest;
use Modules\Accounting\Http\Requests\Language\LanguageUpdateRequest;

class LanguageController extends BaseController
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @OA\Get(
     *     path="/language",
     *     summary="نمایش لیست زبان‌ها",
     *     tags={"Language"},
     *     @OA\Parameter(name="page", in="query", description="شماره صفحه", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="تعداد آیتم در هر صفحه", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="search", in="query", description="جستجو بر اساس نام زبان", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="code", in="query", description="فیلتر بر اساس کد زبان", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="direction", in="query", description="فیلتر بر اساس جهت نوشتار", required=false, @OA\Schema(type="string", enum={"ltr","rtl"})),
     *     @OA\Parameter(name="is_active", in="query", description="فیلتر بر اساس وضعیت فعال", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="sort_by", in="query", description="فیلد مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"id","code","name","native_name","direction","created_at"})),
     *     @OA\Parameter(name="sort_order", in="query", description="ترتیب مرتب‌سازی", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc")),
     *     @OA\Response(response=200, description="لیست زبان‌ها با موفقیت دریافت شد"),
     *     @OA\Response(response=422, description="خطا در دریافت لیست زبان‌ها"),
     *     @OA\Response(response=500, description="خطای داخلی سرور")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = request()->get('search', null);

            $filters = [];
            if ($search) {
                $filters['search'] = $search;
            }

            $languages = $this->languageService->getPaginate($perPage, $filters);

            return $this->successResponse($languages, 'لیست زبان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست زبان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/language",
     *     summary="ایجاد زبان جدید",
     *     tags={"Language"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"code","name"}, @OA\Property(property="code", type="string", maxLength=5, example="fa", description="کد زبان"), @OA\Property(property="name", type="string", maxLength=100, example="Persian", description="نام کامل زبان به انگلیسی"), @OA\Property(property="native_name", type="string", maxLength=100, example="فارسی", nullable=true, description="نام بومی زبان"), @OA\Property(property="direction", type="string", enum={"ltr","rtl"}, default="ltr", example="rtl", description="جهت نوشتار"), @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال"))),
     *     @OA\Response(response=201, description="زبان با موفقیت ایجاد شد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی"),
     *     @OA\Response(response=500, description="خطا در ایجاد زبان")
     * )
     */
    public function create(LanguageRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $language = $this->languageService->create($validatedData);

            return $this->successResponse($language, 'زبان با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in create: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد زبان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/language/{id}",
     *     summary="نمایش اطلاعات یک زبان",
     *     tags={"Language"},
     *     @OA\Parameter(name="id", in="path", description="شناسه زبان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="اطلاعات زبان با موفقیت دریافت شد"),
     *     @OA\Response(response=404, description="زبان یافت نشد")
     * )
     */
    public function show($id)
    {
        try {
            $language = $this->languageService->find($id);
            
            if (!$language) {
                return $this->errorResponse('زبان مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($language, 'اطلاعات زبان با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات زبان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/language/{id}",
     *     summary="به‌روزرسانی زبان",
     *     tags={"Language"},
     *     @OA\Parameter(name="id", in="path", description="شناسه زبان", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(@OA\Property(property="code", type="string", maxLength=5, example="fa", description="کد زبان"), @OA\Property(property="name", type="string", maxLength=100, example="Persian", description="نام کامل زبان به انگلیسی"), @OA\Property(property="native_name", type="string", maxLength=100, example="فارسی", nullable=true, description="نام بومی زبان"), @OA\Property(property="direction", type="string", enum={"ltr","rtl"}, example="rtl", description="جهت نوشتار"), @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال/غیرفعال"))),
     *     @OA\Response(response=200, description="زبان با موفقیت به‌روزرسانی شد"),
     *     @OA\Response(response=404, description="زبان یافت نشد"),
     *     @OA\Response(response=422, description="خطا در اعتبارسنجی")
     * )
     */
    public function update(LanguageUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $language = $this->languageService->update($id, $validatedData);

            return $this->successResponse($language, 'زبان با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی زبان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/language/{id}",
     *     summary="حذف زبان",
     *     tags={"Language"},
     *     @OA\Parameter(name="id", in="path", description="شناسه زبان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="زبان با موفقیت حذف شد"),
     *     @OA\Response(response=404, description="زبان یافت نشد")
     * )
     */
    public function destroy($id)
    {
        try {
            
            $this->languageService->delete($id);

            return $this->successResponse(null, 'زبان با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف زبان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/language/{id}/toggle-status",
     *     summary="تغییر وضعیت فعال/غیرفعال زبان",
     *     tags={"Language"},
     *     @OA\Parameter(name="id", in="path", description="شناسه زبان", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="وضعیت زبان با موفقیت تغییر یافت"),
     *     @OA\Response(response=404, description="زبان یافت نشد")
     * )
     */
    public function toggleStatus($id)
    {
        try {
            

            $language = $this->languageService->toggleStatus($id);

            return $this->successResponse($language, 'وضعیت زبان با موفقیت تغییر یافت');
        } catch (\Exception $ex) {
            Log::error('Error in toggleStatus: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تغییر وضعیت زبان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/language/active",
     *     summary="دریافت تمام زبان‌های فعال",
     *     tags={"Language"},
     *     @OA\Response(response=200, description="لیست زبان‌های فعال با موفقیت دریافت شد")
     * )
     */
    public function getActive()
    {
        try {
            $languages = $this->languageService->getActiveLanguages();
            return $this->successResponse($languages, 'لیست زبان‌های فعال با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getActive: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست زبان‌های فعال: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/language/by-direction/{direction}",
     *     summary="دریافت زبان‌ها بر اساس جهت نوشتار",
     *     tags={"Language"},
     *     @OA\Parameter(name="direction", in="path", description="جهت نوشتار", required=true, @OA\Schema(type="string", enum={"ltr","rtl"})),
     *     @OA\Response(response=200, description="لیست زبان‌ها با موفقیت دریافت شد")
     * )
     */
    public function getByDirection($direction)
    {
        try {
            $languages = $this->languageService->getByDirection($direction);
            return $this->successResponse($languages, 'لیست زبان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByDirection: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست زبان‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/language/statistics",
     *     summary="دریافت آمار زبان‌ها",
     *     tags={"Language"},
     *     @OA\Response(response=200, description="آمار زبان‌ها با موفقیت دریافت شد")
     * )
     */
    public function statistics()
    {
        try {
            $statistics = $this->languageService->getStatistics();
            return $this->successResponse($statistics, 'آمار زبان‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار زبان‌ها: ' . $ex->getMessage(), 500);
        }
    }
}