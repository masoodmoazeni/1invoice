<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Dimensions extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'dimensions';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'type',
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

    // ========== ثابت‌های مربوط به نوع ابعاد ==========

    /**
     * نوع: مرکز هزینه
     */
    const TYPE_COST_CENTER = 'cost_center';
    
    /**
     * نوع: پروژه
     */
    const TYPE_PROJECT = 'project';
    
    /**
     * نوع: دپارتمان
     */
    const TYPE_DEPARTMENT = 'department';
    
    /**
     * نوع: مشتری
     */
    const TYPE_CUSTOMER = 'customer';
    
    /**
     * نوع: تامین‌کننده
     */
    const TYPE_VENDOR = 'vendor';
    
    /**
     * نوع: کارمند
     */
    const TYPE_EMPLOYEE = 'employee';
    
    /**
     * نوع: محصول
     */
    const TYPE_PRODUCT = 'product';
    
    /**
     * نوع: منطقه
     */
    const TYPE_REGION = 'region';
    
    /**
     * نوع: سفارشی
     */
    const TYPE_CUSTOM = 'custom';

    /**
     * لیست انواع ابعاد معتبر
     * @var array
     */
    public static $types = [
        self::TYPE_COST_CENTER,
        self::TYPE_PROJECT,
        self::TYPE_DEPARTMENT,
        self::TYPE_CUSTOMER,
        self::TYPE_VENDOR,
        self::TYPE_EMPLOYEE,
        self::TYPE_PRODUCT,
        self::TYPE_REGION,
        self::TYPE_CUSTOM,
    ];

    /**
     * لیست انواع ابعاد با برچسب فارسی
     * @var array
     */
    public static $typeLabels = [
        self::TYPE_COST_CENTER => 'مرکز هزینه',
        self::TYPE_PROJECT => 'پروژه',
        self::TYPE_DEPARTMENT => 'دپارتمان',
        self::TYPE_CUSTOMER => 'مشتری',
        self::TYPE_VENDOR => 'تامین‌کننده',
        self::TYPE_EMPLOYEE => 'کارمند',
        self::TYPE_PRODUCT => 'محصول',
        self::TYPE_REGION => 'منطقه',
        self::TYPE_CUSTOM => 'سفارشی',
    ];

    /**
     * لیست انواع ابعاد با آیکون‌ها
     * @var array
     */
    public static $typeIcons = [
        self::TYPE_COST_CENTER => 'fa-coins',
        self::TYPE_PROJECT => 'fa-project-diagram',
        self::TYPE_DEPARTMENT => 'fa-building',
        self::TYPE_CUSTOMER => 'fa-user-tie',
        self::TYPE_VENDOR => 'fa-truck',
        self::TYPE_EMPLOYEE => 'fa-user',
        self::TYPE_PRODUCT => 'fa-box',
        self::TYPE_REGION => 'fa-map-marker-alt',
        self::TYPE_CUSTOM => 'fa-cog',
    ];

    /**
     * لیست انواع ابعاد با رنگ‌ها
     * @var array
     */
    public static $typeColors = [
        self::TYPE_COST_CENTER => 'primary',
        self::TYPE_PROJECT => 'success',
        self::TYPE_DEPARTMENT => 'info',
        self::TYPE_CUSTOMER => 'warning',
        self::TYPE_VENDOR => 'danger',
        self::TYPE_EMPLOYEE => 'secondary',
        self::TYPE_PRODUCT => 'dark',
        self::TYPE_REGION => 'purple',
        self::TYPE_CUSTOM => 'indigo',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\DimensionsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\DimensionsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر بعد متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه hasMany برای ارتباط با جدول‌های دیگر
     * هر بعد می‌تواند در ردیف‌های سند حسابداری استفاده شود
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalEntryLines()
    {
        // این رابطه فرض می‌کند که در جدول journal_entry_lines
        // فیلد dimension_id وجود دارد
        return $this->hasMany(JournalEntryLines::class, 'dimension_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌ها
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'dimension_id');
    }

    /**
     * دریافت تعداد کل ردیف‌های سند مربوط به این بعد
     * @return int
     */
    public function getEntriesCount()
    {
        return $this->journalEntryLines()->count();
    }

    /**
     * دریافت مجموع مبالغ ردیف‌های سند مربوط به این بعد
     * @return array
     */
    public function getTotalAmounts()
    {
        $totalDebit = $this->journalEntryLines()->sum('debit');
        $totalCredit = $this->journalEntryLines()->sum('credit');
        
        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'difference' => $totalDebit - $totalCredit,
        ];
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر ابعاد فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر ابعاد غیرفعال
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
     * سکوپ برای فیلتر بر اساس نوع بعد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
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
     * سکوپ برای فیلتر ابعاد مرکز هزینه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCostCenters($query)
    {
        return $query->where('type', self::TYPE_COST_CENTER);
    }

    /**
     * سکوپ برای فیلتر ابعاد پروژه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProjects($query)
    {
        return $query->where('type', self::TYPE_PROJECT);
    }

    /**
     * سکوپ برای فیلتر ابعاد دپارتمان
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDepartments($query)
    {
        return $query->where('type', self::TYPE_DEPARTMENT);
    }

    /**
     * سکوپ برای فیلتر ابعاد مشتری
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCustomers($query)
    {
        return $query->where('type', self::TYPE_CUSTOMER);
    }

    /**
     * سکوپ برای فیلتر ابعاد تامین‌کننده
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVendors($query)
    {
        return $query->where('type', self::TYPE_VENDOR);
    }

    /**
     * سکوپ برای جستجوی ابعاد بر اساس نام یا کد
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
     * سکوپ برای دریافت ابعاد پرکاربرد (دارای تراکنش)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasTransactions($query)
    {
        return $query->has('journalEntryLines');
    }

    /**
     * سکوپ برای دریافت ابعاد بدون استفاده
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutTransactions($query)
    {
        return $query->doesntHave('journalEntryLines');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل بعد (نام به همراه کد در پرانتز)
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
     * دریافت آیکون برای نوع بعد
     * @return string
     */
    public function getTypeIconAttribute()
    {
        return self::$typeIcons[$this->type] ?? 'fa-tag';
    }

    /**
     * دریافت رنگ برای نوع بعد
     * @return string
     */
    public function getTypeColorAttribute()
    {
        return self::$typeColors[$this->type] ?? 'secondary';
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
     * دریافت کلاس CSS برای نوع
     * @return string
     */
    public function getTypeBadgeClassAttribute()
    {
        $classes = [
            self::TYPE_COST_CENTER => 'bg-primary',
            self::TYPE_PROJECT => 'bg-success',
            self::TYPE_DEPARTMENT => 'bg-info',
            self::TYPE_CUSTOMER => 'bg-warning',
            self::TYPE_VENDOR => 'bg-danger',
            self::TYPE_EMPLOYEE => 'bg-secondary',
            self::TYPE_PRODUCT => 'bg-dark',
            self::TYPE_REGION => 'bg-purple',
            self::TYPE_CUSTOM => 'bg-indigo',
        ];
        
        return $classes[$this->type] ?? 'bg-secondary';
    }

    /**
     * بررسی فعال بودن بعد
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * فعال کردن بعد
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن بعد
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * بررسی اینکه آیا بعد دارای تراکنش است
     * @return bool
     */
    public function hasTransactions()
    {
        return $this->journalEntryLines()->exists();
    }

    /**
     * دریافت خلاصه آماری بعد
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $query = $this->journalEntryLines()
                      ->whereHas('journalEntry', function ($q) {
                          $q->posted();
                      });
        
        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->dateBetween($startDate, $endDate);
            });
        }
        
        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        $count = $query->count();
        
        return [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balance' => $totalDebit - $totalCredit,
            'entries_count' => $count,
            'formatted_debit' => number_format($totalDebit, 2),
            'formatted_credit' => number_format($totalCredit, 2),
            'formatted_balance' => number_format($totalDebit - $totalCredit, 2),
        ];
    }

    /**
     * دریافت ارتباط با بعد دیگر (اگر نیاز به سلسله‌مراتب باشد)
     * می‌توانید این متد را برای ارتباط با جدول parent توسعه دهید
     */
    // public function parent()
    // {
    //     return $this->belongsTo(self::class, 'parent_id');
    // }
    //
    // public function children()
    // {
    //     return $this->hasMany(self::class, 'parent_id');
    // }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست ابعاد یک شرکت برای استفاده در dropdown
     * @param int $companyId
     * @param bool $onlyActive
     * @param string|null $type
     * @return array
     */
    public static function getList($companyId, $onlyActive = true, $type = null)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        if ($type) {
            $query->byType($type);
        }
        
        return $query->orderBy('name')
                     ->pluck('name', 'id')
                     ->toArray();
    }

    /**
     * دریافت لیست ابعاد با فرمت کامل (نام به همراه کد)
     * @param int $companyId
     * @param bool $onlyActive
     * @param string|null $type
     * @return array
     */
    public static function getListWithCode($companyId, $onlyActive = true, $type = null)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        if ($type) {
            $query->byType($type);
        }
        
        $dimensions = $query->orderBy('name')->get();
        $list = [];
        
        foreach ($dimensions as $dimension) {
            $list[$dimension->id] = $dimension->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت لیست ابعاد بر اساس نوع
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
     * دریافت بعد بر اساس کد و شرکت
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
     * دریافت آمار ابعاد یک شرکت
     * @param int $companyId
     * @return array
     */
    // public static function getStatistics($companyId)
    // {
    //     $total = self::byCompany($companyId)->count();
    //     $active = self::byCompany($companyId)->active()->count();
    //     $inactive = $total - $active;
        
    //     $statsByType = [];
    //     foreach (self::$types as $type) {
    //         $statsByType[$type] = [
    //             'count' => self::byCompany($companyId)->byType($type)->count(),
    //             'active' => self::byCompany($companyId)->byType($type)->active()->count(),
    //             'label' => self::$typeLabels[$type],
    //             'icon' => self::$typeIcons[$type],
    //         ];
    //     }
        
    //     $hasTransactions = self::byCompany($companyId)->hasTransactions()->count();
    //     $withoutTransactions = $total - $hasTransactions;
        
    //     return [
    //         'total' => $total,
    //         'active' => $active,
    //         'inactive' => $inactive,
    //         'has_transactions' => $hasTransactions,
    //         'without_transactions' => $withoutTransactions,
    //         'by_type' => $statsByType,
    //     ];
    // }

    /**
     * ایجاد بعد جدید با اعتبارسنجی
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
                throw new \Exception('کد بعد تکراری است.');
            }
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * ایجاد خودکار کد بعد بر اساس نام و نوع
     * @param string $name
     * @param string $type
     * @param int $companyId
     * @return string
     */
    public static function generateCode($name, $type, $companyId)
    {
        // ایجاد کد بر اساس نوع و نام
        $typePrefix = [
            self::TYPE_COST_CENTER => 'CC',
            self::TYPE_PROJECT => 'PRJ',
            self::TYPE_DEPARTMENT => 'DEPT',
            self::TYPE_CUSTOMER => 'CUS',
            self::TYPE_VENDOR => 'VEN',
            self::TYPE_EMPLOYEE => 'EMP',
            self::TYPE_PRODUCT => 'PRD',
            self::TYPE_REGION => 'REG',
            self::TYPE_CUSTOM => 'CUS',
        ];
        
        $prefix = $typePrefix[$type] ?? 'DIM';
        $code = $prefix . '_' . strtoupper(Str::slug($name, '_'));
        
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
     * دریافت ابعاد برای استفاده در گزارش‌ها
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForReport($companyId, array $filters = [])
    {
        $query = self::byCompany($companyId)->with('company');
        
        // فیلتر بر اساس وضعیت
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }
        
        // فیلتر بر اساس نوع
        if (isset($filters['type']) && $filters['type']) {
            $query->byType($filters['type']);
        }
        
        // فیلتر بر اساس جستجو
        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }
        
        return $query->orderBy('type')
                     ->orderBy('name')
                     ->get();
    }

    /**
     * دریافت ابعاد با آمار تراکنش‌ها
     * @param int $companyId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWithTransactionStats($companyId, $startDate = null, $endDate = null)
    {
        $dimensions = self::byCompany($companyId)
            ->active()
            ->with(['journalEntryLines' => function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                        $query->dateBetween($startDate, $endDate)
                              ->posted();
                    });
                } else {
                    $q->whereHas('journalEntry', function ($query) {
                        $query->posted();
                    });
                }
            }])
            ->get();
        
        // اضافه کردن آمار به هر بعد
        foreach ($dimensions as $dimension) {
            $totalDebit = $dimension->journalEntryLines->sum('debit');
            $totalCredit = $dimension->journalEntryLines->sum('credit');
            $dimension->total_debit = $totalDebit;
            $dimension->total_credit = $totalCredit;
            $dimension->balance = $totalDebit - $totalCredit;
            $dimension->entries_count = $dimension->journalEntryLines->count();
        }
        
        return $dimensions;
    }
}