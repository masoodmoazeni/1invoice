<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currencies extends Model
{
    use HasFactory;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'currencies';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'rounding',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'decimal_places' => 'integer',
        'rounding' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند (اختیاری)
     * @var array
     */
    protected $hidden = [];

    /**
     * مقدار پیش‌فرض برای ارقام اعشاری
     */
    const DEFAULT_DECIMAL_PLACES = 2;

    /**
     * مقدار پیش‌فرض برای گرد کردن
     */
    const DEFAULT_ROUNDING = 0.01;

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\CurrenciesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\CurrenciesFactory::new();
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر ارزهای فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر ارزهای غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای جستجو بر اساس کد ارز
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', strtoupper($code));
    }

    /**
     * سکوپ برای جستجوی ارز بر اساس نام
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%")
                     ->orWhere('symbol', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای ارزهایی با تعداد ارقام اعشاری خاص
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $places
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDecimalPlaces($query, $places)
    {
        return $query->where('decimal_places', $places);
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل ارز (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت فرمت نمایشی ارز با نماد
     * @return string
     */
    public function getDisplayFormatAttribute()
    {
        return $this->symbol ? $this->symbol . ' (' . $this->code . ')' : $this->code;
    }

    /**
     * دریافت فرمت برای نمایش مبلغ با ارز
     * @param float|int $amount
     * @param bool $withSymbol
     * @return string
     */
    public function formatAmount($amount, $withSymbol = true)
    {
        $formatted = number_format(
            $amount,
            $this->decimal_places,
            '.',
            ','
        );

        if ($withSymbol && $this->symbol) {
            return $this->symbol . ' ' . $formatted;
        }

        return $formatted;
    }

    /**
     * گرد کردن مبلغ بر اساس مقدار rounding این ارز
     * @param float|int $amount
     * @return float
     */
    public function roundAmount($amount)
    {
        if ($this->rounding <= 0) {
            return round($amount, $this->decimal_places);
        }

        return round($amount / $this->rounding) * $this->rounding;
    }

    /**
     * بررسی فعال بودن ارز
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * فعال کردن ارز
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن ارز
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * بررسی اینکه آیا ارز از اعشار پشتیبانی می‌کند
     * @return bool
     */
    public function hasDecimals()
    {
        return $this->decimal_places > 0;
    }

    /**
     * دریافت ضریب تبدیل بر اساس تعداد ارقام اعشاری
     * @return int
     */
    public function getDecimalFactor()
    {
        return pow(10, $this->decimal_places);
    }

    /**
     * تبدیل مقدار به واحد پایه (با در نظر گرفتن اعشار)
     * @param float|int $amount
     * @return int
     */
    public function toBaseUnit($amount)
    {
        return (int) round($amount * $this->getDecimalFactor());
    }

    /**
     * تبدیل از واحد پایه به واحد اصلی
     * @param int $amount
     * @return float
     */
    public function fromBaseUnit($amount)
    {
        return $amount / $this->getDecimalFactor();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست ارزهای فعال به‌صورت کلید-مقدار برای استفاده در dropdown
     * @return array
     */
    public static function getActiveList()
    {
        return self::active()
            ->orderBy('code')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * دریافت لیست ارزهای فعال با کد به‌عنوان کلید
     * @return array
     */
    public static function getActiveListByCode()
    {
        return self::active()
            ->orderBy('code')
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * دریافت ارز پیش‌فرض (اولین ارز فعال)
     * @return self|null
     */
    public static function getDefault()
    {
        return self::active()->first();
    }

    /**
     * بررسی وجود ارز با کد مشخص
     * @param string $code
     * @return bool
     */
    public static function existsByCode($code)
    {
        return self::where('code', strtoupper($code))->exists();
    }

    /**
     * دریافت ارز بر اساس کد (با کش کردن)
     * @param string $code
     * @return self|null
     */
    public static function getByCode($code)
    {
        return self::where('code', strtoupper($code))->first();
    }

    /**
     * دریافت نماد ارز بر اساس کد
     * @param string $code
     * @return string|null
     */
    public static function getSymbolByCode($code)
    {
        $currency = self::getByCode($code);
        return $currency ? $currency->symbol : null;
    }
}