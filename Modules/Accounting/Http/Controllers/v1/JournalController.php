<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\JournalService;
use Modules\Accounting\Http\Requests\Journal\JournalRequest;
use Modules\Accounting\Http\Requests\Journal\JournalUpdateRequest;

class JournalController extends BaseController
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * @OA\Get(
     *     path="/journal",
     *     summary="نمایش لیست دفترهای روزنامه",
     *     tags={"Journal"},
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
     *         description="جستجو بر اساس نام یا کد دفتر روزنامه",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="نوع دفتر روزنامه",
     *         required=false,
     *         @OA\Schema(type="string", enum={"general","sales","purchase","cash","bank","salary","inventory","adjustment","closing"})
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="وضعیت دفتر روزنامه",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active","inactive"})
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
     *         description="لیست دفترهای روزنامه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست دفترهای روزنامه"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $perPage = $request->get('per_page', 15);
            $search = $request->get('search', null);
            $type = $request->get('type', null);
            $status = $request->get('status', null);

            $filters = [
                'company_id' => $companyId,
                'search' => $search,
                'type' => $type,
                'status' => $status,
            ];

            $journals = $this->journalService->getPaginate($perPage, $filters);

            return $this->successResponse($journals, 'لیست دفترهای روزنامه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دفترهای روزنامه: ' . $ex->getMessage(), 500);
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
     *     path="/journal",
     *     summary="ایجاد دفتر روزنامه جدید",
     *     tags={"Journal"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "name", "type"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="code", type="string", maxLength=50, example="GEN_001", description="کد دفتر روزنامه (اختیاری - در صورت عدم وارد شدن خودکار تولید می‌شود)"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="دفتر روزنامه عمومی", description="نام دفتر روزنامه"),
     *             @OA\Property(property="type", type="string", enum={"general","sales","purchase","cash","bank","salary","inventory","adjustment","closing"}, example="general", description="نوع دفتر روزنامه"),
     *             @OA\Property(property="is_default", type="boolean", default=false, example=false, description="دفتر پیش‌فرض شرکت"),
     *             @OA\Property(property="is_active", type="boolean", default=true, example=true, description="وضعیت فعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="دفتر روزنامه با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد دفتر روزنامه"
     *     )
     * )
     */
    public function store(JournalRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // اگر کد وارد نشده، به صورت خودکار تولید می‌شود
            if (!isset($validatedData['code']) || empty($validatedData['code'])) {
                $validatedData['code'] = $this->journalService->generateCode(
                    $validatedData['name'],
                    $validatedData['company_id']
                );
            }

            $journal = $this->journalService->create($validatedData);

            return $this->successResponse($journal, 'دفتر روزنامه با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد دفتر روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/{id}",
     *     summary="نمایش اطلاعات یک دفتر روزنامه",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="id",
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
     *         name="fiscal_year_id",
     *         in="query",
     *         description="شناسه سال مالی (برای دریافت آمار سندها)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات دفتر روزنامه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات دفتر روزنامه"
     *     )
     * )
     */
    public function show($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $with = ['company', 'journalEntries'];
            $journal = $this->journalService->find($id, $companyId, $with);
            
            if (!$journal) {
                return $this->errorResponse('دفتر روزنامه مورد نظر یافت نشد', 404);
            }

            // دریافت آمار سندها
            $fiscalYearId = $request->get('fiscal_year_id');
            $statistics = $fiscalYearId 
                ? $this->journalService->getStatistics($id, $companyId, $fiscalYearId)
                : $this->journalService->getStatistics($id, $companyId);

            // دریافت سندهای اخیر
            $recentEntries = $this->journalService->getRecentEntries($id, $companyId, 10);

            $result = [
                'journal' => $journal,
                'statistics' => $statistics,
                'recent_entries' => $recentEntries,
            ];

            return $this->successResponse($result, 'اطلاعات دفتر روزنامه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات دفتر روزنامه: ' . $ex->getMessage(), 500);
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
     *     path="/journal/{id}",
     *     summary="به‌روزرسانی دفتر روزنامه",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه دفتر روزنامه",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", maxLength=50, example="GEN_001", description="کد دفتر روزنامه"),
     *             @OA\Property(property="name", type="string", maxLength=100, example="دفتر روزنامه عمومی", description="نام دفتر روزنامه"),
     *             @OA\Property(property="type", type="string", enum={"general","sales","purchase","cash","bank","salary","inventory","adjustment","closing"}, example="general", description="نوع دفتر روزنامه"),
     *             @OA\Property(property="is_default", type="boolean", example=false, description="دفتر پیش‌فرض شرکت"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="وضعیت فعال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="دفتر روزنامه با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه یافت نشد"
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
    public function update(JournalUpdateRequest $request, $id)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $validatedData = $request->validated();
            $journal = $this->journalService->update($id, $companyId, $validatedData);

            return $this->successResponse($journal, 'دفتر روزنامه با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی دفتر روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/journal/{id}",
     *     summary="حذف دفتر روزنامه",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="id",
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
     *     @OA\Response(
     *         response=200,
     *         description="دفتر روزنامه با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="دفتر روزنامه دارای سند است و قابل حذف نمی‌باشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف دفتر روزنامه"
     *     )
     * )
     */
    public function destroy($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $this->journalService->delete($id, $companyId);

            return $this->successResponse(null, 'دفتر روزنامه با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف دفتر روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal/{id}/activate",
     *     summary="فعال کردن دفتر روزنامه",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="id",
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
     *     @OA\Response(
     *         response=200,
     *         description="دفتر روزنامه با موفقیت فعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در فعال‌سازی دفتر روزنامه"
     *     )
     * )
     */
    public function activate($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journal = $this->journalService->activate($id, $companyId);

            return $this->successResponse($journal, 'دفتر روزنامه با موفقیت فعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in activate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در فعال‌سازی دفتر روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal/{id}/deactivate",
     *     summary="غیرفعال کردن دفتر روزنامه",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="id",
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
     *     @OA\Response(
     *         response=200,
     *         description="دفتر روزنامه با موفقیت غیرفعال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="دفتر روزنامه پیش‌فرض را نمی‌توان غیرفعال کرد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در غیرفعال‌سازی دفتر روزنامه"
     *     )
     * )
     */
    public function deactivate($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journal = $this->journalService->deactivate($id, $companyId);

            return $this->successResponse($journal, 'دفتر روزنامه با موفقیت غیرفعال شد');
        } catch (\Exception $ex) {
            Log::error('Error in deactivate: ' . $ex->getMessage());
            return $this->errorResponse('خطا در غیرفعال‌سازی دفتر روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal/{id}/set-default",
     *     summary="تنظیم به‌عنوان دفتر روزنامه پیش‌فرض",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="id",
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
     *     @OA\Response(
     *         response=200,
     *         description="دفتر روزنامه با موفقیت به‌عنوان پیش‌فرض تنظیم شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تنظیم دفتر روزنامه پیش‌فرض"
     *     )
     * )
     */
    public function setDefault($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journal = $this->journalService->setDefault($id, $companyId);

            return $this->successResponse($journal, 'دفتر روزنامه با موفقیت به‌عنوان پیش‌فرض تنظیم شد');
        } catch (\Exception $ex) {
            Log::error('Error in setDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تنظیم دفتر روزنامه پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/list",
     *     summary="دریافت لیست دفترهای روزنامه برای استفاده در dropdown",
     *     tags={"Journal"},
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
     *         description="فقط دفترهای فعال",
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
     *         description="لیست دفترهای روزنامه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست دفترهای روزنامه"
     *     )
     * )
     */
    public function list(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $onlyActive = $request->get('only_active', true);
            $withCode = $request->get('with_code', false);

            $journals = $this->journalService->getList($companyId, $onlyActive, $withCode);

            return $this->successResponse($journals, 'لیست دفترهای روزنامه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in list: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دفترهای روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/by-type/{type}",
     *     summary="دریافت دفترهای روزنامه بر اساس نوع",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         description="نوع دفتر روزنامه",
     *         required=true,
     *         @OA\Schema(type="string", enum={"general","sales","purchase","cash","bank","salary","inventory","adjustment","closing"})
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
     *         description="فقط دفترهای فعال",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست دفترهای روزنامه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="نوع دفتر روزنامه نامعتبر است"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست دفترهای روزنامه"
     *     )
     * )
     */
    public function getByType(Request $request, $type)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            // اعتبارسنجی type
            $validTypes = ['general', 'sales', 'purchase', 'cash', 'bank', 'salary', 'inventory', 'adjustment', 'closing'];
            if (!in_array($type, $validTypes)) {
                return $this->errorResponse('نوع دفتر روزنامه نامعتبر است', 422);
            }

            $onlyActive = $request->get('only_active', true);
            $journals = $this->journalService->getByType($companyId, $type, $onlyActive);

            return $this->successResponse($journals, 'لیست دفترهای روزنامه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByType: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دفترهای روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/default",
     *     summary="دریافت دفتر روزنامه پیش‌فرض شرکت",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="دفتر روزنامه پیش‌فرض با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دفتر روزنامه پیش‌فرض یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت دفتر روزنامه پیش‌فرض"
     *     )
     * )
     */
    public function getDefault(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journal = $this->journalService->getDefaultJournal($companyId);

            if (!$journal) {
                return $this->errorResponse('دفتر روزنامه پیش‌فرض یافت نشد', 404);
            }

            return $this->successResponse($journal, 'دفتر روزنامه پیش‌فرض با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDefault: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت دفتر روزنامه پیش‌فرض: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/statistics",
     *     summary="دریافت آمار دفترهای روزنامه یک شرکت",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار دفترهای روزنامه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار دفترهای روزنامه"
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

            $statistics = $this->journalService->getCompanyStatistics($companyId);

            return $this->successResponse($statistics, 'آمار دفترهای روزنامه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار دفترهای روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/with-entries",
     *     summary="دریافت دفترهای روزنامه دارای سند",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست دفترهای روزنامه دارای سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست دفترهای روزنامه"
     *     )
     * )
     */
    public function getWithEntries(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journals = $this->journalService->getWithEntries($companyId);

            return $this->successResponse($journals, 'لیست دفترهای روزنامه دارای سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getWithEntries: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دفترهای روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/without-entries",
     *     summary="دریافت دفترهای روزنامه بدون سند",
     *     tags={"Journal"},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="شناسه شرکت",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست دفترهای روزنامه بدون سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست دفترهای روزنامه"
     *     )
     * )
     */
    public function getWithoutEntries(Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journals = $this->journalService->getWithoutEntries($companyId);

            return $this->successResponse($journals, 'لیست دفترهای روزنامه بدون سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getWithoutEntries: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دفترهای روزنامه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal/types",
     *     summary="دریافت لیست انواع دفتر روزنامه",
     *     tags={"Journal"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع دفتر روزنامه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست انواع دفتر روزنامه"
     *     )
     * )
     */
    public function getTypes()
    {
        try {
            $types = $this->journalService->getTypes();

            return $this->successResponse($types, 'لیست انواع دفتر روزنامه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTypes: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع دفتر روزنامه: ' . $ex->getMessage(), 500);
        }
    }
}