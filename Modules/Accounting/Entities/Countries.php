<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Countries extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'countries';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'iso2',
        'iso3',
        'name',
        'numeric_code',
        'phone_code',
        'capital',
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
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\CountriesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\CountriesFactory::new();
    }

    // ========== سکوپ‌های پرکاربرد ==========

    /**
     * سکوپ برای فیلتر کشورهای فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای جستجو بر اساس کد دو حرفی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $iso2
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIso2($query, $iso2)
    {
        return $query->where('iso2', $iso2);
    }

    // ========== متدهای کمکی ==========

    /**
     * دریافت نام کامل کشور به همراه کد تلفن
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->phone_code . ')';
    }

    /**
     * بررسی فعال بودن کشور
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }
}