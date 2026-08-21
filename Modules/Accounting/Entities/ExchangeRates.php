<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExchangeRates extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'exchange_rates';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'rate',
        'effective_date',
        'source',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'rate' => 'decimal:6',
        'effective_date' => 'date',
        'created_at' => 'datetime',
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
        'effective_date',
        'created_at',
    ];

    /**
     * منابع معتبر برای دریافت نرخ ارز
     * @var array
     */
    public static $sources = [
        'manual' => 'دستی',
        'api' => 'API',
        'central_bank' => 'بانک مرکزی',
        'exchange_market' => 'بازار آزاد',
        'import' => 'وارد شده',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\ExchangeRatesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\ExchangeRatesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo برای ارز مبدا
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromCurrency()
    {
        return $this->belongsTo(Currencies::class, 'from_currency_id');
    }

    /**
     * رابطه belongsTo برای ارز مقصد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toCurrency()
    {
        return $this->belongsTo(Currencies::class, 'to_currency_id');
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر بر اساس ارز مبدا
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $currencyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromCurrency($query, $currencyId)
    {
        return $query->where('from_currency_id', $currencyId);
    }

    /**
     * سکوپ برای فیلتر بر اساس ارز مقصد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $currencyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToCurrency($query, $currencyId)
    {
        return $query->where('to_currency_id', $currencyId);
    }

    /**
     * سکوپ برای فیلتر بر اساس تاریخ اعتبار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEffectiveDate($query, $date)
    {
        return $query->where('effective_date', $date);
    }

    /**
     * سکوپ برای فیلتر بر اساس بازه تاریخی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('effective_date', [$startDate, $endDate]);
    }

    /**
     * سکوپ برای فیلتر بر اساس منبع
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $source
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * سکوپ برای دریافت آخرین نرخ ارز
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('effective_date', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    /**
     * سکوپ برای دریافت نرخ ارز در یک تاریخ خاص
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAtDate($query, $date)
    {
        return $query->where('effective_date', '<=', $date)
                     ->orderBy('effective_date', 'desc');
    }

    /**
     * سکوپ برای دریافت نرخ‌های معتبر (امروز یا قبل از آن)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid($query)
    {
        return $query->where('effective_date', '<=', Carbon::today());
    }

    /**
     * سکوپ برای دریافت نرخ‌های آینده
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFuture($query)
    {
        return $query->where('effective_date', '>', Carbon::today());
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل نرخ ارز
     * @return string
     */
    public function getFullNameAttribute()
    {
        $from = $this->fromCurrency ? $this->fromCurrency->code : 'نامشخص';
        $to = $this->toCurrency ? $this->toCurrency->code : 'نامشخص';
        return "{$from} به {$to} - " . $this->effective_date->format('Y/m/d');
    }

    /**
     * دریافت نرخ به صورت فرمت شده
     * @return string
     */
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate, 6);
    }

    /**
     * دریافت منبع به صورت متنی
     * @return string
     */
    public function getSourceLabelAttribute()
    {
        return self::$sources[$this->source] ?? $this->source ?? 'نامشخص';
    }

    /**
     * دریافت نرخ معکوس
     * @return float
     */
    public function getInverseRateAttribute()
    {
        return $this->rate > 0 ? 1 / $this->rate : 0;
    }

    /**
     * دریافت نرخ معکوس به صورت فرمت شده
     * @return string
     */
    public function getFormattedInverseRateAttribute()
    {
        return number_format($this->inverse_rate, 6);
    }

    /**
     * دریافت عنوان کامل برای نمایش
     * @return string
     */
    public function getDisplayTitleAttribute()
    {
        $from = $this->fromCurrency ? $this->fromCurrency->code : '?';
        $to = $this->toCurrency ? $this->toCurrency->code : '?';
        return "{$from}/{$to} - " . $this->effective_date->format('Y/m/d');
    }

    /**
     * تبدیل مبلغ از ارز مبدا به ارز مقصد
     * @param float $amount
     * @return float
     */
    public function convert($amount)
    {
        return $amount * $this->rate;
    }

    /**
     * تبدیل مبلغ از ارز مقصد به ارز مبدا
     * @param float $amount
     * @return float
     */
    public function convertReverse($amount)
    {
        return $this->rate > 0 ? $amount / $this->rate : 0;
    }

    /**
     * بررسی اینکه آیا نرخ ارز معتبر است (تاریخ اعتبار امروز یا قبل از آن)
     * @return bool
     */
    public function isValid()
    {
        return $this->effective_date <= Carbon::today();
    }

    /**
     * بررسی اینکه آیا نرخ ارز مربوط به امروز است
     * @return bool
     */
    public function isToday()
    {
        return $this->effective_date->isToday();
    }

    /**
     * محاسبه اختلاف درصدی با نرخ دیگر
     * @param float $otherRate
     * @return float
     */
    public function getDifferencePercentage($otherRate)
    {
        if ($otherRate == 0) {
            return 0;
        }
        return (($this->rate - $otherRate) / $otherRate) * 100;
    }

    /**
     * دریافت تفاوت با نرخ قبلی
     * @return float|null
     */
    public function getDifferenceFromPrevious()
    {
        $previous = self::fromCurrency($this->from_currency_id)
            ->toCurrency($this->to_currency_id)
            ->where('effective_date', '<', $this->effective_date)
            ->latest()
            ->first();

        if ($previous) {
            return $this->rate - $previous->rate;
        }

        return null;
    }

    /**
     * دریافت درصد تغییر نسبت به نرخ قبلی
     * @return float|null
     */
    public function getChangePercentage()
    {
        $previous = self::fromCurrency($this->from_currency_id)
            ->toCurrency($this->to_currency_id)
            ->where('effective_date', '<', $this->effective_date)
            ->latest()
            ->first();

        if ($previous && $previous->rate > 0) {
            return (($this->rate - $previous->rate) / $previous->rate) * 100;
        }

        return null;
    }

    /**
     * دریافت تاریخ به صورت شمسی
     * @return string
     */
    public function getPersianDateAttribute()
    {
        // این متد نیاز به کتابخانه تبدیل تاریخ شمسی دارد
        // مانند: verta یا jdatetime
        return $this->effective_date->format('Y/m/d');
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت نرخ ارز بین دو ارز در یک تاریخ مشخص
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string|null $date
     * @return self|null
     */
    public static function getRate($fromCurrencyId, $toCurrencyId, $date = null)
    {
        $date = $date ?: Carbon::today()->format('Y-m-d');

        return self::fromCurrency($fromCurrencyId)
            ->toCurrency($toCurrencyId)
            ->atDate($date)
            ->first();
    }

    /**
     * دریافت نرخ ارز با ضمانت وجود (اگر نبود ایجاد می‌کند)
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param float $rate
     * @param string|null $date
     * @param string|null $source
     * @return self
     */
    public static function getOrCreate($fromCurrencyId, $toCurrencyId, $rate, $date = null, $source = null)
    {
        $date = $date ?: Carbon::today()->format('Y-m-d');

        $exchangeRate = self::getRate($fromCurrencyId, $toCurrencyId, $date);

        if ($exchangeRate) {
            // به‌روزرسانی نرخ موجود
            $exchangeRate->rate = $rate;
            if ($source) {
                $exchangeRate->source = $source;
            }
            $exchangeRate->save();
            return $exchangeRate;
        }

        // ایجاد نرخ جدید
        return self::create([
            'from_currency_id' => $fromCurrencyId,
            'to_currency_id' => $toCurrencyId,
            'rate' => $rate,
            'effective_date' => $date,
            'source' => $source,
        ]);
    }

    /**
     * دریافت نرخ ارز در یک بازه زمانی
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRatesInRange($fromCurrencyId, $toCurrencyId, $startDate, $endDate)
    {
        return self::fromCurrency($fromCurrencyId)
            ->toCurrency($toCurrencyId)
            ->dateBetween($startDate, $endDate)
            ->orderBy('effective_date')
            ->get();
    }

    /**
     * دریافت آخرین نرخ ارز
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @return self|null
     */
    public static function getLatestRate($fromCurrencyId, $toCurrencyId)
    {
        return self::fromCurrency($fromCurrencyId)
            ->toCurrency($toCurrencyId)
            ->valid()
            ->latest()
            ->first();
    }

    /**
     * دریافت میانگین نرخ ارز در یک بازه زمانی
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function getAverageRate($fromCurrencyId, $toCurrencyId, $startDate, $endDate)
    {
        return self::fromCurrency($fromCurrencyId)
            ->toCurrency($toCurrencyId)
            ->dateBetween($startDate, $endDate)
            ->avg('rate') ?? 0;
    }

    /**
     * دریافت حداکثر نرخ ارز در یک بازه زمانی
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function getMaxRate($fromCurrencyId, $toCurrencyId, $startDate, $endDate)
    {
        return self::fromCurrency($fromCurrencyId)
            ->toCurrency($toCurrencyId)
            ->dateBetween($startDate, $endDate)
            ->max('rate') ?? 0;
    }

    /**
     * دریافت حداقل نرخ ارز در یک بازه زمانی
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public static function getMinRate($fromCurrencyId, $toCurrencyId, $startDate, $endDate)
    {
        return self::fromCurrency($fromCurrencyId)
            ->toCurrency($toCurrencyId)
            ->dateBetween($startDate, $endDate)
            ->min('rate') ?? 0;
    }

    /**
     * دریافت نرخ ارز با فرمت مناسب برای نمایش در نمودار
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getChartData($fromCurrencyId, $toCurrencyId, $startDate, $endDate)
    {
        $rates = self::getRatesInRange($fromCurrencyId, $toCurrencyId, $startDate, $endDate);

        $data = [
            'labels' => [],
            'values' => [],
        ];

        foreach ($rates as $rate) {
            $data['labels'][] = $rate->effective_date->format('Y-m-d');
            $data['values'][] = (float) $rate->rate;
        }

        return $data;
    }

    /**
     * دریافت نرخ‌های ارز بر اساس منبع
     * @param string $source
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getBySource($source, $startDate = null, $endDate = null)
    {
        $query = self::bySource($source);

        if ($startDate && $endDate) {
            $query->dateBetween($startDate, $endDate);
        }

        return $query->orderBy('effective_date', 'desc')->get();
    }

    /**
     * به‌روزرسانی یا ایجاد نرخ ارز به صورت گروهی
     * @param array $rates
     * @param string|null $source
     * @param string|null $date
     * @return \Illuminate\Support\Collection
     */
    public static function syncRates(array $rates, $source = null, $date = null)
    {
        $date = $date ?: Carbon::today()->format('Y-m-d');
        $created = collect();

        foreach ($rates as $rateData) {
            $exchangeRate = self::getOrCreate(
                $rateData['from_currency_id'],
                $rateData['to_currency_id'],
                $rateData['rate'],
                $date,
                $source
            );
            $created->push($exchangeRate);
        }

        return $created;
    }

    /**
     * دریافت نرخ ارز با احتساب نرخ معکوس
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string|null $date
     * @return array
     */
    public static function getRateWithReverse($fromCurrencyId, $toCurrencyId, $date = null)
    {
        $rate = self::getRate($fromCurrencyId, $toCurrencyId, $date);

        if (!$rate) {
            return null;
        }

        return [
            'rate' => $rate,
            'inverse_rate' => $rate->inverse_rate,
            'formatted_rate' => $rate->formatted_rate,
            'formatted_inverse_rate' => $rate->formatted_inverse_rate,
        ];
    }

    /**
     * دریافت خلاصه آماری نرخ‌های ارز
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getStatistics($fromCurrencyId, $toCurrencyId, $startDate, $endDate)
    {
        $rates = self::getRatesInRange($fromCurrencyId, $toCurrencyId, $startDate, $endDate);

        if ($rates->isEmpty()) {
            return [
                'count' => 0,
                'avg' => 0,
                'min' => 0,
                'max' => 0,
                'first' => null,
                'last' => null,
                'change' => 0,
                'change_percentage' => 0,
            ];
        }

        $avg = $rates->avg('rate');
        $min = $rates->min('rate');
        $max = $rates->max('rate');
        $first = $rates->first();
        $last = $rates->last();

        return [
            'count' => $rates->count(),
            'avg' => $avg,
            'min' => $min,
            'max' => $max,
            'first' => $first,
            'last' => $last,
            'change' => $last->rate - $first->rate,
            'change_percentage' => $first->rate > 0 ? (($last->rate - $first->rate) / $first->rate) * 100 : 0,
        ];
    }
}