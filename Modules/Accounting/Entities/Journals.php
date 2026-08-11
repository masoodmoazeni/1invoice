<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Journals extends Model
{
    use HasFactory;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'journals';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'type',
        'is_default',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند
     * @var array
     */
    protected $hidden = [];

    // ========== ثابت‌های مربوط به نوع دفتر روزنامه ==========

    /**
     * نوع: عمومی
     */
    const TYPE_GENERAL = 'general';
    
    /**
     * نوع: فروش
     */
    const TYPE_SALES = 'sales';
    
    /**
     * نوع: خرید
     */
    const TYPE_PURCHASE = 'purchase';
    
    /**
     * نوع: نقدی
     */
    const TYPE_CASH = 'cash';
    
    /**
     * نوع: بانکی
     */
    const TYPE_BANK = 'bank';
    
    /**
     * نوع: حقوق و دستمزد
     */
    const TYPE_SALARY = 'salary';
    
    /**
     * نوع: انبار
     */
    const TYPE_INVENTORY = 'inventory';
    
    /**
     * نوع: تعدیلات
     */
    const TYPE_ADJUSTMENT = 'adjustment';
    
    /**
     * نوع: اختتامیه
     */
    const TYPE_CLOSING = 'closing';

    /**
     * لیست انواع دفتر روزنامه معتبر
     * @var array
     */
    public static $types = [
        self::TYPE_GENERAL,
        self::TYPE_SALES,
        self::TYPE_PURCHASE,
        self::TYPE_CASH,
        self::TYPE_BANK,
        self::TYPE_SALARY,
        self::TYPE_INVENTORY,
        self::TYPE_ADJUSTMENT,
        self::TYPE_CLOSING,
    ];

    /**
     * لیست انواع دفتر روزنامه با برچسب فارسی
     * @var array
     */
    public static $typeLabels = [
        self::TYPE_GENERAL => 'دفتر روزنامه عمومی',
        self::TYPE_SALES => 'دفتر روزنامه فروش',
        self::TYPE_PURCHASE => 'دفتر روزنامه خرید',
        self::TYPE_CASH => 'دفتر روزنامه نقدی',
        self::TYPE_BANK => 'دفتر روزنامه بانکی',
        self::TYPE_SALARY => 'دفتر روزنامه حقوق و دستمزد',
        self::TYPE_INVENTORY => 'دفتر روزنامه انبار',
        self::TYPE_ADJUSTMENT => 'دفتر روزنامه تعدیلات',
        self::TYPE_CLOSING => 'دفتر روزنامه اختتامیه',
    ];

    /**
     * لیست انواع دفتر روزنامه با رنگ‌بندی
     * @var array
     */
    public static $typeColors = [
        self::TYPE_GENERAL => 'primary',
        self::TYPE_SALES => 'success',
        self::TYPE_PURCHASE => 'info',
        self::TYPE_CASH => 'warning',
        self::TYPE_BANK => 'secondary',
        self::TYPE_SALARY => 'danger',
        self::TYPE_INVENTORY => 'dark',
        self::TYPE_ADJUSTMENT => 'purple',
        self::TYPE_CLOSING => 'indigo',
    ];

    /**
     * لیست انواع دفتر روزنامه با آیکون‌ها
     * @var array
     */
    public static $typeIcons = [
        self::TYPE_GENERAL => 'fa-book',
        self::TYPE_SALES => 'fa-shopping-cart',
        self::TYPE_PURCHASE => 'fa-truck',
        self::TYPE_CASH => 'fa-money',
        self::TYPE_BANK => 'fa-university',
        self::TYPE_SALARY => 'fa-users',
        self::TYPE_INVENTORY => 'fa-cubes',
        self::TYPE_ADJUSTMENT => 'fa-sliders-h',
        self::TYPE_CLOSING => 'fa-lock',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\JournalsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\JournalsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر دفتر روزنامه متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه hasMany برای سندهای حسابداری
     * تمام سندهایی که در این دفتر روزنامه ثبت شده‌اند
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalEntries()
    {
        return $this->hasMany(JournalEntries::class, 'journal_id');
    }

    /**
     * رابطه hasMany برای سندهای ثبت نهایی شده
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postedEntries()
    {
        return $this->hasMany(JournalEntries::class, 'journal_id')
                    ->where('status', JournalEntries::STATUS_POSTED);
    }

    /**
     * دریافت تعداد سندهای ثبت شده در این دفتر
     * @return int
     */
    public function getEntriesCount()
    {
        return $this->journalEntries()->count();
    }

    /**
     * دریافت تعداد سندهای ثبت نهایی شده
     * @return int
     */
    public function getPostedEntriesCount()
    {
        return $this->postedEntries()->count();
    }

    /**
     * دریافت مجموع مبالغ سندهای این دفتر
     * @return array
     */
    public function getTotalAmounts()
    {
        $totalDebit = $this->journalEntries()->sum('total_debit');
        $totalCredit = $this->journalEntries()->sum('total_credit');
        
        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
        ];
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر دفترهای روزنامه فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر دفترهای روزنامه غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای فیلتر دفتر روزنامه پیش‌فرض
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
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
     * سکوپ برای فیلتر بر اساس نوع دفتر روزنامه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * سکوپ برای فیلتر بر اساس کد دفتر روزنامه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای جستجوی دفترهای روزنامه بر اساس نام یا کد
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
     * سکوپ برای دریافت دفترهای روزنامه عمومی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGeneral($query)
    {
        return $query->where('type', self::TYPE_GENERAL);
    }

    /**
     * سکوپ برای دریافت دفترهای روزنامه غیرعمومی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotGeneral($query)
    {
        return $query->where('type', '!=', self::TYPE_GENERAL);
    }

    /**
     * سکوپ برای دریافت دفترهای روزنامه فروش
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSales($query)
    {
        return $query->where('type', self::TYPE_SALES);
    }

    /**
     * سکوپ برای دریافت دفترهای روزنامه خرید
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePurchase($query)
    {
        return $query->where('type', self::TYPE_PURCHASE);
    }

    /**
     * سکوپ برای دریافت دفترهای روزنامه نقدی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCash($query)
    {
        return $query->where('type', self::TYPE_CASH);
    }

    /**
     * سکوپ برای دریافت دفترهای روزنامه بانکی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBank($query)
    {
        return $query->where('type', self::TYPE_BANK);
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل دفتر روزنامه (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت نوع به صورت متنی
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    /**
     * دریافت رنگ برای نوع دفتر روزنامه
     * @return string
     */
    public function getTypeColorAttribute()
    {
        return self::$typeColors[$this->type] ?? 'primary';
    }

    /**
     * دریافت آیکون برای نوع دفتر روزنامه
     * @return string
     */
    public function getTypeIconAttribute()
    {
        return self::$typeIcons[$this->type] ?? 'fa-book';
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
     * بررسی فعال بودن دفتر روزنامه
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * بررسی پیش‌فرض بودن دفتر روزنامه
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }

    /**
     * فعال کردن دفتر روزنامه
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن دفتر روزنامه
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * تنظیم به‌عنوان دفتر روزنامه پیش‌فرض شرکت
     * سایر دفترهای روزنامه پیش‌فرض شرکت غیرفعال می‌شوند
     * @return bool
     */
    public function setAsDefault()
    {
        // ابتدا تمام دفترهای روزنامه پیش‌فرض این شرکت را غیرپیش‌فرض می‌کنیم
        self::where('company_id', $this->company_id)
            ->where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // سپس این دفتر روزنامه را پیش‌فرض می‌کنیم
        $this->is_default = true;
        return $this->save();
    }

    /**
     * دریافت آخرین سند ثبت شده در این دفتر
     * @return JournalEntries|null
     */
    public function getLastEntry()
    {
        return $this->journalEntries()
                    ->orderBy('document_no', 'desc')
                    ->first();
    }

    /**
     * دریافت آخرین شماره سند ثبت شده در این دفتر
     * @return string|null
     */
    public function getLastDocumentNo()
    {
        $lastEntry = $this->getLastEntry();
        return $lastEntry ? $lastEntry->document_no : null;
    }

    /**
     * تولید شماره سند جدید برای این دفتر
     * @param int $fiscalYearId
     * @return string
     */
    public function generateDocumentNo($fiscalYearId)
    {
        $lastEntry = JournalEntries::byCompany($this->company_id)
            ->byFiscalYear($fiscalYearId)
            ->byJournal($this->id)
            ->orderBy('document_no', 'desc')
            ->first();
        
        if ($lastEntry) {
            $lastNumber = (int) $lastEntry->document_no;
            return str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }
        
        return '00001';
    }

    /**
     * دریافت آمار سندهای این دفتر در یک سال مالی
     * @param int $fiscalYearId
     * @return array
     */
    public function getStatistics($fiscalYearId)
    {
        $query = $this->journalEntries()
                      ->byFiscalYear($fiscalYearId);
        
        $total = $query->count();
        $draft = $query->draft()->count();
        $pending = $query->pending()->count();
        $approved = $query->approved()->count();
        $posted = $query->posted()->count();
        $rejected = $query->rejected()->count();
        $voided = $query->voided()->count();
        
        $totalDebit = $query->sum('total_debit');
        $totalCredit = $query->sum('total_credit');
        
        return [
            'total' => $total,
            'draft' => $draft,
            'pending' => $pending,
            'approved' => $approved,
            'posted' => $posted,
            'rejected' => $rejected,
            'voided' => $voided,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ];
    }

    /**
     * دریافت سندهای این دفتر در یک بازه زمانی
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEntriesByDateRange($startDate, $endDate)
    {
        return $this->journalEntries()
                    ->dateBetween($startDate, $endDate)
                    ->orderBy('document_date')
                    ->get();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست دفترهای روزنامه یک شرکت برای استفاده در dropdown
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
     * دریافت لیست دفترهای روزنامه با فرمت کامل (نام به همراه کد)
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
        
        $journals = $query->orderBy('name')->get();
        $list = [];
        
        foreach ($journals as $journal) {
            $list[$journal->id] = $journal->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت دفتر روزنامه پیش‌فرض یک شرکت
     * @param int $companyId
     * @return self|null
     */
    public static function getDefaultJournal($companyId)
    {
        return self::byCompany($companyId)
            ->active()
            ->default()
            ->first();
    }

    /**
     * دریافت دفتر روزنامه بر اساس کد و شرکت
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
     * دریافت دفترهای روزنامه بر اساس نوع
     * @param int $companyId
     * @param string $type
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByType($companyId, $type, $onlyActive = true)
    {
        $query = self::byCompany($companyId)->byType($type);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * دریافت آمار دفترهای روزنامه یک شرکت
     * @param int $companyId
     * @return array
     */
    // public static function getStatistics($companyId)
    // {
    //     $total = self::byCompany($companyId)->count();
    //     $active = self::byCompany($companyId)->active()->count();
    //     $inactive = $total - $active;
    //     $default = self::byCompany($companyId)->default()->count();
        
    //     $general = self::byCompany($companyId)->general()->count();
    //     $sales = self::byCompany($companyId)->sales()->count();
    //     $purchase = self::byCompany($companyId)->purchase()->count();
    //     $cash = self::byCompany($companyId)->cash()->count();
    //     $bank = self::byCompany($companyId)->bank()->count();
        
    //     return [
    //         'total' => $total,
    //         'active' => $active,
    //         'inactive' => $inactive,
    //         'default' => $default,
    //         'by_type' => [
    //             'general' => $general,
    //             'sales' => $sales,
    //             'purchase' => $purchase,
    //             'cash' => $cash,
    //             'bank' => $bank,
    //         ],
    //     ];
    // }

    /**
     * ایجاد خودکار کد دفتر روزنامه بر اساس نام
     * @param string $name
     * @param int $companyId
     * @return string
     */
    public static function generateCode($name, $companyId)
    {
        $code = strtoupper(Str::slug($name, '_'));
        
        // اگر کد تکراری باشد، شماره اضافه می‌شود
        $counter = 1;
        $originalCode = $code;
        while (self::where('code', $code)->where('company_id', $companyId)->exists()) {
            $code = $originalCode . '_' . $counter;
            $counter++;
        }
        
        return $code;
    }

    /**
     * ایجاد دفتر روزنامه جدید با اعتبارسنجی
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
                throw new \Exception('کد دفتر روزنامه تکراری است.');
            }
        }

        // اگر پیش‌فرض است، سایر دفترهای روزنامه را غیرپیش‌فرض کن
        if (isset($data['is_default']) && $data['is_default']) {
            self::byCompany($companyId)->update(['is_default' => false]);
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * دریافت دفترهای روزنامه دارای سند
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWithEntries($companyId)
    {
        return self::byCompany($companyId)
            ->has('journalEntries')
            ->orderBy('name')
            ->get();
    }

    /**
     * دریافت دفترهای روزنامه بدون سند
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWithoutEntries($companyId)
    {
        return self::byCompany($companyId)
            ->doesntHave('journalEntries')
            ->orderBy('name')
            ->get();
    }
}