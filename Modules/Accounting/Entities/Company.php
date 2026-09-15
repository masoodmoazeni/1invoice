<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\System\Entities\Country;
use Modules\System\Entities\Currency;
use Modules\System\Entities\Language;
use Modules\System\Entities\TimeZone;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'legal_name',
        'country_id',
        'base_currency_id',
        'language_id',
        'timezone_id',
        'tax_number',
        'registration_number',
        'phone',
        'mobile',
        'email',
        'website',
        'address',
        'postal_code',
        'city',
        'state',
        'logo',
        'fiscal_year_start_month',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'fiscal_year_start_month' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    // ========== روابط ==========

    /**
     * Get the country that owns the company.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the base currency of the company.
     */
    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    /**
     * Get the language of the company.
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the timezone of the company.
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    // ========== متدهای کاربردی ==========

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to search companies by name or code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%")
              ->orWhere('legal_name', 'LIKE', "%{$search}%")
              ->orWhere('tax_number', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get the full address as a formatted string.
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get the company display name (code - name).
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    /**
     * Check if company has a logo.
     */
    public function hasLogo()
    {
        return !empty($this->logo) && file_exists(storage_path('app/public/' . $this->logo));
    }

    /**
     * Get the logo URL.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        return null;
    }

    /**
     * Get the fiscal year start month name.
     */
    public function getFiscalYearStartMonthNameAttribute()
    {
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        return $months[$this->fiscal_year_start_month] ?? 'January';
    }

    /**
     * Check if the company is currently in fiscal year.
     */
    public function isInFiscalYear($date = null)
    {
        $date = $date ?? now();
        $currentMonth = $date->month;
        $fiscalStartMonth = $this->fiscal_year_start_month;

        if ($currentMonth >= $fiscalStartMonth) {
            return true;
        }

        return false;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            // اگر کد وارد نشده بود، به‌صورت خودکار تولید کن
            if (empty($company->code)) {
                $company->code = 'COMP-' . strtoupper(uniqid());
            }
        });
    }
}
