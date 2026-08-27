<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\DocumentTypeService;

//use Modules\Accounting\Http\Requests\DocumentType\DocumentTypeRequest;
//use Modules\Accounting\Http\Requests\DocumentType\DocumentTypeUpdateRequest;

class DocumentTypeController extends BaseController
{
    protected $documentTypService;

    public function __construct(DocumentTypeService $documentTypService)
    {
        $this->documentTypService = $documentTypService;
    }

    /**
     * @OA\Get(
     *     path="/document-types",
     *     summary="نمایش لیست انواع اسناد",
     *     tags={"DocumentTypes"},
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
     *         description="جستجو بر اساس نام یا کد",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="وضعیت (active/inactive)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="journal_id",
     *         in="query",
     *         description="شناسه دفتر روزنامه",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست انواع اسناد"
     *     )
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

            if ($request->has('status')) {
                $filters['status'] = $request->get('status');
            }

            if ($request->has('company_id')) {
                $filters['company_id'] = $request->get('company_id');
            }

            if ($request->has('journal_id')) {
                $filters['journal_id'] = $request->get('journal_id');
            }

            $documentTypes = $this->documentTypService->getPaginate($perPage, $filters);

            return $this->successResponse($documentTypes, 'لیست انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/document-types",
     *     summary="ایجاد نوع سند جدید",
     *     tags={"DocumentTypes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "code", "name"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="SINV", description="کد نوع سند"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="فاکتور فروش", description="نام نوع سند"),
     *             @OA\Property(property="journal_id", type="integer", example=1, description="شناسه دفتر روزنامه"),
     *             @OA\Property(property="number_sequence_id", type="integer", example=1, description="شناسه شماره‌گذاری"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="نوع سند با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد نوع سند"
     *     )
     * )
     */
    public function store(DocumentTypRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $documentType = $this->documentTypService->create($validatedData);

            return $this->successResponse($documentType, 'نوع سند با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد نوع سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/{id}",
     *     summary="نمایش اطلاعات یک نوع سند",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نوع سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات نوع سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات نوع سند"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $documentType = $this->documentTypService->find($id);

            if (!$documentType) {
                return $this->errorResponse('نوع سند مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($documentType, 'اطلاعات نوع سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات نوع سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/document-types/{id}",
     *     summary="به‌روزرسانی نوع سند",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نوع سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="SINV", description="کد نوع سند"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="فاکتور فروش", description="نام نوع سند"),
     *             @OA\Property(property="journal_id", type="integer", example=1, description="شناسه دفتر روزنامه"),
     *             @OA\Property(property="number_sequence_id", type="integer", example=1, description="شناسه شماره‌گذاری"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نوع سند با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی نوع سند"
     *     )
     * )
     */
    public function update(DocumentTypUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $documentType = $this->documentTypService->update($id, $validatedData);

            return $this->successResponse($documentType, 'نوع سند با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی نوع سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/document-types/{id}",
     *     summary="حذف نوع سند",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نوع سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نوع سند با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف نوع سند"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->documentTypService->delete($id);
            return $this->successResponse(null, 'نوع سند با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف نوع سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/by-company/{company_id}",
     *     summary="دریافت انواع اسناد بر اساس شرکت",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="path",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="فقط انواع اسناد فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شرکت یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست انواع اسناد"
     *     )
     * )
     */
    public function getByCompany($company_id, Request $request)
    {
        try {
            $onlyActive = $request->get('only_active', true);
            $documentTypes = $this->documentTypService->getByCompany($company_id, $onlyActive);

            if ($documentTypes->isEmpty()) {
                return $this->errorResponse('هیچ نوع سندی برای این شرکت یافت نشد', 404);
            }

            return $this->successResponse($documentTypes, 'لیست انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@getByCompany: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/by-journal/{journal_id}",
     *     summary="دریافت انواع اسناد بر اساس دفتر روزنامه",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="journal_id",
     *         in="path",
     *         description="شناسه دفتر روزنامه",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="فقط انواع اسناد فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست انواع اسناد"
     *     )
     * )
     */
    public function getByJournal($journal_id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $onlyActive = $request->get('only_active', true);

            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $documentTypes = $this->documentTypService->getByJournal($companyId, $journal_id, $onlyActive);

            if ($documentTypes->isEmpty()) {
                return $this->errorResponse('هیچ نوع سندی برای این دفتر روزنامه یافت نشد', 404);
            }

            return $this->successResponse($documentTypes, 'لیست انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@getByJournal: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/by-sequence/{sequence_id}",
     *     summary="دریافت انواع اسناد بر اساس شماره‌گذاری",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="sequence_id",
     *         in="path",
     *         description="شناسه شماره‌گذاری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="فقط انواع اسناد فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="شماره‌گذاری یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست انواع اسناد"
     *     )
     * )
     */
    public function getByNumberSequence($sequence_id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $onlyActive = $request->get('only_active', true);

            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $documentTypes = $this->documentTypService->getByNumberSequence($companyId, $sequence_id, $onlyActive);

            if ($documentTypes->isEmpty()) {
                return $this->errorResponse('هیچ نوع سندی برای این شماره‌گذاری یافت نشد', 404);
            }

            return $this->successResponse($documentTypes, 'لیست انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@getByNumberSequence: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/document-types/{id}/activate",
     *     summary="فعال کردن نوع سند",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نوع سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نوع سند با موفقیت فعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در فعال کردن نوع سند"
     *     )
     * )
     */
    public function activate($id)
    {
        try {
            $documentType = $this->documentTypService->activate($id);
            return $this->successResponse($documentType, 'نوع سند با موفقیت فعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@activate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در فعال کردن نوع سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/document-types/{id}/deactivate",
     *     summary="غیرفعال کردن نوع سند",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه نوع سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نوع سند با موفقیت غیرفعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نوع سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در غیرفعال کردن نوع سند"
     *     )
     * )
     */
    public function deactivate($id)
    {
        try {
            $documentType = $this->documentTypService->deactivate($id);
            return $this->successResponse($documentType, 'نوع سند با موفقیت غیرفعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@deactivate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در غیرفعال کردن نوع سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/statistics",
     *     summary="دریافت آمار انواع اسناد",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار انواع اسناد"
     *     )
     * )
     */
    public function statistics(Request $request)
    {
        try {
            $companyId = $request->get('company_id');

            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $statistics = $this->documentTypService->getStatistics($companyId);
            return $this->successResponse($statistics, 'آمار انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/list",
     *     summary="دریافت لیست انواع اسناد برای Dropdown",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="only_active",
     *         in="query",
     *         description="فقط انواع اسناد فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="with_code",
     *         in="query",
     *         description="نمایش با کد",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست انواع اسناد"
     *     )
     * )
     */
    public function getList(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $onlyActive = $request->get('only_active', true);
            $withCode = $request->get('with_code', false);

            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $list = $this->documentTypService->getList($companyId, $onlyActive, $withCode);
            return $this->successResponse($list, 'لیست انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@getList: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/document-types/create-default",
     *     summary="ایجاد انواع اسناد پیش‌فرض",
     *     tags={"DocumentTypes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="default_journal_id", type="integer", example=1, description="شناسه دفتر روزنامه پیش‌فرض"),
     *             @OA\Property(property="default_sequence_id", type="integer", example=1, description="شناسه شماره‌گذاری پیش‌فرض")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="انواع اسناد پیش‌فرض با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد انواع اسناد پیش‌فرض"
     *     )
     * )
     */
    public function createDefault(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $defaultJournalId = $request->get('default_journal_id', null);
            $defaultSequenceId = $request->get('default_sequence_id', null);

            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $documentTypes = $this->documentTypService->createDefaultTypes($companyId, $defaultJournalId, $defaultSequenceId);
            return $this->successResponse($documentTypes, 'انواع اسناد پیش‌فرض با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@createDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد انواع اسناد پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/with-entries",
     *     summary="دریافت انواع اسناد به همراه تعداد اسناد ثبت شده",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست انواع اسناد"
     *     )
     * )
     */
    public function getWithEntriesCount(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $onlyActive = $request->get('only_active', true);

            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $documentTypes = $this->documentTypService->getWithEntriesCount($companyId, $onlyActive);
            return $this->successResponse($documentTypes, 'لیست انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@getWithEntriesCount: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/document-types/bulk-update-journal",
     *     summary="به‌روزرسانی دفتر روزنامه برای گروهی از انواع اسناد",
     *     tags={"DocumentTypes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "old_journal_id", "new_journal_id"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="old_journal_id", type="integer", example=1, description="شناسه دفتر روزنامه قدیم"),
     *             @OA\Property(property="new_journal_id", type="integer", example=2, description="شناسه دفتر روزنامه جدید")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="دفتر روزنامه با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی دفتر روزنامه"
     *     )
     * )
     */
    public function bulkUpdateJournal(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $oldJournalId = $request->get('old_journal_id');
            $newJournalId = $request->get('new_journal_id');

            if (!$companyId || !$oldJournalId || !$newJournalId) {
                return $this->errorResponse('همه فیلدهای الزامی باید پر شوند', 422);
            }

            $updated = $this->documentTypService->updateJournal($companyId, $oldJournalId, $newJournalId);
            return $this->successResponse(['updated' => $updated], 'دفتر روزنامه با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@bulkUpdateJournal: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی دفتر روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/document-types/bulk-update-sequence",
     *     summary="به‌روزرسانی شماره‌گذاری برای گروهی از انواع اسناد",
     *     tags={"DocumentTypes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "old_sequence_id", "new_sequence_id"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="old_sequence_id", type="integer", example=1, description="شناسه شماره‌گذاری قدیم"),
     *             @OA\Property(property="new_sequence_id", type="integer", example=2, description="شناسه شماره‌گذاری جدید")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="شماره‌گذاری با موفقیت به‌روزرسانی شد"
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
    public function bulkUpdateNumberSequence(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $oldSequenceId = $request->get('old_sequence_id');
            $newSequenceId = $request->get('new_sequence_id');

            if (!$companyId || !$oldSequenceId || !$newSequenceId) {
                return $this->errorResponse('همه فیلدهای الزامی باید پر شوند', 422);
            }

            $updated = $this->documentTypService->updateNumberSequence($companyId, $oldSequenceId, $newSequenceId);
            return $this->successResponse(['updated' => $updated], 'شماره‌گذاری با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@bulkUpdateNumberSequence: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی شماره‌گذاری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/document-types/report",
     *     summary="گزارش انواع اسناد",
     *     tags={"DocumentTypes"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="وضعیت (active/inactive)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="جستجو بر اساس نام یا کد",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="گزارش انواع اسناد با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت گزارش انواع اسناد"
     *     )
     * )
     */
    public function getReport(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            $filters = [];

            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            if ($request->has('status')) {
                $filters['status'] = $request->get('status');
            }

            if ($request->has('search')) {
                $filters['search'] = $request->get('search');
            }

            if ($request->has('journal_id')) {
                $filters['journal_id'] = $request->get('journal_id');
            }

            $report = $this->documentTypService->getReport($companyId, $filters);
            return $this->successResponse($report, 'گزارش انواع اسناد با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in DocumentTypController@getReport: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت گزارش انواع اسناد: ' . $ex->getMessage(), 500);
        }
    }
}
