<?php

namespace Modules\Accounting\Entities;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\System\Entities\Currency;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     *
     * @var string
     */
    protected $table = 'journal_entries';

    /**
     * فیلدهای قابل پر کردن (Mass Assignment)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'journal_id',
        'fiscal_year_id',
        'document_no',
        'reference_no',
        'document_date',
        'posting_date',
        'status',
        'currency_id',
        'exchange_rate',
        'description',
        'total_debit',
        'total_credit',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * فیلدهایی که باید به تاریخ تبدیل شوند
     *
     * @var array<int, string>
     */
    protected $casts = [
        'document_date' => 'date',
        'posting_date' => 'date',
        'approved_at' => 'datetime',
        'exchange_rate' => 'decimal:4',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * مقادیر پیش‌فرض برای فیلدها
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'draft',
        'exchange_rate' => 1,
        'total_debit' => 0,
        'total_credit' => 0,
    ];

    /**
     * ثابت‌های وضعیت سند
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_POSTED = 'posted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_VOIDED = 'voided';

    /**
     * لیست وضعیت‌های معتبر
     *
     * @var array
     */
    public static $statuses = [
        self::STATUS_DRAFT,
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_POSTED,
        self::STATUS_REJECTED,
        self::STATUS_VOIDED,
    ];

    /**
     * آیا سند پیش‌نویس است؟
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * آیا سند در انتظار تایید است؟
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * آیا سند تایید شده است؟
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * آیا سند ثبت نهایی شده است؟
     *
     * @return bool
     */
    public function isPosted(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    /**
     * آیا سند قابل ویرایش است؟
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_PENDING,
            self::STATUS_REJECTED,
        ]);
    }

    /**
     * آیا سند قابل تایید است؟
     *
     * @return bool
     */
    public function isApprovable(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * آیا سند قابل ثبت نهایی است؟
     *
     * @return bool
     */
    public function isPostable(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    // ==================== روابط (Relationships) ====================

    /**
     * رابطه با مدل Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * رابطه با مدل Journal (دفتر روزنامه)
     */
    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * رابطه با مدل FiscalYear (سال مالی)
     */
    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    /**
     * رابطه با مدل Currency (ارز)
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * رابطه با مدل User برای ایجاد کننده
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * رابطه با مدل User برای تایید کننده
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * رابطه با مدل JournalEntryDetail (آیتم‌های سند)
     */
    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class);
    }

    // ==================== Scope ها (Query Scopes) ====================

    /**
     * Scope برای فیلتر بر اساس شرکت
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope برای فیلتر بر اساس سال مالی
     */
    public function scopeForFiscalYear($query, $fiscalYearId)
    {
        return $query->where('fiscal_year_id', $fiscalYearId);
    }

    /**
     * Scope برای فیلتر بر اساس وضعیت
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope برای فیلتر بر اساس بازه تاریخ
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('document_date', [$startDate, $endDate]);
    }

    /**
     * Scope برای سندهای قابل ویرایش
     */
    public function scopeEditable($query)
    {
        return $query->whereIn('status', [
            self::STATUS_DRAFT,
            self::STATUS_PENDING,
            self::STATUS_REJECTED,
        ]);
    }

    /**
     * Scope برای سندهای تایید شده
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope برای سندهای ثبت نهایی شده
     */
    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }
}
