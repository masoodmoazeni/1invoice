<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JournalEntryLineDimension extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'journal_entry_line_dimensions';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'journal_entry_line_id',
        'dimension_value_id',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند
     * @var array
     */
    protected $hidden = [];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\JournalEntryLineDimensionsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\JournalEntryLineDimensionsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل JournalEntryLine
     * هر رکورد متعلق به یک ردیف سند است
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journalEntryLine()
    {
        return $this->belongsTo(JournalEntryLines::class, 'journal_entry_line_id');
    }

    /**
     * رابطه belongsTo با مدل DimensionValue
     * هر رکورد متعلق به یک مقدار بعد است
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dimensionValue()
    {
        return $this->belongsTo(DimensionValues::class, 'dimension_value_id');
    }

    /**
     * دریافت بعد از طریق مقدار بعد
     *
     * @return Dimensions|null
     */
    public function getDimensionAttribute()
    {
        return $this->dimensionValue ? $this->dimensionValue->dimension : null;
    }

    /**
     * دریافت نام بعد
     *
     * @return string|null
     */
    public function getDimensionNameAttribute()
    {
        return $this->dimension ? $this->dimension->name : null;
    }

    /**
     * دریافت نام مقدار بعد
     *
     * @return string|null
     */
    public function getDimensionValueNameAttribute()
    {
        return $this->dimensionValue ? $this->dimensionValue->name : null;
    }

    /**
     * دریافت نام کامل (بعد - مقدار)
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        $dimensionName = $this->dimension_name;
        $valueName = $this->dimension_value_name;

        if ($dimensionName && $valueName) {
            return $dimensionName . ' - ' . $valueName;
        }

        return 'نامشخص';
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر بر اساس ردیف سند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $lineId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLine($query, $lineId)
    {
        return $query->where('journal_entry_line_id', $lineId);
    }

    /**
     * سکوپ برای فیلتر بر اساس مقدار بعد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $dimensionValueId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDimensionValue($query, $dimensionValueId)
    {
        return $query->where('dimension_value_id', $dimensionValueId);
    }

    /**
     * سکوپ برای فیلتر بر اساس بعد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $dimensionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDimension($query, $dimensionId)
    {
        return $query->whereHas('dimensionValue', function ($q) use ($dimensionId) {
            $q->where('dimension_id', $dimensionId);
        });
    }

    /**
     * سکوپ برای فیلتر بر اساس نوع بعد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dimensionType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDimensionType($query, $dimensionType)
    {
        return $query->whereHas('dimensionValue.dimension', function ($q) use ($dimensionType) {
            $q->where('type', $dimensionType);
        });
    }

    /**
     * سکوپ برای دریافت ارتباطات با مقادیر فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithActiveDimensionValues($query)
    {
        return $query->whereHas('dimensionValue', function ($q) {
            $q->whereHas('dimension', function ($query) {
                $query->where('is_active', true);
            });
        });
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * بررسی وجود ارتباط معتبر
     * @return bool
     */
    public function isValid()
    {
        return $this->journalEntryLine && $this->dimensionValue;
    }

    /**
     * دریافت اطلاعات کامل برای گزارش
     * @return array
     */
    public function getReportInfo()
    {
        return [
            'id' => $this->id,
            'line_id' => $this->journal_entry_line_id,
            'dimension_value_id' => $this->dimension_value_id,
            'dimension_name' => $this->dimension_name,
            'dimension_value_name' => $this->dimension_value_name,
            'full_name' => $this->full_name,
            'created_at' => $this->created_at,
        ];
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت ابعاد یک ردیف سند
     * @param int $lineId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getDimensionsForLine($lineId)
    {
        return self::byLine($lineId)
            ->with(['dimensionValue', 'dimensionValue.dimension'])
            ->get();
    }

    /**
     * دریافت مقادیر بعد برای یک ردیف سند
     * @param int $lineId
     * @param int $dimensionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getDimensionValuesForLine($lineId, $dimensionId)
    {
        return self::byLine($lineId)
            ->byDimension($dimensionId)
            ->with('dimensionValue')
            ->get()
            ->pluck('dimensionValue');
    }

    /**
     * دریافت ردیف‌های سند برای یک مقدار بعد
     * @param int $dimensionValueId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLinesForDimensionValue($dimensionValueId)
    {
        return self::byDimensionValue($dimensionValueId)
            ->with('journalEntryLine')
            ->get()
            ->pluck('journalEntryLine');
    }

    /**
     * دریافت ردیف‌های سند برای یک بعد
     * @param int $dimensionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLinesForDimension($dimensionId)
    {
        return self::byDimension($dimensionId)
            ->with(['journalEntryLine', 'dimensionValue'])
            ->get();
    }

    /**
     * ایجاد ارتباطات ابعاد برای یک ردیف سند
     * @param int $lineId
     * @param array $dimensionValueIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createForLine($lineId, array $dimensionValueIds)
    {
        $created = collect();

        // حذف ارتباطات قبلی (در صورت نیاز)
        // self::byLine($lineId)->delete();

        foreach ($dimensionValueIds as $dimensionValueId) {
            // بررسی تکراری نبودن
            if (!self::byLine($lineId)->byDimensionValue($dimensionValueId)->exists()) {
                $record = self::create([
                    'journal_entry_line_id' => $lineId,
                    'dimension_value_id' => $dimensionValueId,
                ]);
                $created->push($record);
            }
        }

        return $created;
    }

    /**
     * به‌روزرسانی ارتباطات ابعاد یک ردیف سند
     * @param int $lineId
     * @param array $dimensionValueIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function updateForLine($lineId, array $dimensionValueIds)
    {
        // حذف ارتباطات قبلی
        self::byLine($lineId)->delete();

        // ایجاد ارتباطات جدید
        return self::createForLine($lineId, $dimensionValueIds);
    }

    /**
     * دریافت آمار ارتباطات ابعاد
     * @param int|null $dimensionId
     * @return array
     */
    public static function getStatistics($dimensionId = null)
    {
        $query = self::query();

        if ($dimensionId) {
            $query->byDimension($dimensionId);
        }

        $total = $query->count();
        $uniqueLines = $query->distinct('journal_entry_line_id')->count('journal_entry_line_id');
        $uniqueValues = $query->distinct('dimension_value_id')->count('dimension_value_id');

        // دریافت آمار بر اساس بعد
        $byDimension = self::with('dimensionValue.dimension')
            ->select('dimension_value_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('dimension_value_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->dimensionValue->dimension_id ?? 'unknown';
            })
            ->map(function ($items) {
                return [
                    'count' => $items->sum('count'),
                    'unique_values' => $items->count(),
                ];
            });

        return [
            'total_connections' => $total,
            'unique_lines' => $uniqueLines,
            'unique_values' => $uniqueValues,
            'by_dimension' => $byDimension,
        ];
    }

    /**
     * دریافت ابعاد پرکاربرد در ردیف‌های سند
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getMostUsedDimensions($limit = 10)
    {
        return self::select('dimension_value_id')
            ->selectRaw('COUNT(*) as usage_count')
            ->groupBy('dimension_value_id')
            ->with('dimensionValue.dimension')
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * دریافت ابعاد یک سند کامل
     * @param int $entryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getDimensionsForJournalEntry($entryId)
    {
        return self::whereHas('journalEntryLine', function ($q) use ($entryId) {
            $q->where('journal_entry_id', $entryId);
        })
        ->with(['dimensionValue', 'dimensionValue.dimension'])
        ->get()
        ->unique('dimension_value_id');
    }

    /**
     * کپی ارتباطات ابعاد از یک ردیف به ردیف دیگر
     * @param int $sourceLineId
     * @param int $targetLineId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function copyFromLine($sourceLineId, $targetLineId)
    {
        $sourceDimensions = self::byLine($sourceLineId)->get();
        $dimensionValueIds = $sourceDimensions->pluck('dimension_value_id')->toArray();

        return self::createForLine($targetLineId, $dimensionValueIds);
    }
}
