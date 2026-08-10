<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Languages extends Model
{
    use HasFactory;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'languages';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
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
     * فیلدهایی که نباید در JSON نمایش داده شوند (اختیاری)
     * @var array
     */
    protected $hidden = [];

    /**
     * ثابت‌های مربوط به جهت نوشتار
     */
    const DIRECTION_LTR = 'ltr';
    const DIRECTION_RTL = 'rtl';

    /**
     * لیست جهات نوشتار معتبر
     * @var array
     */
    public static $directions = [
        self::DIRECTION_LTR,
        self::DIRECTION_RTL,
    ];

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\LanguagesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\LanguagesFactory::new();
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر زبان‌های فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر زبان‌های غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای فیلتر زبان‌های راست‌به‌چپ (RTL)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRtl($query)
    {
        return $query->where('direction', self::DIRECTION_RTL);
    }

    /**
     * سکوپ برای فیلتر زبان‌های چپ‌به‌راست (LTR)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLtr($query)
    {
        return $query->where('direction', self::DIRECTION_LTR);
    }

    /**
     * سکوپ برای جستجو بر اساس کد زبان
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای جستجوی زبان بر اساس نام (به انگلیسی یا بومی)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('native_name', 'LIKE', "%{$search}%");
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل زبان (نام انگلیسی همراه با نام بومی در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        if ($this->native_name && $this->native_name !== $this->name) {
            return $this->name . ' (' . $this->native_name . ')';
        }
        return $this->name;
    }

    /**
     * دریافت نام نمایشی بر اساس اولویت (بومی یا انگلیسی)
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->native_name ?? $this->name;
    }

    /**
     * دریافت نشانگر جهت نوشتار به‌صورت نمادین
     * @return string
     */
    public function getDirectionSymbolAttribute()
    {
        return $this->direction === self::DIRECTION_RTL ? '→' : '←';
    }

    /**
     * دریافت کلاس CSS برای جهت نوشتار
     * @return string
     */
    public function getDirectionClassAttribute()
    {
        return $this->direction === self::DIRECTION_RTL ? 'rtl-text' : 'ltr-text';
    }

    /**
     * بررسی فعال بودن زبان
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * بررسی راست‌به‌چپ بودن زبان
     * @return bool
     */
    public function isRtl()
    {
        return $this->direction === self::DIRECTION_RTL;
    }

    /**
     * بررسی چپ‌به‌راست بودن زبان
     * @return bool
     */
    public function isLtr()
    {
        return $this->direction === self::DIRECTION_LTR;
    }

    /**
     * فعال کردن زبان
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن زبان
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * تغییر جهت نوشتار
     * @param string $direction
     * @return bool
     */
    public function setDirection($direction)
    {
        if (!in_array($direction, self::$directions)) {
            return false;
        }
        
        $this->direction = $direction;
        return $this->save();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست زبان‌های فعال به‌صورت کلید-مقدار برای استفاده در dropdown
     * @return array
     */
    public static function getActiveList()
    {
        return self::active()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * دریافت لیست زبان‌های فعال با کد به‌عنوان کلید
     * @return array
     */
    public static function getActiveListByCode()
    {
        return self::active()
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * دریافت زبان پیش‌فرض (اولین زبان فعال)
     * @return self|null
     */
    public static function getDefault()
    {
        return self::active()->first();
    }

    /**
     * بررسی وجود زبان با کد مشخص
     * @param string $code
     * @return bool
     */
    public static function existsByCode($code)
    {
        return self::where('code', $code)->exists();
    }
}