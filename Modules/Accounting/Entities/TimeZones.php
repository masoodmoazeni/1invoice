<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeZones extends Model
{
    use HasFactory;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'time_zones';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'country_id',
        'name',
        'utc_offset',
        'is_default',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
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
     * @return \Modules\Accounting\Database\factories\TimeZonesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\TimeZonesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Countries
     * هر منطقه زمانی متعلق به یک کشور است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر منطقه‌های زمانی پیش‌فرض
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * سکوپ برای فیلتر منطقه‌های زمانی غیرپیش‌فرض
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotDefault($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * سکوپ برای جستجو بر اساس کشور
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * سکوپ برای جستجو بر اساس نام منطقه زمانی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل منطقه زمانی به همراه UTC Offset
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (UTC' . $this->utc_offset . ')';
    }

    /**
     * بررسی پیش‌فرض بودن منطقه زمانی
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }

    /**
     * تنظیم کردن این منطقه زمانی به‌عنوان پیش‌فرض برای کشور خود
     * و غیرفعال کردن پیش‌فرض سایر منطقه‌های زمانی همان کشور
     * 
     * @return bool
     */
    public function setAsDefault()
    {
        // ابتدا تمام منطقه‌های زمانی این کشور را غیرپیش‌فرض می‌کنیم
        self::where('country_id', $this->country_id)
            ->update(['is_default' => false]);
        
        // سپس این منطقه زمانی را پیش‌فرض می‌کنیم
        $this->is_default = true;
        return $this->save();
    }

    /**
     * دریافت UTC Offset به‌صورت عددی (برای محاسبات)
     * @return float
     */
    public function getUtcOffsetInHours()
    {
        // تبدیل +03:30 به 3.5
        $parts = explode(':', $this->utc_offset);
        $hours = (int) $parts[0];
        $minutes = isset($parts[1]) ? (int) $parts[1] / 60 : 0;
        
        return $hours + $minutes;
    }
}