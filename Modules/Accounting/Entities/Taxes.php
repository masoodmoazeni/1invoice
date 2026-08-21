<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Taxes extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'taxes';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'country_id',
        'code',
        'name',
        'description',
        'rate',
        'tax_kind',
        'calculation_method',
        'account_id',
        'is_inclusive',
        'is_default',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'rate' => 'decimal:2',
        'is_inclusive' => 'boolean',
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

    // ========== ثابت‌های مربوط به نوع مالیات ==========
    
    /**
     * نوع مالیات: درصدی
     */
    const TAX_KIND_PERCENTAGE = 'percentage';
    
    /**
     * نوع مالیات: مبلغ ثابت
     */
    const TAX_KIND_FIXED = 'fixed';

    /**
     * لیست انواع مالیات معتبر
     * @var array
     */
    public static $taxKinds = [
        self::TAX_KIND_PERCENTAGE,
        self::TAX_KIND_FIXED,
    ];

    // ========== ثابت‌های مربوط به روش محاسبه ==========

    /**
     * محاسبه قبل از اعمال تخفیف
     */
    const CALC_BEFORE_DISCOUNT = 'before_discount';
    
    /**
     * محاسبه بعد از اعمال تخفیف
     */
    const CALC_AFTER_DISCOUNT = 'after_discount';
    
    /**
     * محاسبه مستقل (بدون در نظر گرفتن تخفیف)
     */
    const CALC_EXCLUSIVE = 'exclusive';

    /**
     * لیست روش‌های محاسبه معتبر
     * @var array
     */
    public static $calculationMethods = [
        self::CALC_BEFORE_DISCOUNT,
        self::CALC_AFTER_DISCOUNT,
        self::CALC_EXCLUSIVE,
    ];

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\TaxesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\TaxesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر مالیات متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsTo با مدل Country
     * هر مالیات متعلق به یک کشور است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    /**
     * رابطه belongsTo با مدل Account (اختیاری)
     * هر مالیات می‌تواند به یک حساب مالیاتی متصل باشد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Accounts::class, 'account_id');
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر مالیات‌های فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر مالیات‌های غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای فیلتر مالیات‌های پیش‌فرض
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * سکوپ برای فیلتر مالیات‌های درصدی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePercentage($query)
    {
        return $query->where('tax_kind', self::TAX_KIND_PERCENTAGE);
    }

    /**
     * سکوپ برای فیلتر مالیات‌های مبلغ ثابت
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFixed($query)
    {
        return $query->where('tax_kind', self::TAX_KIND_FIXED);
    }

    /**
     * سکوپ برای فیلتر مالیات‌های درون‌زا (در قیمت لحاظ شده)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInclusive($query)
    {
        return $query->where('is_inclusive', true);
    }

    /**
     * سکوپ برای فیلتر مالیات‌های برون‌زا (به قیمت اضافه می‌شود)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExclusive($query)
    {
        return $query->where('is_inclusive', false);
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
     * سکوپ برای فیلتر بر اساس کد مالیات
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای جستجوی مالیات بر اساس نام یا کد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%")
                     ->orWhere('description', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای فیلتر بر اساس روش محاسبه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $method
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCalculationMethod($query, $method)
    {
        return $query->where('calculation_method', $method);
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل مالیات (نام به همراه نرخ در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        if ($this->tax_kind === self::TAX_KIND_PERCENTAGE) {
            return $this->name . ' (' . $this->rate . '%)';
        }
        return $this->name . ' (' . $this->rate . ' ' . ($this->country->currency->symbol ?? '') . ')';
    }

    /**
     * دریافت توضیحات کامل مالیات با جزئیات
     * @return string
     */
    public function getFullDescriptionAttribute()
    {
        $parts = [];
        $parts[] = "نوع: " . ($this->tax_kind === self::TAX_KIND_PERCENTAGE ? 'درصدی' : 'مبلغ ثابت');
        $parts[] = "نرخ: " . $this->rate . ($this->tax_kind === self::TAX_KIND_PERCENTAGE ? '%' : '');
        $parts[] = "محاسبه: " . $this->getCalculationMethodLabel();
        $parts[] = $this->is_inclusive ? 'مالیات درون‌زا' : 'مالیات برون‌زا';
        
        if ($this->description) {
            $parts[] = $this->description;
        }
        
        return implode(' - ', $parts);
    }

    /**
     * دریافت برچسب روش محاسبه به فارسی
     * @return string
     */
    public function getCalculationMethodLabel()
    {
        $labels = [
            self::CALC_BEFORE_DISCOUNT => 'قبل از تخفیف',
            self::CALC_AFTER_DISCOUNT => 'بعد از تخفیف',
            self::CALC_EXCLUSIVE => 'مستقل',
        ];
        
        return $labels[$this->calculation_method] ?? $this->calculation_method;
    }

    /**
     * دریافت برچسب نوع مالیات به فارسی
     * @return string
     */
    public function getTaxKindLabelAttribute()
    {
        $labels = [
            self::TAX_KIND_PERCENTAGE => 'درصدی',
            self::TAX_KIND_FIXED => 'مبلغ ثابت',
        ];
        
        return $labels[$this->tax_kind] ?? $this->tax_kind;
    }

    /**
     * محاسبه مالیات برای یک مبلغ مشخص
     * @param float $amount
     * @return float
     */
    public function calculateTax($amount)
    {
        if ($this->tax_kind === self::TAX_KIND_PERCENTAGE) {
            return $amount * ($this->rate / 100);
        } else {
            // مبلغ ثابت
            return $this->rate;
        }
    }

    /**
     * محاسبه مالیات با در نظر گرفتن روش محاسبه و تخفیف
     * @param float $amount
     * @param float $discount
     * @return float
     */
    public function calculateTaxWithDiscount($amount, $discount = 0)
    {
        $baseAmount = $amount;
        
        switch ($this->calculation_method) {
            case self::CALC_BEFORE_DISCOUNT:
                // محاسبه قبل از تخفیف
                $baseAmount = $amount;
                break;
                
            case self::CALC_AFTER_DISCOUNT:
                // محاسبه بعد از تخفیف
                $baseAmount = $amount - $discount;
                break;
                
            case self::CALC_EXCLUSIVE:
                // محاسبه مستقل (بدون تغییر)
                $baseAmount = $amount;
                break;
        }
        
        // اگر مالیات درون‌زا باشد، باید از مبلغ پایه کم شود
        if ($this->is_inclusive) {
            $taxRate = $this->rate / 100;
            $taxAmount = ($baseAmount * $taxRate) / (1 + $taxRate);
        } else {
            $taxAmount = $this->calculateTax($baseAmount);
        }
        
        return $taxAmount;
    }

    /**
     * بررسی فعال بودن مالیات
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * بررسی پیش‌فرض بودن مالیات
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }

    /**
     * بررسی درون‌زا بودن مالیات
     * @return bool
     */
    public function isInclusive()
    {
        return (bool) $this->is_inclusive;
    }

    /**
     * فعال کردن مالیات
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن مالیات
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * تنظیم به‌عنوان مالیات پیش‌فرض شرکت
     * سایر مالیات‌های پیش‌فرض شرکت غیرفعال می‌شوند
     * @return bool
     */
    public function setAsDefault()
    {
        // ابتدا تمام مالیات‌های پیش‌فرض این شرکت را غیرپیش‌فرض می‌کنیم
        self::where('company_id', $this->company_id)
            ->where('is_default', true)
            ->update(['is_default' => false]);
        
        // سپس این مالیات را پیش‌فرض می‌کنیم
        $this->is_default = true;
        return $this->save();
    }

    /**
     * دریافت نرخ مالیات به‌صورت درصد (برای نمایش)
     * @return float
     */
    public function getRatePercentage()
    {
        if ($this->tax_kind === self::TAX_KIND_PERCENTAGE) {
            return $this->rate;
        }
        return 0;
    }

    /**
     * دریافت نرخ مالیات به‌صورت اعشاری
     * @return float
     */
    public function getRateDecimal()
    {
        if ($this->tax_kind === self::TAX_KIND_PERCENTAGE) {
            return $this->rate / 100;
        }
        return $this->rate;
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست مالیات‌های فعال یک شرکت برای استفاده در dropdown
     * @param int $companyId
     * @return array
     */
    public static function getActiveList($companyId)
    {
        return self::active()
            ->byCompany($companyId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * دریافت مالیات پیش‌فرض یک شرکت
     * @param int $companyId
     * @return self|null
     */
    public static function getDefaultTax($companyId)
    {
        return self::active()
            ->byCompany($companyId)
            ->default()
            ->first();
    }

    /**
     * دریافت مالیات‌های یک کشور
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCountry($countryId)
    {
        return self::active()
            ->byCountry($countryId)
            ->orderBy('name')
            ->get();
    }

    /**
     * دریافت مالیات‌های مناسب برای یک مبلغ و شرایط خاص
     * @param int $companyId
     * @param float $amount
     * @param float $discount
     * @return array
     */
    public static function calculateAllTaxes($companyId, $amount, $discount = 0)
    {
        $taxes = self::active()
            ->byCompany($companyId)
            ->get();
        
        $results = [];
        $totalTax = 0;
        
        foreach ($taxes as $tax) {
            $taxAmount = $tax->calculateTaxWithDiscount($amount, $discount);
            $results[] = [
                'tax' => $tax,
                'amount' => $taxAmount,
            ];
            $totalTax += $taxAmount;
        }
        
        return [
            'taxes' => $results,
            'total_tax' => $totalTax,
        ];
    }
}