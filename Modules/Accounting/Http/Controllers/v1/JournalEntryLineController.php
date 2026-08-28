<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\JournalEntryLineService;
use Modules\Accounting\Http\Requests\JournalEntryLine\JournalEntryLineRequest;
use Modules\Accounting\Http\Requests\JournalEntryLine\JournalEntryLineUpdateRequest;

class JournalEntryLineController extends BaseController
{
    protected $journalEntryLineService;

    public function __construct(JournalEntryLineService $journalEntryLineService)
    {
        $this->journalEntryLineService = $journalEntryLineService;
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line",
     *     summary="نمایش لیست ردیف‌های سند حسابداری",
     *     tags={"JournalEntryLine"},
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
     *         description="جستجو بر اساس شرح ردیف",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="journal_entry_id",
     *         in="query",
     *         description="شناسه سند حسابداری",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="شناسه حساب مالی",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="partner_id",
     *         in="query",
     *         description="شناسه طرف حساب",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="project_id",
     *         in="query",
     *         description="شناسه پروژه",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="department_id",
     *         in="query",
     *         description="شناسه دپارتمان",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="cost_center_id",
     *         in="query",
     *         description="شناسه مرکز هزینه",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="نوع ردیف (بدهکار یا بستانکار)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"debit", "credit"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست ردیف‌های سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست ردیف‌های سند"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only([
                'search',
                'journal_entry_id',
                'account_id',
                'partner_id',
                'project_id',
                'department_id',
                'cost_center_id',
                'type'
            ]);

            // حذف فیلترهای خالی
            $filters = array_filter($filters, function($value) {
                return !is_null($value) && $value !== '';
            });

            $lines = $this->journalEntryLineService->getPaginate($perPage, $filters);

            return $this->successResponse($lines, 'لیست ردیف‌های سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست ردیف‌های سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/journal-entry-line",
     *     summary="ایجاد ردیف جدید برای سند حسابداری",
     *     tags={"JournalEntryLine"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"journal_entry_id", "account_id"},
     *             @OA\Property(property="journal_entry_id", type="integer", example=1, description="شناسه سند حسابداری"),
     *             @OA\Property(property="account_id", type="integer", example=5, description="شناسه حساب مالی"),
     *             @OA\Property(property="partner_id", type="integer", example=3, description="شناسه طرف حساب"),
     *             @OA\Property(property="project_id", type="integer", example=2, description="شناسه پروژه"),
     *             @OA\Property(property="department_id", type="integer", example=4, description="شناسه دپارتمان"),
     *             @OA\Property(property="cost_center_id", type="integer", example=6, description="شناسه مرکز هزینه"),
     *             @OA\Property(property="description", type="string", example="خرید کالا", description="شرح ردیف"),
     *             @OA\Property(property="debit", type="number", format="float", example=1000000.00, description="مبلغ بدهکار"),
     *             @OA\Property(property="credit", type="number", format="float", example=0.00, description="مبلغ بستانکار"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="exchange_rate", type="number", format="float", example=1.0000, description="نرخ ارز")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="ردیف سند با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد ردیف سند"
     *     )
     * )
     */
    public function store(JournalEntryLineRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $line = $this->journalEntryLineService->create($validatedData);

            return $this->successResponse($line, 'ردیف سند با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد ردیف سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/{id}",
     *     summary="نمایش اطلاعات یک ردیف سند",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه ردیف سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات ردیف سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ردیف سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات ردیف سند"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $line = $this->journalEntryLineService->find($id);
            
            if (!$line) {
                return $this->errorResponse('ردیف سند مورد نظر یافت نشد', 404);
            }

            // دریافت اطلاعات تکمیلی
            $details = $this->journalEntryLineService->getDetails($id);

            $data = [
                'line' => $line,
                'details' => $details
            ];

            return $this->successResponse($data, 'اطلاعات ردیف سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات ردیف سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/journal-entry-line/{id}",
     *     summary="به‌روزرسانی ردیف سند",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه ردیف سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="account_id", type="integer", example=5, description="شناسه حساب مالی"),
     *             @OA\Property(property="partner_id", type="integer", example=3, description="شناسه طرف حساب"),
     *             @OA\Property(property="project_id", type="integer", example=2, description="شناسه پروژه"),
     *             @OA\Property(property="department_id", type="integer", example=4, description="شناسه دپارتمان"),
     *             @OA\Property(property="cost_center_id", type="integer", example=6, description="شناسه مرکز هزینه"),
     *             @OA\Property(property="description", type="string", example="خرید کالا - ویرایش", description="شرح ردیف"),
     *             @OA\Property(property="debit", type="number", format="float", example=1000000.00, description="مبلغ بدهکار"),
     *             @OA\Property(property="credit", type="number", format="float", example=0.00, description="مبلغ بستانکار"),
     *             @OA\Property(property="currency_id", type="integer", example=1, description="شناسه ارز"),
     *             @OA\Property(property="exchange_rate", type="number", format="float", example=1.0000, description="نرخ ارز")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ردیف سند با موفقیت به‌روزرسانی شد"
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
     *         description="خطا در به‌روزرسانی ردیف سند"
     *     )
     * )
     */
    public function update(JournalEntryLineUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $line = $this->journalEntryLineService->update($id, $validatedData);

            return $this->successResponse($line, 'ردیف سند با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی ردیف سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/journal-entry-line/{id}",
     *     summary="حذف ردیف سند",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه ردیف سند",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ردیف سند با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ردیف سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف ردیف سند"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->journalEntryLineService->delete($id);

            return $this->successResponse(null, 'ردیف سند با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف ردیف سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/by-entry/{journal_entry_id}",
     *     summary="دریافت ردیف‌های یک سند حسابداری",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="journal_entry_id",
     *         in="path",
     *         description="شناسه سند حسابداری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های سند با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ردیف‌های سند"
     *     )
     * )
     */
    public function getByEntry($journal_entry_id)
    {
        try {
            $lines = $this->journalEntryLineService->getByEntry($journal_entry_id);

            if ($lines->isEmpty()) {
                return $this->errorResponse('هیچ ردیفی برای این سند یافت نشد', 404);
            }

            return $this->successResponse($lines, 'ردیف‌های سند با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByEntry: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌های سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/totals/{journal_entry_id}",
     *     summary="دریافت مجموع بدهکار و بستانکار یک سند",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="journal_entry_id",
     *         in="path",
     *         description="شناسه سند حسابداری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مجموع مبالغ با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت مجموع مبالغ"
     *     )
     * )
     */
    public function getTotals($journal_entry_id)
    {
        try {
            $totals = $this->journalEntryLineService->getTotals($journal_entry_id);

            return $this->successResponse($totals, 'مجموع مبالغ با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getTotals: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت مجموع مبالغ: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/by-account/{account_id}",
     *     summary="دریافت ردیف‌های یک حساب مالی",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="path",
     *         description="شناسه حساب مالی",
     *         required=true,
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
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ردیف‌های حساب"
     *     )
     * )
     */
    public function getByAccount(Request $request, $account_id)
    {
        try {
            $validator = validator($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $lines = $this->journalEntryLineService->getByAccount(
                $account_id,
                $request->start_date,
                $request->end_date
            );

            if ($lines->isEmpty()) {
                return $this->errorResponse('هیچ ردیفی برای این حساب یافت نشد', 404);
            }

            return $this->successResponse($lines, 'ردیف‌های حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByAccount: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌های حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/balance/{account_id}",
     *     summary="دریافت مانده یک حساب مالی",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="path",
     *         description="شناسه حساب مالی",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="تاریخ تا (فرمت: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="مانده حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت مانده حساب"
     *     )
     * )
     */
    public function getBalance(Request $request, $account_id)
    {
        try {
            $validator = validator($request->all(), [
                'date_to' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $balance = $this->journalEntryLineService->getAccountBalance(
                $account_id,
                $request->date_to
            );

            return $this->successResponse(['balance' => $balance], 'مانده حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getBalance: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت مانده حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/by-partner/{partner_id}",
     *     summary="دریافت ردیف‌های یک طرف حساب",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="partner_id",
     *         in="path",
     *         description="شناسه طرف حساب",
     *         required=true,
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
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های طرف حساب با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="طرف حساب یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ردیف‌های طرف حساب"
     *     )
     * )
     */
    public function getByPartner(Request $request, $partner_id)
    {
        try {
            $validator = validator($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $lines = $this->journalEntryLineService->getByPartner(
                $partner_id,
                $request->start_date,
                $request->end_date
            );

            if ($lines->isEmpty()) {
                return $this->errorResponse('هیچ ردیفی برای این طرف حساب یافت نشد', 404);
            }

            return $this->successResponse($lines, 'ردیف‌های طرف حساب با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByPartner: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌های طرف حساب: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/by-project/{project_id}",
     *     summary="دریافت ردیف‌های یک پروژه",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="project_id",
     *         in="path",
     *         description="شناسه پروژه",
     *         required=true,
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
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های پروژه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="پروژه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ردیف‌های پروژه"
     *     )
     * )
     */
    public function getByProject(Request $request, $project_id)
    {
        try {
            $validator = validator($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $lines = $this->journalEntryLineService->getByProject(
                $project_id,
                $request->start_date,
                $request->end_date
            );

            if ($lines->isEmpty()) {
                return $this->errorResponse('هیچ ردیفی برای این پروژه یافت نشد', 404);
            }

            return $this->successResponse($lines, 'ردیف‌های پروژه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByProject: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌های پروژه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/by-department/{department_id}",
     *     summary="دریافت ردیف‌های یک دپارتمان",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="department_id",
     *         in="path",
     *         description="شناسه دپارتمان",
     *         required=true,
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
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های دپارتمان با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="دپارتمان یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ردیف‌های دپارتمان"
     *     )
     * )
     */
    public function getByDepartment(Request $request, $department_id)
    {
        try {
            $validator = validator($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $lines = $this->journalEntryLineService->getByDepartment(
                $department_id,
                $request->start_date,
                $request->end_date
            );

            if ($lines->isEmpty()) {
                return $this->errorResponse('هیچ ردیفی برای این دپارتمان یافت نشد', 404);
            }

            return $this->successResponse($lines, 'ردیف‌های دپارتمان با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByDepartment: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌های دپارتمان: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/by-cost-center/{cost_center_id}",
     *     summary="دریافت ردیف‌های یک مرکز هزینه",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="cost_center_id",
     *         in="path",
     *         description="شناسه مرکز هزینه",
     *         required=true,
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
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های مرکز هزینه با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="مرکز هزینه یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت ردیف‌های مرکز هزینه"
     *     )
     * )
     */
    public function getByCostCenter(Request $request, $cost_center_id)
    {
        try {
            $validator = validator($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $lines = $this->journalEntryLineService->getByCostCenter(
                $cost_center_id,
                $request->start_date,
                $request->end_date
            );

            if ($lines->isEmpty()) {
                return $this->errorResponse('هیچ ردیفی برای این مرکز هزینه یافت نشد', 404);
            }

            return $this->successResponse($lines, 'ردیف‌های مرکز هزینه با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getByCostCenter: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت ردیف‌های مرکز هزینه: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/journal-entry-line/create-multiple",
     *     summary="ایجاد چندین ردیف برای یک سند",
     *     tags={"JournalEntryLine"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"journal_entry_id", "lines"},
     *             @OA\Property(property="journal_entry_id", type="integer", example=1, description="شناسه سند حسابداری"),
     *             @OA\Property(
     *                 property="lines",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"account_id"},
     *                     @OA\Property(property="account_id", type="integer", example=5),
     *                     @OA\Property(property="partner_id", type="integer", example=3),
     *                     @OA\Property(property="description", type="string", example="شرح ردیف"),
     *                     @OA\Property(property="debit", type="number", format="float", example=1000000.00),
     *                     @OA\Property(property="credit", type="number", format="float", example=0.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="ردیف‌های سند با موفقیت ایجاد شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در ایجاد ردیف‌های سند"
     *     )
     * )
     */
    public function createMultiple(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'journal_entry_id' => 'required|exists:journal_entries,id',
                'lines' => 'required|array|min:2',
                'lines.*.account_id' => 'required|exists:accounts,id',
                'lines.*.partner_id' => 'nullable|exists:partners,id',
                'lines.*.project_id' => 'nullable|exists:projects,id',
                'lines.*.department_id' => 'nullable|exists:departments,id',
                'lines.*.cost_center_id' => 'nullable|exists:cost_centers,id',
                'lines.*.description' => 'nullable|string',
                'lines.*.debit' => 'nullable|numeric|min:0',
                'lines.*.credit' => 'nullable|numeric|min:0',
                'lines.*.currency_id' => 'nullable|exists:currencies,id',
                'lines.*.exchange_rate' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $lines = $this->journalEntryLineService->createMultiple(
                $request->journal_entry_id,
                $request->lines
            );

            return $this->successResponse($lines, 'ردیف‌های سند با موفقیت ایجاد شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in createMultiple: ' . $ex->getMessage());
            return $this->errorResponse('خطا در ایجاد ردیف‌های سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/journal-entry-line/update-multiple/{journal_entry_id}",
     *     summary="به‌روزرسانی تمام ردیف‌های یک سند",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="journal_entry_id",
     *         in="path",
     *         description="شناسه سند حسابداری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"lines"},
     *             @OA\Property(
     *                 property="lines",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"account_id"},
     *                     @OA\Property(property="account_id", type="integer", example=5),
     *                     @OA\Property(property="partner_id", type="integer", example=3),
     *                     @OA\Property(property="description", type="string", example="شرح ردیف"),
     *                     @OA\Property(property="debit", type="number", format="float", example=1000000.00),
     *                     @OA\Property(property="credit", type="number", format="float", example=0.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ردیف‌های سند با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی ردیف‌های سند"
     *     )
     * )
     */
    public function updateMultiple(Request $request, $journal_entry_id)
    {
        try {
            $validator = validator($request->all(), [
                'lines' => 'required|array|min:2',
                'lines.*.account_id' => 'required|exists:accounts,id',
                'lines.*.partner_id' => 'nullable|exists:partners,id',
                'lines.*.project_id' => 'nullable|exists:projects,id',
                'lines.*.department_id' => 'nullable|exists:departments,id',
                'lines.*.cost_center_id' => 'nullable|exists:cost_centers,id',
                'lines.*.description' => 'nullable|string',
                'lines.*.debit' => 'nullable|numeric|min:0',
                'lines.*.credit' => 'nullable|numeric|min:0',
                'lines.*.currency_id' => 'nullable|exists:currencies,id',
                'lines.*.exchange_rate' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $lines = $this->journalEntryLineService->updateMultiple(
                $journal_entry_id,
                $request->lines
            );

            return $this->successResponse($lines, 'ردیف‌های سند با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in updateMultiple: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی ردیف‌های سند: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/journal-entry-line/validate",
     *     summary="اعتبارسنجی ردیف‌های سند",
     *     tags={"JournalEntryLine"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"lines"},
     *             @OA\Property(
     *                 property="lines",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="account_id", type="integer", example=5),
     *                     @OA\Property(property="debit", type="number", format="float", example=1000000.00),
     *                     @OA\Property(property="credit", type="number", format="float", example=0.00)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نتیجه اعتبارسنجی با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در اعتبارسنجی ردیف‌ها"
     *     )
     * )
     */
    public function validateLines(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'lines' => 'required|array|min:2',
                'lines.*.account_id' => 'required|exists:accounts,id',
                'lines.*.debit' => 'nullable|numeric|min:0',
                'lines.*.credit' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $result = $this->journalEntryLineService->validateLines($request->lines);

            return $this->successResponse($result, 'نتیجه اعتبارسنجی با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in validateLines: ' . $ex->getMessage());
            return $this->errorResponse('خطا در اعتبارسنجی ردیف‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/journal-entry-line/summary/{journal_entry_id}",
     *     summary="دریافت خلاصه ردیف‌ها بر اساس حساب",
     *     tags={"JournalEntryLine"},
     *     @OA\Parameter(
     *         name="journal_entry_id",
     *         in="path",
     *         description="شناسه سند حسابداری",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="خلاصه ردیف‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="سند یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت خلاصه ردیف‌ها"
     *     )
     * )
     */
    public function getSummary($journal_entry_id)
    {
        try {
            $summary = $this->journalEntryLineService->getSummaryByAccount($journal_entry_id);

            if (empty($summary)) {
                return $this->errorResponse('هیچ ردیفی برای این سند یافت نشد', 404);
            }

            return $this->successResponse($summary, 'خلاصه ردیف‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getSummary: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت خلاصه ردیف‌ها: ' . $ex->getMessage(), 500);
        }
    }
}