<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Branches extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'branches';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'country_id',
        'city',
        'address',
        'phone',
        'email',
        'manager_id',
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

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\BranchesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\BranchesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر شعبه متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsTo با مدل Country
     * هر شعبه در یک کشور قرار دارد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    /**
     * رابطه belongsTo برای مدیر شعبه
     * فرض بر این است که جدول users یا employees وجود دارد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id'); // یا Employee::class
    }

    /**
     * رابطه hasMany برای کارمندان شعبه (در صورت وجود)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id'); // در صورت وجود جدول employees
    }

    /**
     * رابطه hasMany برای کاربران شعبه
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌های مالی شعبه
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'branch_id');
    }

    /**
     * رابطه hasMany برای فاکتورهای شعبه
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'branch_id');
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر شعب فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر شعب غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای فیلتر شعبه پیش‌فرض
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
     * سکوپ برای فیلتر بر اساس کشور
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * سکوپ برای فیلتر بر اساس شهر
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $city
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', 'LIKE', "%{$city}%");
    }

    /**
     * سکوپ برای جستجو بر اساس کد شعبه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای جستجوی شعب بر اساس نام یا کد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%")
                     ->orWhere('city', 'LIKE', "%{$search}%")
                     ->orWhere('address', 'LIKE', "%{$search}%")
                     ->orWhere('phone', 'LIKE', "%{$search}%")
                     ->orWhere('email', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای دریافت شعب دارای مدیر
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasManager($query)
    {
        return $query->whereNotNull('manager_id');
    }

    /**
     * سکوپ برای دریافت شعب بدون مدیر
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutManager($query)
    {
        return $query->whereNull('manager_id');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل شعبه (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت آدرس کامل شعبه
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $parts = [];
        
        if ($this->address) {
            $parts[] = $this->address;
        }
        
        if ($this->city) {
            $parts[] = $this->city;
        }
        
        if ($this->country) {
            $parts[] = $this->country->name;
        }
        
        return implode('، ', $parts);
    }

    /**
     * دریافت اطلاعات تماس کامل
     * @return string
     */
    public function getContactInfoAttribute()
    {
        $parts = [];
        
        if ($this->phone) {
            $parts[] = 'تلفن: ' . $this->phone;
        }
        
        if ($this->email) {
            $parts[] = 'ایمیل: ' . $this->email;
        }
        
        return implode(' | ', $parts);
    }

    /**
     * دریافت وضعیت به صورت متنی
     * @return string
     */
    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'غیرفعال';
        }
        
        if ($this->is_default) {
            return 'فعال - پیش‌فرض';
        }
        
        return 'فعال';
    }

    /**
     * دریافت کلاس CSS برای وضعیت
     * @return string
     */
    public function getStatusClassAttribute()
    {
        if (!$this->is_active) {
            return 'danger';
        }
        
        if ($this->is_default) {
            return 'success';
        }
        
        return 'info';
    }

    /**
     * بررسی فعال بودن شعبه
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * بررسی پیش‌فرض بودن شعبه
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }

    /**
     * فعال کردن شعبه
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن شعبه
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * تنظیم به‌عنوان شعبه پیش‌فرض شرکت
     * سایر شعب پیش‌فرض شرکت غیرفعال می‌شوند
     * @return bool
     */
    public function setAsDefault()
    {
        // ابتدا تمام شعب پیش‌فرض این شرکت را غیرپیش‌فرض می‌کنیم
        self::where('company_id', $this->company_id)
            ->where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // سپس این شعبه را پیش‌فرض می‌کنیم
        $this->is_default = true;
        return $this->save();
    }

    /**
     * دریافت تعداد کل کارمندان شعبه
     * @return int
     */
    public function getEmployeesCount()
    {
        return $this->employees()->count();
    }

    /**
     * دریافت تعداد کل کاربران شعبه
     * @return int
     */
    public function getUsersCount()
    {
        return $this->users()->count();
    }

    /**
     * دریافت تعداد کل تراکنش‌های شعبه
     * @return int
     */
    public function getTransactionsCount()
    {
        return $this->transactions()->count();
    }

    /**
     * دریافت مجموع تراکنش‌های شعبه
     * @return float
     */
    public function getTotalTransactionsAmount()
    {
        return $this->transactions()->sum('amount');
    }

    /**
     * دریافت نزدیک‌ترین شعبه به یک موقعیت (مثال)
     * @param float $latitude
     * @param float $longitude
     * @return self|null
     */
    public static function getNearestBranch($latitude, $longitude)
    {
        // این متد نیاز به فیلدهای latitude و longitude در جدول دارد
        // که در صورت نیاز می‌توان اضافه کرد
        // به عنوان مثال با استفاده از فرمول هاورسین
        return self::selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
        ->having('distance', '<', 50) // فاصله کمتر از 50 کیلومتر
        ->orderBy('distance')
        ->first();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست شعب یک شرکت برای استفاده در dropdown
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
     * دریافت لیست شعب با فرمت کامل (نام به همراه کد)
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
        
        $branches = $query->orderBy('name')->get();
        $list = [];
        
        foreach ($branches as $branch) {
            $list[$branch->id] = $branch->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت شعبه پیش‌فرض یک شرکت
     * @param int $companyId
     * @return self|null
     */
    public static function getDefaultBranch($companyId)
    {
        return self::byCompany($companyId)
            ->active()
            ->default()
            ->first();
    }

    /**
     * دریافت شعبه بر اساس کد و شرکت
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
     * بررسی وجود شعبه با کد مشخص در شرکت
     * @param int $companyId
     * @param string $code
     * @return bool
     */
    public static function existsByCode($companyId, $code)
    {
        return self::byCompany($companyId)
            ->byCode($code)
            ->exists();
    }

    /**
     * ایجاد خودکار کد شعبه بر اساس نام
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
     * دریافت آمار شعب یک شرکت
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        $active = self::byCompany($companyId)->active()->count();
        $inactive = $total - $active;
        $default = self::byCompany($companyId)->default()->count();
        $hasManager = self::byCompany($companyId)->hasManager()->count();
        $withoutManager = $total - $hasManager;
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'default' => $default,
            'has_manager' => $hasManager,
            'without_manager' => $withoutManager,
        ];
    }

    /**
     * دریافت شعب بر اساس کشور
     * @param int $countryId
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCountry($countryId, $onlyActive = true)
    {
        $query = self::byCountry($countryId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * دریافت شعب بر اساس شهر
     * @param string $city
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCity($city, $onlyActive = true)
    {
        $query = self::byCity($city);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('name')->get();
    }
}