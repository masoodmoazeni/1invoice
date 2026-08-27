<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\JournalEntryService;
use Modules\Accounting\Http\Requests\JournalEntry\JournalEntryRequest;
use Modules\Accounting\Http\Requests\JournalEntry\JournalEntryUpdateRequest;

class JournalEntryController extends BaseController
{
    protected $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }

    /**
     * @OA\Get(
     *     path="/journal-entry",
     *     summary="نمایش لیست سندهای حسابداری",
     *     tags={"JournalEntry"},
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
     *         description="جستجو بر اساس شماره سند یا شرح",
     *         required=false,
     *         @OA\Schema(type="string")
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
     *         description="شناسه سال مالی",
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
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="وضعیت سند",
     *         required=false,
     *         @OA\Schema(type="string", enum={"draft","pending","approved","posted","rejected","voided"})
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="تاریخ شروع (فرمت: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="تاریخ پایان (فرمت: YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست سندها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست سندها"
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
            $fiscalYearId = $request->get('fiscal_year_id', null);
            $journalId = $request->get('journal_id', null);
            $status = $request->get('status', null);
            $startDate = $request->get('start_date', null);
            $endDate = $request->get('end_date', null);

            $filters = [
                'company_id' => $companyId,
                'search' => $search,
                'fiscal_year_id' => $fiscalYearId,
                'journal_id' => $journalId,
                'status' => $status,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];

            $journalEntries = $this->journalEntryService->getPaginate($perPage, $filters);

            return $this->successResponse($journalEntries, 'لیست سندها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست سندها: ' . $ex->getMessage(), 500);
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
     *     path="/journal-entry",
     *     summary="ایجاد سند حسابداری جدید",
     *     tags={"JournalEntry"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "fiscal_year_id", "journal_id", "document_date", "currency_id", "details"},
     *             @OA\Property(property="company_id", type="integer", example=1, description="شناسه شرکت"),
     *             @OA\Property(property="fiscal_year_id", type="integer", example=1, description="شناسه سال مالی"),
     *             @OA\Property(property="journal_id", type="integer", example=1, description="شناسه دفتر روزنامه"),
     *             @OA\Property(property="reference_no", type="string", maxLength=50, example="INV-001", description="شماره مرجع"),
     *             @OA\Property(property="document_date", type="string", format="date", example="2026-01-15", description="تاریخ سند"),
     *             @OA\Property(property="posting_date", type="string", format="date", example="2026-01-15", description="تاریخ ثبت در سیستم"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="exchange_rate", type="number", format="float", example=1.0000, description="نرخ ارز"),
     *             @OA\Property(property="description", type="string", example="سند فروش کالا", description="شرح سند"),
     *             @OA\Property(
     *                 property="details",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"account_id", "debit", "credit"},
     *                     @OA\Property(property="account_id", type="integer", example=1, description="شناسه حساب"),
     *                     @OA\Property(property="debit", type="number", format="float", example=1000000, description="مبلغ بدهکار"),
     *                     @OA\Property(property="credit", type="number", format="float", example=0, description="مبلغ بستانکار"),
     *                     @OA\Property(property="description", type="string", example="شرح آیتم", description="شرح آیتم"),
     *                     @OA\Property(property="cost_center_id", type="integer", nullable=true, example=1, description="شناسه مرکز هزینه"),
     *                     @OA\Property(property="project_id", type="integer", nullable=true, example=1, description="شناسه پروژه")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="سند با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد سند"
     *     )
     * )
     */
    public function store(JournalEntryRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // تولید شماره سند خودکار
            if (!isset($validatedData['document_no']) || empty($validatedData['document_no'])) {
                $validatedData['document_no'] = $this->journalEntryService->generateDocumentNo(
                    $validatedData['company_id'],
                    $validatedData['fiscal_year_id'],
                    $validatedData['journal_id']
                );
            }

            $journalEntry = $this->journalEntryService->create($validatedData);

            return $this->successResponse($journalEntry, 'سند با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry/{id}",
     *     summary="نمایش اطلاعات یک سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *         description="اطلاعات سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات سند"
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

            $with = ['company', 'journal', 'fiscalYear', 'currency', 'creator', 'approver', 'details.account'];
            $journalEntry = $this->journalEntryService->find($id, $companyId, $with);
            
            if (!$journalEntry) {
                return $this->errorResponse('سند مورد نظر یافت نشد', 404);
            }

            return $this->successResponse($journalEntry, 'اطلاعات سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات سند: ' . $ex->getMessage(), 500);
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
     *     path="/journal-entry/{id}",
     *     summary="به‌روزرسانی سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="reference_no", type="string", maxLength=50, example="INV-001", description="شماره مرجع"),
     *             @OA\Property(property="document_date", type="string", format="date", example="2026-01-15", description="تاریخ سند"),
     *             @OA\Property(property="posting_date", type="string", format="date", example="2026-01-15", description="تاریخ ثبت در سیستم"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="exchange_rate", type="number", format="float", example=1.0000, description="نرخ ارز"),
     *             @OA\Property(property="description", type="string", example="سند فروش کالا", description="شرح سند"),
     *             @OA\Property(
     *                 property="details",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", nullable=true, example=1, description="شناسه آیتم (برای بروزرسانی)"),
     *                     @OA\Property(property="account_id", type="integer", example=1, description="شناسه حساب"),
     *                     @OA\Property(property="debit", type="number", format="float", example=1000000, description="مبلغ بدهکار"),
     *                     @OA\Property(property="credit", type="number", format="float", example=0, description="مبلغ بستانکار"),
     *                     @OA\Property(property="description", type="string", example="شرح آیتم", description="شرح آیتم"),
     *                     @OA\Property(property="cost_center_id", type="integer", nullable=true, example=1, description="شناسه مرکز هزینه"),
     *                     @OA\Property(property="project_id", type="integer", nullable=true, example=1, description="شناسه پروژه")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سند با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="سند قابل ویرایش نیست یا خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی سند"
     *     )
     * )
     */
    public function update(JournalEntryUpdateRequest $request, $id)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $validatedData = $request->validated();
            $journalEntry = $this->journalEntryService->update($id, $companyId, $validatedData);

            return $this->successResponse($journalEntry, 'سند با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/journal-entry/{id}",
     *     summary="حذف سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *         description="سند با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="سند قابل حذف نیست"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف سند"
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

            $this->journalEntryService->delete($id, $companyId);

            return $this->successResponse(null, 'سند با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal-entry/{id}/submit",
     *     summary="ارسال سند برای تایید",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *         description="سند با موفقیت برای تایید ارسال شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="سند قابل ارسال برای تایید نیست"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ارسال سند برای تایید"
     *     )
     * )
     */
    public function submit($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journalEntry = $this->journalEntryService->submit($id, $companyId);

            return $this->successResponse($journalEntry, 'سند با موفقیت برای تایید ارسال شد');
        } catch (\Exception $ex) {
            Log::error('Error in submit: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ارسال سند برای تایید: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal-entry/{id}/approve",
     *     summary="تایید سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *         description="سند با موفقیت تایید شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="سند قابل تایید نیست"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در تایید سند"
     *     )
     * )
     */
    public function approve($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $userId = $request->user()->id ?? $request->get('user_id');
            $journalEntry = $this->journalEntryService->approve($id, $companyId, $userId);

            return $this->successResponse($journalEntry, 'سند با موفقیت تایید شد');
        } catch (\Exception $ex) {
            Log::error('Error in approve: ' . $ex->getMessage());
            return $this->errorResponse('خطا در تایید سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal-entry/{id}/reject",
     *     summary="رد سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="مدارک ناقص", description="دلیل رد")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سند با موفقیت رد شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="سند قابل رد نیست"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در رد سند"
     *     )
     * )
     */
    public function reject($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $userId = $request->user()->id ?? $request->get('user_id');
            $reason = $request->get('reason', null);
            
            $journalEntry = $this->journalEntryService->reject($id, $companyId, $userId, $reason);

            return $this->successResponse($journalEntry, 'سند با موفقیت رد شد');
        } catch (\Exception $ex) {
            Log::error('Error in reject: ' . $ex->getMessage());
            return $this->errorResponse('خطا در رد سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal-entry/{id}/post",
     *     summary="ثبت نهایی سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *         description="سند با موفقیت ثبت نهایی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="سند قابل ثبت نهایی نیست"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ثبت نهایی سند"
     *     )
     * )
     */
    public function post($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $journalEntry = $this->journalEntryService->post($id, $companyId);

            return $this->successResponse($journalEntry, 'سند با موفقیت ثبت نهایی شد');
        } catch (\Exception $ex) {
            Log::error('Error in post: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ثبت نهایی سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/journal-entry/{id}/void",
     *     summary="ابطال سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="ثبت اشتباه", description="دلیل ابطال")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="سند با موفقیت باطل شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="سند قابل ابطال نیست"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ابطال سند"
     *     )
     * )
     */
    public function void($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $reason = $request->get('reason', 'ابطال شده توسط کاربر');
            $journalEntry = $this->journalEntryService->void($id, $companyId, $reason);

            return $this->successResponse($journalEntry, 'سند با موفقیت باطل شد');
        } catch (\Exception $ex) {
            Log::error('Error in void: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ابطال سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry/by-journal/{journal_id}",
     *     summary="دریافت سندهای یک دفتر روزنامه",
     *     tags={"JournalEntry"},
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
     *         name="fiscal_year_id",
     *         in="query",
     *         description="شناسه سال مالی",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست سندها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست سندها"
     *     )
     * )
     */
    public function getByJournal($journal_id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $fiscalYearId = $request->get('fiscal_year_id', null);
            $perPage = $request->get('per_page', 15);

            $journalEntries = $this->journalEntryService->getByJournal(
                $companyId,
                $journal_id,
                $fiscalYearId,
                $perPage
            );

            return $this->successResponse($journalEntries, 'لیست سندها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByJournal: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست سندها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry/by-fiscal-year/{fiscal_year_id}",
     *     summary="دریافت سندهای یک سال مالی",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="fiscal_year_id",
     *         in="path",
     *         description="شناسه سال مالی",
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
     *         description="لیست سندها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست سندها"
     *     )
     * )
     */
    public function getByFiscalYear($fiscal_year_id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $perPage = $request->get('per_page', 15);
            $status = $request->get('status', null);

            $journalEntries = $this->journalEntryService->getByFiscalYear(
                $companyId,
                $fiscal_year_id,
                $status,
                $perPage
            );

            return $this->successResponse($journalEntries, 'لیست سندها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByFiscalYear: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست سندها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry/statistics",
     *     summary="دریافت آمار سندها",
     *     tags={"JournalEntry"},
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
     *         description="شناسه سال مالی (اختیاری)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="آمار سندها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار سندها"
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

            $fiscalYearId = $request->get('fiscal_year_id', null);
            $statistics = $this->journalEntryService->getStatistics($companyId, $fiscalYearId);

            return $this->successResponse($statistics, 'آمار سندها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار سندها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry/statuses",
     *     summary="دریافت لیست وضعیت‌های سند",
     *     tags={"JournalEntry"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست وضعیت‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست وضعیت‌ها"
     *     )
     * )
     */
    public function getStatuses()
    {
        try {
            $statuses = $this->journalEntryService->getStatuses();

            return $this->successResponse($statuses, 'لیست وضعیت‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getStatuses: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست وضعیت‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry/print/{id}",
     *     summary="چاپ سند",
     *     tags={"JournalEntry"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه سند",
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
     *         description="سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت سند"
     *     )
     * )
     */
    public function print($id, Request $request)
    {
        try {
            $companyId = $request->get('company_id');
            
            if (!$companyId) {
                return $this->errorResponse('شناسه شرکت الزامی است', 422);
            }

            $with = ['company', 'journal', 'fiscalYear', 'currency', 'creator', 'approver', 'details.account'];
            $journalEntry = $this->journalEntryService->find($id, $companyId, $with);
            
            if (!$journalEntry) {
                return $this->errorResponse('سند مورد نظر یافت نشد', 404);
            }

            // در اینجا می‌توانید PDF یا View چاپ را برگردانید
            return $this->successResponse($journalEntry, 'سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in print: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت سند: ' . $ex->getMessage(), 500);
        }
    }
}