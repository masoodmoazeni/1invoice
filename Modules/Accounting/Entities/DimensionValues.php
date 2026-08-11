<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class DimensionValues extends Model
{
    use HasFactory;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'dimension_values';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'dimension_id',
        'code',
        'name',
        'parent_id',
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
     * @return \Modules\Accounting\Database\factories\DimensionValuesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\DimensionValuesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Dimension
     * هر مقدار متعلق به یک بعد است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dimension()
    {
        return $this->belongsTo(Dimensions::class, 'dimension_id');
    }

    /**
     * رابطه belongsTo برای مقدار والد
     * دریافت مقدار بالادستی (سطح بالاتر)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * رابطه hasMany برای مقادیر فرزند
     * دریافت تمام مقادیر زیرمجموعه
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * رابطه hasMany برای مقادیر فرزند فعال (با فیلتر از طریق بعد)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeChildren()
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->whereHas('dimension', function ($q) {
                        $q->where('is_active', true);
                    });
    }

    /**
     * رابطه hasMany برای ارتباط با ردیف‌های سند حسابداری
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalEntryLines()
    {
        // این رابطه فرض می‌کند که در جدول journal_entry_lines
        // فیلد dimension_value_id وجود دارد
        return $this->hasMany(JournalEntryLines::class, 'dimension_value_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌ها
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'dimension_value_id');
    }

    /**
     * دریافت تمام مقادیر فرزند به صورت بازگشتی (تا هر سطح)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllChildren()
    {
        $children = collect();
        
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }
        
        return $children;
    }

    /**
     * دریافت تمام مقادیر والد به صورت بازگشتی (تا ریشه)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllParents()
    {
        $parents = collect();
        
        if ($this->parent) {
            $parents->push($this->parent);
            $parents = $parents->merge($this->parent->getAllParents());
        }
        
        return $parents;
    }

    /**
     * دریافت مسیر کامل مقدار (از ریشه تا خودش)
     * 
     * @return string
     */
    public function getFullPath()
    {
        $path = collect([$this->name]);
        $parent = $this->parent;
        
        while ($parent) {
            $path->prepend($parent->name);
            $parent = $parent->parent;
        }
        
        return $path->implode(' / ');
    }

    /**
     * دریافت سطح مقدار در ساختار سلسله‌مراتبی
     * (سطح 0 برای ریشه، سطح 1 برای فرزند، و ...)
     * 
     * @return int
     */
    public function getLevel()
    {
        $level = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    /**
     * دریافت تعداد کل ردیف‌های سند مربوط به این مقدار
     * @return int
     */
    public function getEntriesCount()
    {
        return $this->journalEntryLines()->count();
    }

    /**
     * دریافت مجموع مبالغ ردیف‌های سند مربوط به این مقدار
     * @return array
     */
    public function getTotalAmounts()
    {
        $totalDebit = $this->journalEntryLines()->sum('debit');
        $totalCredit = $this->journalEntryLines()->sum('credit');
        
        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'difference' => $totalDebit - $totalCredit,
        ];
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر بر اساس بعد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $dimensionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDimension($query, $dimensionId)
    {
        return $query->where('dimension_id', $dimensionId);
    }

    /**
     * سکوپ برای دریافت مقادیر ریشه (بدون والد)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * سکوپ برای دریافت مقادیر غیرریشه (دارای والد)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotRoot($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * سکوپ برای فیلتر بر اساس کد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای جستجوی مقادیر بر اساس نام یا کد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای دریافت مقادیر دارای فرزند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasChildren($query)
    {
        return $query->has('children');
    }

    /**
     * سکوپ برای دریافت مقادیر بدون فرزند (برگ)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeaf($query)
    {
        return $query->doesntHave('children');
    }

    /**
     * سکوپ برای دریافت مقادیر دارای تراکنش
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasTransactions($query)
    {
        return $query->has('journalEntryLines');
    }

    /**
     * سکوپ برای دریافت مقادیر بدون تراکنش
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutTransactions($query)
    {
        return $query->doesntHave('journalEntryLines');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل مقدار (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت نام مقدار با پیش‌فراز (برای نمایش درختواره)
     * @return string
     */
    public function getIndentedNameAttribute()
    {
        $indent = str_repeat('— ', $this->getLevel());
        return $indent . $this->name;
    }

    /**
     * دریافت مسیر کامل کد (برای ساخت کد فرزند)
     * @return string
     */
    public function getFullCodePath()
    {
        $codes = collect([$this->code]);
        $parent = $this->parent;
        
        while ($parent) {
            $codes->prepend($parent->code);
            $parent = $parent->parent;
        }
        
        return $codes->implode('.');
    }

    /**
     * دریافت نام کامل بعد به همراه مقدار
     * @return string
     */
    public function getDimensionValueAttribute()
    {
        return ($this->dimension ? $this->dimension->name : '') . ' - ' . $this->name;
    }

    /**
     * بررسی اینکه آیا مقدار ریشه است
     * @return bool
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * بررسی اینکه آیا مقدار برگ (بدون فرزند) است
     * @return bool
     */
    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    /**
     * بررسی اینکه آیا مقدار دارای تراکنش است
     * @return bool
     */
    public function hasTransactions()
    {
        return $this->journalEntryLines()->exists();
    }

    /**
     * ایجاد کد برای فرزند جدید
     * @return string
     */
    public function generateChildCode()
    {
        $maxCode = self::where('parent_id', $this->id)
            ->orderBy('code', 'desc')
            ->first();
        
        if ($maxCode) {
            $lastPart = explode('.', $maxCode->code);
            $newNumber = (int) end($lastPart) + 1;
            return $this->code . '.' . $newNumber;
        }
        
        return $this->code . '.1';
    }

    /**
     * دریافت درختواره مقادیر به صورت بازگشتی
     * @param int|null $parentId
     * @return \Illuminate\Support\Collection
     */
    public static function getTree($parentId = null)
    {
        $values = self::where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
        
        foreach ($values as $value) {
            $value->children = self::getTree($value->id);
        }
        
        return $values;
    }

    /**
     * دریافت درختواره به صورت لیست تخت با تورفتگی
     * @param int|null $parentId
     * @param int $level
     * @return \Illuminate\Support\Collection
     */
    public static function getFlatTree($parentId = null, $level = 0)
    {
        $result = collect();
        $values = self::where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
        
        foreach ($values as $value) {
            $value->level = $level;
            $result->push($value);
            $result = $result->merge(self::getFlatTree($value->id, $level + 1));
        }
        
        return $result;
    }

    /**
     * دریافت خلاصه آماری مقدار
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $query = $this->journalEntryLines()
                      ->whereHas('journalEntry', function ($q) {
                          $q->posted();
                      });
        
        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->dateBetween($startDate, $endDate);
            });
        }
        
        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        $count = $query->count();
        
        return [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balance' => $totalDebit - $totalCredit,
            'entries_count' => $count,
            'formatted_debit' => number_format($totalDebit, 2),
            'formatted_credit' => number_format($totalCredit, 2),
            'formatted_balance' => number_format($totalDebit - $totalCredit, 2),
        ];
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست مقادیر یک بعد برای استفاده در dropdown
     * @param int $dimensionId
     * @param bool $flat
     * @return array
     */
    public static function getList($dimensionId, $flat = true)
    {
        if ($flat) {
            $values = self::getFlatTree(null, 0);
            $list = [];
            foreach ($values as $value) {
                if ($value->dimension_id == $dimensionId) {
                    $indent = str_repeat('— ', $value->level);
                    $list[$value->id] = $indent . $value->name;
                }
            }
            return $list;
        }
        
        return self::byDimension($dimensionId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * دریافت لیست مقادیر با فرمت کامل (نام به همراه کد)
     * @param int $dimensionId
     * @param bool $flat
     * @return array
     */
    public static function getListWithCode($dimensionId, $flat = true)
    {
        if ($flat) {
            $values = self::getFlatTree(null, 0);
            $list = [];
            foreach ($values as $value) {
                if ($value->dimension_id == $dimensionId) {
                    $indent = str_repeat('— ', $value->level);
                    $list[$value->id] = $indent . $value->full_name;
                }
            }
            return $list;
        }
        
        $values = self::byDimension($dimensionId)
            ->orderBy('name')
            ->get();
        
        $list = [];
        foreach ($values as $value) {
            $list[$value->id] = $value->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت مقادیر ریشه یک بعد
     * @param int $dimensionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRootValues($dimensionId)
    {
        return self::byDimension($dimensionId)
            ->root()
            ->orderBy('name')
            ->get();
    }

    /**
     * دریافت مقدار بر اساس کد و بعد
     * @param int $dimensionId
     * @param string $code
     * @return self|null
     */
    public static function getByCodeAndDimension($dimensionId, $code)
    {
        return self::byDimension($dimensionId)
            ->byCode($code)
            ->first();
    }

    /**
     * دریافت آمار مقادیر یک بعد
     * @param int $dimensionId
     * @return array
     */
    public static function getStatisticsByDimension($dimensionId)
    {
        $total = self::byDimension($dimensionId)->count();
        $root = self::byDimension($dimensionId)->root()->count();
        $leaf = self::byDimension($dimensionId)->leaf()->count();
        $hasChildren = self::byDimension($dimensionId)->hasChildren()->count();
        $hasTransactions = self::byDimension($dimensionId)->hasTransactions()->count();
        $withoutTransactions = $total - $hasTransactions;
        
        // محاسبه میانگین سطح
        $values = self::byDimension($dimensionId)->get();
        $totalLevel = 0;
        foreach ($values as $value) {
            $totalLevel += $value->getLevel();
        }
        $avgLevel = $total > 0 ? $totalLevel / $total : 0;
        
        return [
            'total' => $total,
            'root' => $root,
            'leaf' => $leaf,
            'has_children' => $hasChildren,
            'has_transactions' => $hasTransactions,
            'without_transactions' => $withoutTransactions,
            'avg_level' => round($avgLevel, 2),
        ];
    }

    /**
     * ایجاد مقدار جدید با اعتبارسنجی
     * @param int $dimensionId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($dimensionId, array $data)
    {
        // بررسی وجود کد تکراری در بعد
        if (isset($data['code'])) {
            $exists = self::byDimension($dimensionId)
                ->where('code', $data['code'])
                ->exists();
            
            if ($exists) {
                throw new \Exception('کد مقدار تکراری است.');
            }
        }

        // بررسی والد (اگر وجود دارد)
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = self::find($data['parent_id']);
            if (!$parent) {
                throw new \Exception('والد مشخص شده وجود ندارد.');
            }
            
            // بررسی اینکه والد در همان بعد باشد
            if ($parent->dimension_id != $dimensionId) {
                throw new \Exception('والد باید در همان بعد باشد.');
            }
        }

        return self::create(array_merge($data, ['dimension_id' => $dimensionId]));
    }

    /**
     * ایجاد خودکار کد مقدار بر اساس نام و بعد
     * @param string $name
     * @param int $dimensionId
     * @param int|null $parentId
     * @return string
     */
    public static function generateCode($name, $dimensionId, $parentId = null)
    {
        // اگر والد وجود دارد، کد والد را به عنوان پیشوند استفاده کن
        $prefix = '';
        if ($parentId) {
            $parent = self::find($parentId);
            if ($parent) {
                $prefix = $parent->code . '.';
            }
        }
        
        $code = $prefix . strtoupper(Str::slug($name, '_'));
        
        // اگر کد تکراری باشد، شماره اضافه می‌شود
        $counter = 1;
        $originalCode = $code;
        while (self::where('code', $code)->where('dimension_id', $dimensionId)->exists()) {
            $code = $originalCode . '_' . $counter;
            $counter++;
        }
        
        return $code;
    }

    /**
     * دریافت مقادیر با آمار تراکنش‌ها
     * @param int $dimensionId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWithTransactionStats($dimensionId, $startDate = null, $endDate = null)
    {
        $values = self::byDimension($dimensionId)
            ->with(['journalEntryLines' => function ($q) use ($startDate, $endDate) {
                if ($startDate && $endDate) {
                    $q->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                        $query->dateBetween($startDate, $endDate)
                              ->posted();
                    });
                } else {
                    $q->whereHas('journalEntry', function ($query) {
                        $query->posted();
                    });
                }
            }])
            ->get();
        
        // اضافه کردن آمار به هر مقدار
        foreach ($values as $value) {
            $totalDebit = $value->journalEntryLines->sum('debit');
            $totalCredit = $value->journalEntryLines->sum('credit');
            $value->total_debit = $totalDebit;
            $value->total_credit = $totalCredit;
            $value->balance = $totalDebit - $totalCredit;
            $value->entries_count = $value->journalEntryLines->count();
        }
        
        return $values;
    }

    /**
     * کپی کردن مقادیر از یک بعد به بعد دیگر
     * @param int $sourceDimensionId
     * @param int $targetDimensionId
     * @return int
     */
    public static function copyFromDimension($sourceDimensionId, $targetDimensionId)
    {
        $sourceValues = self::byDimension($sourceDimensionId)->get();
        $count = 0;
        $map = [];
        
        foreach ($sourceValues as $sourceValue) {
            // ایجاد مقدار جدید در بعد هدف
            $newValue = self::create([
                'dimension_id' => $targetDimensionId,
                'code' => $sourceValue->code,
                'name' => $sourceValue->name,
                'parent_id' => null, // ابتدا والد را null می‌گذاریم
            ]);
            
            // ذخیره نگاشت شناسه قدیمی به جدید
            $map[$sourceValue->id] = $newValue->id;
            $count++;
        }
        
        // به‌روزرسانی والدها
        foreach ($sourceValues as $sourceValue) {
            if ($sourceValue->parent_id && isset($map[$sourceValue->parent_id])) {
                $newValue = self::find($map[$sourceValue->id]);
                if ($newValue) {
                    $newValue->parent_id = $map[$sourceValue->parent_id];
                    $newValue->save();
                }
            }
        }
        
        return $count;
    }
}