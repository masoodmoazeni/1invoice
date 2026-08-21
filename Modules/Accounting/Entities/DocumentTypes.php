<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class DocumentTypes extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'document_types';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'journal_id',
        'number_sequence_id',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند
     * @var array
     */
    protected $hidden = [];

    // ========== ثابت‌های مربوط به انواع اسناد پیش‌فرض ==========

    /**
     * انواع اسناد پیش‌فرض در سیستم حسابداری
     */
    const TYPE_SALES_INVOICE = 'sales_invoice';
    const TYPE_PURCHASE_INVOICE = 'purchase_invoice';
    const TYPE_RECEIPT = 'receipt';
    const TYPE_PAYMENT = 'payment';
    const TYPE_BANK_DEPOSIT = 'bank_deposit';
    const TYPE_BANK_WITHDRAWAL = 'bank_withdrawal';
    const TYPE_SALARY = 'salary';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_CLOSING = 'closing';
    const TYPE_OPENING_BALANCE = 'opening_balance';
    const TYPE_CREDIT_NOTE = 'credit_note';
    const TYPE_DEBIT_NOTE = 'debit_note';

    /**
     * لیست انواع اسناد با برچسب فارسی
     * @var array
     */
    public static $typeLabels = [
        self::TYPE_SALES_INVOICE => 'فاکتور فروش',
        self::TYPE_PURCHASE_INVOICE => 'فاکتور خرید',
        self::TYPE_RECEIPT => 'دریافت نقدی',
        self::TYPE_PAYMENT => 'پرداخت نقدی',
        self::TYPE_BANK_DEPOSIT => 'واریز به بانک',
        self::TYPE_BANK_WITHDRAWAL => 'برداشت از بانک',
        self::TYPE_SALARY => 'حقوق و دستمزد',
        self::TYPE_ADJUSTMENT => 'سند تعدیلی',
        self::TYPE_CLOSING => 'سند اختتامیه',
        self::TYPE_OPENING_BALANCE => 'مانده افتتاحیه',
        self::TYPE_CREDIT_NOTE => 'یادداشت بستانکار',
        self::TYPE_DEBIT_NOTE => 'یادداشت بدهکار',
    ];

    /**
     * لیست انواع اسناد با شماره‌گذاری پیش‌فرض
     * @var array
     */
    public static $defaultNumberSequences = [
        self::TYPE_SALES_INVOICE => 'SINV-{year}-{number}',
        self::TYPE_PURCHASE_INVOICE => 'PINV-{year}-{number}',
        self::TYPE_RECEIPT => 'REC-{year}-{number}',
        self::TYPE_PAYMENT => 'PAY-{year}-{number}',
        self::TYPE_BANK_DEPOSIT => 'BDEP-{year}-{number}',
        self::TYPE_BANK_WITHDRAWAL => 'BWIT-{year}-{number}',
        self::TYPE_SALARY => 'SAL-{year}-{number}',
        self::TYPE_ADJUSTMENT => 'ADJ-{year}-{number}',
        self::TYPE_CLOSING => 'CLS-{year}-{number}',
        self::TYPE_OPENING_BALANCE => 'OPB-{year}-{number}',
        self::TYPE_CREDIT_NOTE => 'CRN-{year}-{number}',
        self::TYPE_DEBIT_NOTE => 'DBN-{year}-{number}',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\DocumentTypesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\DocumentTypesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر نوع سند متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsTo با مدل Journal
     * هر نوع سند به یک دفتر روزنامه متصل است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journal()
    {
        return $this->belongsTo(Journals::class, 'journal_id');
    }

    /**
     * رابطه belongsTo با مدل NumberSequence
     * هر نوع سند به یک شماره‌گذاری متصل است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function numberSequence()
    {
        return $this->belongsTo(NumberSequences::class, 'number_sequence_id');
    }

    /**
     * رابطه hasMany برای اسناد حسابداری
     * تمام اسنادی که از این نوع هستند
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalEntries()
    {
        return $this->hasMany(JournalEntries::class, 'document_type_id');
    }

    /**
     * دریافت تعداد اسناد ثبت شده از این نوع
     * @return int
     */
    public function getEntriesCount()
    {
        return $this->journalEntries()->count();
    }

    /**
     * دریافت آخرین سند ثبت شده از این نوع
     * @return JournalEntries|null
     */
    public function getLastEntry()
    {
        return $this->journalEntries()
                    ->orderBy('document_no', 'desc')
                    ->first();
    }

    /**
     * دریافت آخرین شماره سند ثبت شده از این نوع
     * @return string|null
     */
    public function getLastDocumentNo()
    {
        $lastEntry = $this->getLastEntry();
        return $lastEntry ? $lastEntry->document_no : null;
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر انواع اسناد فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر انواع اسناد غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای فیلتر بر اساس شرکت
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * سکوپ برای فیلتر بر اساس کد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای فیلتر بر اساس دفتر روزنامه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $journalId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    /**
     * سکوپ برای فیلتر بر اساس شماره‌گذاری
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $sequenceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByNumberSequence($query, $sequenceId)
    {
        return $query->where('number_sequence_id', $sequenceId);
    }

    /**
     * سکوپ برای جستجوی انواع اسناد بر اساس نام یا کد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای دریافت انواع اسناد دارای شماره‌گذاری
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasNumberSequence($query)
    {
        return $query->whereNotNull('number_sequence_id');
    }

    /**
     * سکوپ برای دریافت انواع اسناد بدون شماره‌گذاری
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutNumberSequence($query)
    {
        return $query->whereNull('number_sequence_id');
    }

    /**
     * سکوپ برای دریافت انواع اسناد دارای دفتر روزنامه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasJournal($query)
    {
        return $query->whereNotNull('journal_id');
    }

    /**
     * سکوپ برای دریافت انواع اسناد بدون دفتر روزنامه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutJournal($query)
    {
        return $query->whereNull('journal_id');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت وضعیت به صورت متنی
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'فعال' : 'غیرفعال';
    }

    /**
     * دریافت کلاس CSS برای وضعیت
     * @return string
     */
    public function getStatusClassAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    /**
     * دریافت نام دفتر روزنامه (در صورت وجود)
     * @return string|null
     */
    public function getJournalNameAttribute()
    {
        return $this->journal ? $this->journal->name : null;
    }

    /**
     * دریافت نام شماره‌گذاری (در صورت وجود)
     * @return string|null
     */
    public function getNumberSequenceNameAttribute()
    {
        return $this->numberSequence ? $this->numberSequence->name : null;
    }

    /**
     * دریافت اطلاعات کامل نوع سند برای گزارش
     * @return array
     */
    public function getReportInfo()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'journal' => $this->journal_name,
            'number_sequence' => $this->number_sequence_name,
            'is_active' => $this->is_active,
            'status_text' => $this->status_text,
            'entries_count' => $this->getEntriesCount(),
            'last_document_no' => $this->getLastDocumentNo(),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * بررسی فعال بودن نوع سند
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * فعال کردن نوع سند
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن نوع سند
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * تولید شماره سند بعدی برای این نوع سند
     * @param int $fiscalYearId
     * @return string|null
     */
    public function generateDocumentNo($fiscalYearId)
    {
        // اگر شماره‌گذاری تعریف شده است، از آن استفاده کن
        if ($this->number_sequence_id) {
            return $this->numberSequence->generateNumber($this->company_id, $fiscalYearId);
        }

        // در غیر این صورت، از شماره‌گذاری پیش‌فرض استفاده کن
        $lastEntry = JournalEntries::byCompany($this->company_id)
            ->byFiscalYear($fiscalYearId)
            ->where('document_type_id', $this->id)
            ->orderBy('document_no', 'desc')
            ->first();

        $year = now()->format('Y');
        $prefix = $this->code . '-' . $year . '-';
        
        if ($lastEntry) {
            $lastNumber = (int) str_replace($prefix, '', $lastEntry->document_no);
            return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }
        
        return $prefix . '00001';
    }

    /**
     * دریافت الگوی شماره‌گذاری
     * @return string
     */
    public function getNumberPattern()
    {
        if ($this->number_sequence_id) {
            return $this->numberSequence->pattern;
        }
        
        return self::$defaultNumberSequences[$this->code] ?? $this->code . '-{year}-{number}';
    }

    /**
     * دریافت لیست انواع اسناد قابل استفاده برای یک دفتر روزنامه
     * @param int $companyId
     * @param int $journalId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByJournal($companyId, $journalId)
    {
        return self::byCompany($companyId)
            ->active()
            ->byJournal($journalId)
            ->orderBy('name')
            ->get();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست انواع اسناد یک شرکت برای استفاده در dropdown
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getList($companyId, $onlyActive = true)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('name')
                     ->pluck('name', 'id')
                     ->toArray();
    }

    /**
     * دریافت لیست انواع اسناد با فرمت کامل (نام به همراه کد)
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getListWithCode($companyId, $onlyActive = true)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        $types = $query->orderBy('name')->get();
        $list = [];
        
        foreach ($types as $type) {
            $list[$type->id] = $type->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت نوع سند بر اساس کد و شرکت
     * @param int $companyId
     * @param string $code
     * @return self|null
     */
    public static function getByCodeAndCompany($companyId, $code)
    {
        return self::byCompany($companyId)
            ->byCode($code)
            ->first();
    }

    /**
     * دریافت آمار انواع اسناد یک شرکت
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        $active = self::byCompany($companyId)->active()->count();
        $inactive = $total - $active;
        
        $hasJournal = self::byCompany($companyId)->hasJournal()->count();
        $withoutJournal = $total - $hasJournal;
        
        $hasSequence = self::byCompany($companyId)->hasNumberSequence()->count();
        $withoutSequence = $total - $hasSequence;
        
        // دریافت تعداد اسناد ثبت شده برای هر نوع
        $typesWithCount = self::byCompany($companyId)
            ->withCount('journalEntries')
            ->orderBy('name')
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'code' => $type->code,
                    'name' => $type->name,
                    'entries_count' => $type->journal_entries_count,
                    'is_active' => $type->is_active,
                ];
            });
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'has_journal' => $hasJournal,
            'without_journal' => $withoutJournal,
            'has_number_sequence' => $hasSequence,
            'without_number_sequence' => $withoutSequence,
            'types' => $typesWithCount,
        ];
    }

    /**
     * ایجاد نوع سند جدید با اعتبارسنجی
     * @param int $companyId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($companyId, array $data)
    {
        // بررسی وجود کد تکراری
        if (isset($data['code'])) {
            $exists = self::byCompany($companyId)
                ->where('code', $data['code'])
                ->exists();
            
            if ($exists) {
                throw new \Exception('کد نوع سند تکراری است.');
            }
        }

        // بررسی وجود دفتر روزنامه (اگر مشخص شده باشد)
        if (isset($data['journal_id']) && $data['journal_id']) {
            $journal = Journals::byCompany($companyId)
                ->where('id', $data['journal_id'])
                ->exists();
            
            if (!$journal) {
                throw new \Exception('دفتر روزنامه مشخص شده وجود ندارد.');
            }
        }

        // بررسی وجود شماره‌گذاری (اگر مشخص شده باشد)
        if (isset($data['number_sequence_id']) && $data['number_sequence_id']) {
            $sequence = NumberSequences::byCompany($companyId)
                ->where('id', $data['number_sequence_id'])
                ->exists();
            
            if (!$sequence) {
                throw new \Exception('شماره‌گذاری مشخص شده وجود ندارد.');
            }
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * ایجاد انواع اسناد پیش‌فرض برای یک شرکت
     * @param int $companyId
     * @param int|null $defaultJournalId
     * @param int|null $defaultSequenceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createDefaultTypes($companyId, $defaultJournalId = null, $defaultSequenceId = null)
    {
        $created = collect();
        
        $defaultTypes = [
            ['code' => 'SINV', 'name' => 'فاکتور فروش', 'type' => self::TYPE_SALES_INVOICE],
            ['code' => 'PINV', 'name' => 'فاکتور خرید', 'type' => self::TYPE_PURCHASE_INVOICE],
            ['code' => 'REC', 'name' => 'دریافت نقدی', 'type' => self::TYPE_RECEIPT],
            ['code' => 'PAY', 'name' => 'پرداخت نقدی', 'type' => self::TYPE_PAYMENT],
            ['code' => 'BDEP', 'name' => 'واریز به بانک', 'type' => self::TYPE_BANK_DEPOSIT],
            ['code' => 'BWIT', 'name' => 'برداشت از بانک', 'type' => self::TYPE_BANK_WITHDRAWAL],
            ['code' => 'SAL', 'name' => 'حقوق و دستمزد', 'type' => self::TYPE_SALARY],
            ['code' => 'ADJ', 'name' => 'سند تعدیلی', 'type' => self::TYPE_ADJUSTMENT],
            ['code' => 'CLS', 'name' => 'سند اختتامیه', 'type' => self::TYPE_CLOSING],
            ['code' => 'OPB', 'name' => 'مانده افتتاحیه', 'type' => self::TYPE_OPENING_BALANCE],
        ];
        
        foreach ($defaultTypes as $typeData) {
            try {
                $type = self::createWithValidation($companyId, [
                    'code' => $typeData['code'],
                    'name' => $typeData['name'],
                    'journal_id' => $defaultJournalId,
                    'number_sequence_id' => $defaultSequenceId,
                    'is_active' => true,
                ]);
                $created->push($type);
            } catch (\Exception $e) {
                // در صورت خطا، ادامه دهید
                continue;
            }
        }
        
        return $created;
    }

    /**
     * دریافت انواع اسناد بر اساس دفتر روزنامه
     * @param int $companyId
     * @param int $journalId
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByJournalId($companyId, $journalId, $onlyActive = true)
    {
        $query = self::byCompany($companyId)->byJournal($journalId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * دریافت انواع اسناد بر اساس شماره‌گذاری
     * @param int $companyId
     * @param int $sequenceId
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByNumberSequence($companyId, $sequenceId, $onlyActive = true)
    {
        $query = self::byCompany($companyId)->byNumberSequence($sequenceId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * دریافت انواع اسناد برای استفاده در گزارش
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForReport($companyId, array $filters = [])
    {
        $query = self::byCompany($companyId)->with(['journal', 'numberSequence']);
        
        // فیلتر بر اساس وضعیت
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }
        
        // فیلتر بر اساس جستجو
        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }
        
        // فیلتر بر اساس دفتر روزنامه
        if (isset($filters['journal_id']) && $filters['journal_id']) {
            $query->byJournal($filters['journal_id']);
        }
        
        return $query->orderBy('code')->get();
    }

    /**
     * به‌روزرسانی شماره‌گذاری برای همه انواع اسناد
     * @param int $companyId
     * @param int $oldSequenceId
     * @param int $newSequenceId
     * @return int
     */
    public static function updateNumberSequence($companyId, $oldSequenceId, $newSequenceId)
    {
        return self::byCompany($companyId)
            ->where('number_sequence_id', $oldSequenceId)
            ->update(['number_sequence_id' => $newSequenceId]);
    }

    /**
     * به‌روزرسانی دفتر روزنامه برای همه انواع اسناد
     * @param int $companyId
     * @param int $oldJournalId
     * @param int $newJournalId
     * @return int
     */
    public static function updateJournal($companyId, $oldJournalId, $newJournalId)
    {
        return self::byCompany($companyId)
            ->where('journal_id', $oldJournalId)
            ->update(['journal_id' => $newJournalId]);
    }
}