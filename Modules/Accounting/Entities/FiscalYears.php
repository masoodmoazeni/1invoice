<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FiscalYears extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'fiscal_years';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'is_default',
        'lock_date',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'lock_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند
     * @var array
     */
    protected $hidden = [];

    /**
     * تاریخ‌هایی که باید به صورت تاریخ شمسی نمایش داده شوند
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'lock_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // ========== ثابت‌های مربوط به وضعیت سال مالی ==========

    /**
     * وضعیت: باز (قابل استفاده)
     */
    const STATUS_OPEN = 'open';
    
    /**
     * وضعیت: بسته شده
     */
    const STATUS_CLOSED = 'closed';
    
    /**
     * وضعیت: در انتظار
     */
    const STATUS_PENDING = 'pending';
    
    /**
     * وضعیت: قفل شده
     */
    const STATUS_LOCKED = 'locked';

    /**
     * لیست وضعیت‌های معتبر
     * @var array
     */
    public static $statuses = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
        self::STATUS_PENDING,
        self::STATUS_LOCKED,
    ];

    /**
     * لیست وضعیت‌ها با برچسب فارسی
     * @var array
     */
    public static $statusLabels = [
        self::STATUS_OPEN => 'باز',
        self::STATUS_CLOSED => 'بسته شده',
        self::STATUS_PENDING => 'در انتظار',
        self::STATUS_LOCKED => 'قفل شده',
    ];

    /**
     * لیست وضعیت‌های قابل ویرایش
     * @var array
     */
    public static $editableStatuses = [
        self::STATUS_OPEN,
        self::STATUS_PENDING,
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\FiscalYearsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\FiscalYearsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر سال مالی متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌های مالی
     * تمام تراکنش‌هایی که در این سال مالی ثبت شده‌اند
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'fiscal_year_id');
    }

    /**
     * رابطه hasMany برای فاکتورها
     * تمام فاکتورهایی که در این سال مالی ثبت شده‌اند
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'fiscal_year_id');
    }

    /**
     * رابطه hasMany برای بودجه‌ها
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class, 'fiscal_year_id');
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر سال‌های مالی باز
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی بسته شده
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی در انتظار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی قفل شده
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLocked($query)
    {
        return $query->where('status', self::STATUS_LOCKED);
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی فعال (باز یا در انتظار)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_PENDING]);
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی غیرفعال (بسته یا قفل شده)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->whereIn('status', [self::STATUS_CLOSED, self::STATUS_LOCKED]);
    }

    /**
     * سکوپ برای فیلتر سال مالی پیش‌فرض
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
     * سکوپ برای فیلتر سال‌های مالی که تاریخ مشخصی در آنها قرار دارد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContainsDate($query, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return $query->where('start_date', '<=', $date)
                     ->where('end_date', '>=', $date);
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی جاری (سال جاری)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent($query)
    {
        $today = Carbon::today()->format('Y-m-d');
        return $query->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today);
    }

    /**
     * سکوپ برای جستجوی سال‌های مالی بر اساس نام یا تاریخ
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('start_date', 'LIKE', "%{$search}%")
                     ->orWhere('end_date', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی که تاریخ پایان آنها گذشته است
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', Carbon::today());
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی که تاریخ شروع آنها در آینده است
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFuture($query)
    {
        return $query->where('start_date', '>', Carbon::today());
    }

    /**
     * سکوپ برای فیلتر سال‌های مالی قابل ویرایش
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEditable($query)
    {
        return $query->whereIn('status', self::$editableStatuses);
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل سال مالی
     * @return string
     */
    public function getFullNameAttribute()
    {
        $start = Carbon::parse($this->start_date)->format('Y/m/d');
        $end = Carbon::parse($this->end_date)->format('Y/m/d');
        return $this->name . ' (' . $start . ' - ' . $end . ')';
    }

    /**
     * دریافت مدت زمان سال مالی به روز
     * @return int
     */
    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        return $start->diffInDays($end) + 1;
    }

    /**
     * دریافت وضعیت به صورت متنی
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    /**
     * دریافت کلاس CSS برای وضعیت
     * @return string
     */
    public function getStatusClassAttribute()
    {
        $classes = [
            self::STATUS_OPEN => 'success',
            self::STATUS_CLOSED => 'secondary',
            self::STATUS_PENDING => 'warning',
            self::STATUS_LOCKED => 'danger',
        ];
        
        return $classes[$this->status] ?? 'info';
    }

    /**
     * دریافت رنگ برای وضعیت
     * @return string
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_OPEN => '#28a745',
            self::STATUS_CLOSED => '#6c757d',
            self::STATUS_PENDING => '#ffc107',
            self::STATUS_LOCKED => '#dc3545',
        ];
        
        return $colors[$this->status] ?? '#17a2b8';
    }

    /**
     * دریافت تعداد روزهای باقیمانده تا پایان سال مالی
     * @return int|null
     */
    public function getRemainingDaysAttribute()
    {
        $end = Carbon::parse($this->end_date);
        $today = Carbon::today();
        
        if ($today->gt($end)) {
            return 0;
        }
        
        return $today->diffInDays($end);
    }

    /**
     * دریافت درصد پیشرفت سال مالی
     * @return float
     */
    public function getProgressAttribute()
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $today = Carbon::today();
        
        if ($today->lt($start)) {
            return 0;
        }
        
        if ($today->gt($end)) {
            return 100;
        }
        
        $totalDays = $start->diffInDays($end);
        $passedDays = $start->diffInDays($today);
        
        return round(($passedDays / $totalDays) * 100, 2);
    }

    /**
     * بررسی اینکه آیا سال مالی جاری است؟
     * @return bool
     */
    public function isCurrent()
    {
        $today = Carbon::today();
        return $today->between(
            Carbon::parse($this->start_date),
            Carbon::parse($this->end_date)
        );
    }

    /**
     * بررسی اینکه آیا سال مالی باز است؟
     * @return bool
     */
    public function isOpen()
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * بررسی اینکه آیا سال مالی بسته شده است؟
     * @return bool
     */
    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * بررسی اینکه آیا سال مالی قفل شده است؟
     * @return bool
     */
    public function isLocked()
    {
        return $this->status === self::STATUS_LOCKED;
    }

    /**
     * بررسی اینکه آیا سال مالی در انتظار است؟
     * @return bool
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * بررسی اینکه آیا سال مالی قابل ویرایش است؟
     * @return bool
     */
    public function isEditable()
    {
        return in_array($this->status, self::$editableStatuses);
    }

    /**
     * بررسی اینکه آیا سال مالی پیش‌فرض است؟
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }

    /**
     * بررسی اینکه آیا تاریخ مشخصی در محدوده سال مالی قرار دارد؟
     * @param string|Carbon $date
     * @return bool
     */
    public function containsDate($date)
    {
        $date = Carbon::parse($date);
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        return $date->between($start, $end);
    }

    /**
     * دریافت تاریخ‌های شروع و پایان به صورت آرایه
     * @return array
     */
    public function getDateRange()
    {
        return [
            'start' => Carbon::parse($this->start_date)->format('Y-m-d'),
            'end' => Carbon::parse($this->end_date)->format('Y-m-d'),
        ];
    }

    /**
     * باز کردن سال مالی
     * @return bool
     */
    public function open()
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }
        
        $this->status = self::STATUS_OPEN;
        return $this->save();
    }

    /**
     * بستن سال مالی
     * @return bool
     */
    public function close()
    {
        if (!in_array($this->status, [self::STATUS_OPEN, self::STATUS_PENDING])) {
            return false;
        }
        
        $this->status = self::STATUS_CLOSED;
        return $this->save();
    }

    /**
     * قفل کردن سال مالی
     * @param string|null $lockDate
     * @return bool
     */
    public function lock($lockDate = null)
    {
        if (!in_array($this->status, [self::STATUS_OPEN, self::STATUS_PENDING])) {
            return false;
        }
        
        $this->status = self::STATUS_LOCKED;
        $this->lock_date = $lockDate ? Carbon::parse($lockDate) : Carbon::now();
        return $this->save();
    }

    /**
     * تنظیم به‌عنوان سال مالی پیش‌فرض شرکت
     * سایر سال‌های مالی پیش‌فرض شرکت غیرفعال می‌شوند
     * @return bool
     */
    public function setAsDefault()
    {
        // ابتدا تمام سال‌های مالی پیش‌فرض این شرکت را غیرپیش‌فرض می‌کنیم
        self::where('company_id', $this->company_id)
            ->where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // سپس این سال مالی را پیش‌فرض می‌کنیم
        $this->is_default = true;
        return $this->save();
    }

    /**
     * دریافت سال مالی بعدی (سال بعد)
     * @return self|null
     */
    public function getNextFiscalYear()
    {
        return self::byCompany($this->company_id)
            ->where('start_date', '>', $this->start_date)
            ->orderBy('start_date')
            ->first();
    }

    /**
     * دریافت سال مالی قبلی (سال قبل)
     * @return self|null
     */
    public function getPreviousFiscalYear()
    {
        return self::byCompany($this->company_id)
            ->where('start_date', '<', $this->start_date)
            ->orderBy('start_date', 'desc')
            ->first();
    }

    /**
     * دریافت تراکنش‌های این سال مالی بر اساس نوع
     * @param string|null $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionsByType($type = null)
    {
        $query = $this->transactions();
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    /**
     * دریافت مجموع تراکنش‌های سال مالی
     * @return float
     */
    public function getTotalTransactionsAmount()
    {
        return $this->transactions()->sum('amount');
    }

    /**
     * دریافت مجموع درآمدهای سال مالی
     * @return float
     */
    public function getTotalIncome()
    {
        return $this->transactions()->where('type', 'income')->sum('amount');
    }

    /**
     * دریافت مجموع هزینه‌های سال مالی
     * @return float
     */
    public function getTotalExpense()
    {
        return $this->transactions()->where('type', 'expense')->sum('amount');
    }

    /**
     * دریافت سود/زیان سال مالی
     * @return float
     */
    public function getProfitLoss()
    {
        return $this->getTotalIncome() - $this->getTotalExpense();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست سال‌های مالی یک شرکت برای استفاده در dropdown
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getList($companyId, $onlyActive = false)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('start_date', 'desc')
                     ->pluck('name', 'id')
                     ->toArray();
    }

    /**
     * دریافت لیست سال‌های مالی با فرمت کامل
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getListWithFullName($companyId, $onlyActive = false)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        $years = $query->orderBy('start_date', 'desc')->get();
        $list = [];
        
        foreach ($years as $year) {
            $list[$year->id] = $year->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت سال مالی پیش‌فرض یک شرکت
     * @param int $companyId
     * @return self|null
     */
    public static function getDefaultFiscalYear($companyId)
    {
        return self::byCompany($companyId)
            ->default()
            ->first();
    }

    /**
     * دریافت سال مالی جاری یک شرکت
     * @param int $companyId
     * @return self|null
     */
    public static function getCurrentFiscalYear($companyId)
    {
        return self::byCompany($companyId)
            ->current()
            ->first();
    }

    /**
     * دریافت سال مالی بر اساس تاریخ مشخص
     * @param int $companyId
     * @param string|Carbon $date
     * @return self|null
     */
    public static function getByDate($companyId, $date)
    {
        return self::byCompany($companyId)
            ->containsDate($date)
            ->first();
    }

    /**
     * دریافت آمار سال‌های مالی یک شرکت
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        $open = self::byCompany($companyId)->open()->count();
        $closed = self::byCompany($companyId)->closed()->count();
        $pending = self::byCompany($companyId)->pending()->count();
        $locked = self::byCompany($companyId)->locked()->count();
        $default = self::byCompany($companyId)->default()->count();
        $current = self::byCompany($companyId)->current()->count();
        
        return [
            'total' => $total,
            'open' => $open,
            'closed' => $closed,
            'pending' => $pending,
            'locked' => $locked,
            'default' => $default,
            'current' => $current,
        ];
    }

    /**
     * ایجاد سال مالی جدید با بررسی همپوشانی
     * @param int $companyId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($companyId, array $data)
    {
        // بررسی همپوشانی با سال‌های مالی دیگر
        $overlap = self::byCompany($companyId)
            ->where(function ($query) use ($data) {
                $query->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                      ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                      ->orWhere(function ($q) use ($data) {
                          $q->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                      });
            })->exists();
        
        if ($overlap) {
            throw new \Exception('تاریخ‌های سال مالی با سال مالی دیگری تداخل دارد.');
        }
        
        // اگر پیش‌فرض است، سایر سال‌های مالی را غیرپیش‌فرض کن
        if (isset($data['is_default']) && $data['is_default']) {
            self::byCompany($companyId)->update(['is_default' => false]);
        }
        
        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * دریافت سال‌های مالی بر اساس محدوده تاریخ
     * @param int $companyId
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByDateRange($companyId, $startDate, $endDate)
    {
        return self::byCompany($companyId)
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->orderBy('start_date')
            ->get();
    }

    /**
     * دریافت سال‌های مالی دارای تراکنش
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWithTransactions($companyId)
    {
        return self::byCompany($companyId)
            ->has('transactions')
            ->orderBy('start_date', 'desc')
            ->get();
    }
}