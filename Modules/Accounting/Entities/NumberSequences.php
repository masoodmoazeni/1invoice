<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NumberSequences extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'number_sequences';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'module',
        'prefix',
        'suffix',
        'current_number',
        'padding',
        'reset_type',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'current_number' => 'integer',
        'padding' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند
     * @var array
     */
    protected $hidden = [];

    // ========== ثابت‌های مربوط به ماژول‌ها ==========

    /**
     * ماژول‌های مختلف سیستم
     */
    const MODULE_SALES = 'sales';
    const MODULE_PURCHASE = 'purchase';
    const MODULE_ACCOUNTING = 'accounting';
    const MODULE_INVENTORY = 'inventory';
    const MODULE_HR = 'hr';
    const MODULE_BANK = 'bank';
    const MODULE_CUSTOM = 'custom';

    /**
     * لیست ماژول‌های معتبر
     * @var array
     */
    public static $modules = [
        self::MODULE_SALES,
        self::MODULE_PURCHASE,
        self::MODULE_ACCOUNTING,
        self::MODULE_INVENTORY,
        self::MODULE_HR,
        self::MODULE_BANK,
        self::MODULE_CUSTOM,
    ];

    /**
     * لیست ماژول‌ها با برچسب فارسی
     * @var array
     */
    public static $moduleLabels = [
        self::MODULE_SALES => 'فروش',
        self::MODULE_PURCHASE => 'خرید',
        self::MODULE_ACCOUNTING => 'حسابداری',
        self::MODULE_INVENTORY => 'انبار',
        self::MODULE_HR => 'منابع انسانی',
        self::MODULE_BANK => 'بانک',
        self::MODULE_CUSTOM => 'سفارشی',
    ];

    // ========== ثابت‌های مربوط به نوع ریست ==========

    /**
     * نوع ریست: روزانه
     */
    const RESET_DAILY = 'daily';
    
    /**
     * نوع ریست: ماهانه
     */
    const RESET_MONTHLY = 'monthly';
    
    /**
     * نوع ریست: سالانه
     */
    const RESET_YEARLY = 'yearly';
    
    /**
     * نوع ریست: هرگز (همیشگی)
     */
    const RESET_NEVER = 'never';

    /**
     * لیست انواع ریست معتبر
     * @var array
     */
    public static $resetTypes = [
        self::RESET_DAILY,
        self::RESET_MONTHLY,
        self::RESET_YEARLY,
        self::RESET_NEVER,
    ];

    /**
     * لیست انواع ریست با برچسب فارسی
     * @var array
     */
    public static $resetLabels = [
        self::RESET_DAILY => 'روزانه',
        self::RESET_MONTHLY => 'ماهانه',
        self::RESET_YEARLY => 'سالانه',
        self::RESET_NEVER => 'هرگز (همیشگی)',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\NumberSequencesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\NumberSequencesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر شماره‌گذاری متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه hasMany برای انواع اسناد
     * هر شماره‌گذاری می‌تواند برای چندین نوع سند استفاده شود
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentTypes()
    {
        return $this->hasMany(DocumentTypes::class, 'number_sequence_id');
    }

    /**
     * دریافت تعداد انواع اسناد استفاده‌کننده از این شماره‌گذاری
     * @return int
     */
    public function getDocumentTypesCount()
    {
        return $this->documentTypes()->count();
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

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
     * سکوپ برای فیلتر بر اساس ماژول
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $module
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * سکوپ برای فیلتر بر اساس پیشوند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $prefix
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPrefix($query, $prefix)
    {
        return $query->where('prefix', $prefix);
    }

    /**
     * سکوپ برای فیلتر بر اساس نوع ریست
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $resetType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByResetType($query, $resetType)
    {
        return $query->where('reset_type', $resetType);
    }

    /**
     * سکوپ برای جستجوی شماره‌گذاری‌ها
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('module', 'LIKE', "%{$search}%")
                     ->orWhere('prefix', 'LIKE', "%{$search}%")
                     ->orWhere('suffix', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای دریافت شماره‌گذاری‌های دارای اسناد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasDocuments($query)
    {
        return $query->has('documentTypes');
    }

    /**
     * سکوپ برای دریافت شماره‌گذاری‌های بدون اسناد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutDocuments($query)
    {
        return $query->doesntHave('documentTypes');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل شماره‌گذاری
     * @return string
     */
    public function getFullNameAttribute()
    {
        $moduleName = self::$moduleLabels[$this->module] ?? $this->module;
        $pattern = $this->getPattern();
        return $moduleName . ' - ' . $pattern;
    }

    /**
     * دریافت الگوی شماره‌گذاری
     * @return string
     */
    public function getPatternAttribute()
    {
        return $this->getPattern();
    }

    /**
     * دریافت نوع ریست به صورت متنی
     * @return string
     */
    public function getResetLabelAttribute()
    {
        return self::$resetLabels[$this->reset_type] ?? $this->reset_type;
    }

    /**
     * دریافت ماژول به صورت متنی
     * @return string
     */
    public function getModuleLabelAttribute()
    {
        return self::$moduleLabels[$this->module] ?? $this->module;
    }

    /**
     * دریافت کلاس CSS برای نوع ریست
     * @return string
     */
    public function getResetClassAttribute()
    {
        $classes = [
            self::RESET_DAILY => 'primary',
            self::RESET_MONTHLY => 'info',
            self::RESET_YEARLY => 'warning',
            self::RESET_NEVER => 'success',
        ];
        
        return $classes[$this->reset_type] ?? 'secondary';
    }

    /**
     * دریافت کلید ریست بر اساس نوع
     * @return string
     */
    public function getResetKey()
    {
        $now = Carbon::now();
        
        switch ($this->reset_type) {
            case self::RESET_DAILY:
                return $now->format('Y-m-d');
            case self::RESET_MONTHLY:
                return $now->format('Y-m');
            case self::RESET_YEARLY:
                return $now->format('Y');
            case self::RESET_NEVER:
            default:
                return 'never';
        }
    }

    /**
     * دریافت الگوی شماره‌گذاری
     * @return string
     */
    public function getPattern()
    {
        $number = str_pad('{number}', $this->padding, '0', STR_PAD_LEFT);
        $prefix = $this->prefix ?? '';
        $suffix = $this->suffix ?? '';
        
        return $prefix . $number . $suffix;
    }

    /**
     * تولید شماره بعدی
     * @return string
     */
    public function generateNumber()
    {
        // بررسی نیاز به ریست شماره
        $this->checkAndReset();

        // افزایش شماره جاری
        $this->current_number++;
        $this->save();

        // تولید شماره با فرمت مشخص
        $number = str_pad($this->current_number, $this->padding, '0', STR_PAD_LEFT);
        $prefix = $this->prefix ?? '';
        $suffix = $this->suffix ?? '';

        return $prefix . $number . $suffix;
    }

    /**
     * بررسی و ریست شماره در صورت نیاز
     * @return bool
     */
    protected function checkAndReset()
    {
        $resetKey = $this->getResetKey();
        $lastResetKey = $this->getLastResetKey();

        if ($resetKey !== $lastResetKey && $this->reset_type !== self::RESET_NEVER) {
            $this->current_number = 0;
            $this->save();
            $this->setLastResetKey($resetKey);
            return true;
        }

        return false;
    }

    /**
     * دریافت کلید آخرین ریست
     * @return string|null
     */
    protected function getLastResetKey()
    {
        // می‌توانید این مقدار را در یک فیلد جداگانه در جدول ذخیره کنید
        // یا از کش استفاده کنید
        return cache()->get($this->getResetKeyCacheKey());
    }

    /**
     * ذخیره کلید آخرین ریست
     * @param string $resetKey
     * @return void
     */
    protected function setLastResetKey($resetKey)
    {
        cache()->put($this->getResetKeyCacheKey(), $resetKey, 86400 * 365); // 1 سال
    }

    /**
     * دریافت کلید کش برای ذخیره کلید ریست
     * @return string
     */
    protected function getResetKeyCacheKey()
    {
        return 'number_sequence_reset_' . $this->id;
    }

    /**
     * دریافت شماره جاری به صورت فرمت شده
     * @return string
     */
    public function getCurrentNumberFormatted()
    {
        return str_pad($this->current_number, $this->padding, '0', STR_PAD_LEFT);
    }

    /**
     * دریافت شماره بعدی (بدون افزایش)
     * @return string
     */
    public function getNextNumber()
    {
        $nextNumber = $this->current_number + 1;
        return str_pad($nextNumber, $this->padding, '0', STR_PAD_LEFT);
    }

    /**
     * ریست کردن شماره به مقدار مشخص
     * @param int $number
     * @return bool
     */
    public function resetTo($number = 0)
    {
        $this->current_number = $number;
        return $this->save();
    }

    /**
     * تنظیم تعداد ارقام
     * @param int $padding
     * @return bool
     */
    public function setPadding($padding)
    {
        $this->padding = $padding;
        return $this->save();
    }

    /**
     * دریافت اطلاعات کامل برای گزارش
     * @return array
     */
    public function getReportInfo()
    {
        return [
            'id' => $this->id,
            'module' => $this->module,
            'module_label' => $this->module_label,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'pattern' => $this->pattern,
            'current_number' => $this->current_number,
            'current_number_formatted' => $this->getCurrentNumberFormatted(),
            'padding' => $this->padding,
            'reset_type' => $this->reset_type,
            'reset_label' => $this->reset_label,
            'document_types_count' => $this->getDocumentTypesCount(),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * بررسی وجود شماره‌گذاری تکراری
     * @param int $companyId
     * @param string $module
     * @param string|null $prefix
     * @param string|null $suffix
     * @param int|null $exceptId
     * @return bool
     */
    public static function existsDuplicate($companyId, $module, $prefix = null, $suffix = null, $exceptId = null)
    {
        $query = self::byCompany($companyId)
            ->where('module', $module)
            ->where('prefix', $prefix)
            ->where('suffix', $suffix);
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        return $query->exists();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست شماره‌گذاری‌ها برای استفاده در dropdown
     * @param int $companyId
     * @param string|null $module
     * @return array
     */
    public static function getList($companyId, $module = null)
    {
        $query = self::byCompany($companyId);
        
        if ($module) {
            $query->byModule($module);
        }
        
        return $query->orderBy('module')
                     ->orderBy('prefix')
                     ->get()
                     ->mapWithKeys(function ($item) {
                         return [$item->id => $item->full_name];
                     })
                     ->toArray();
    }

    /**
     * دریافت شماره‌گذاری بر اساس ماژول و پیشوند
     * @param int $companyId
     * @param string $module
     * @param string|null $prefix
     * @param string|null $suffix
     * @return self|null
     */
    public static function getByModuleAndPrefix($companyId, $module, $prefix = null, $suffix = null)
    {
        $query = self::byCompany($companyId)
            ->where('module', $module);
        
        if ($prefix !== null) {
            $query->where('prefix', $prefix);
        }
        
        if ($suffix !== null) {
            $query->where('suffix', $suffix);
        }
        
        return $query->first();
    }

    /**
     * دریافت یا ایجاد شماره‌گذاری
     * @param int $companyId
     * @param string $module
     * @param array $data
     * @return self
     */
    public static function getOrCreate($companyId, $module, array $data)
    {
        $sequence = self::getByModuleAndPrefix(
            $companyId,
            $module,
            $data['prefix'] ?? null,
            $data['suffix'] ?? null
        );

        if ($sequence) {
            return $sequence;
        }

        return self::create(array_merge([
            'company_id' => $companyId,
            'module' => $module,
            'prefix' => null,
            'suffix' => null,
            'current_number' => 0,
            'padding' => 5,
            'reset_type' => self::RESET_YEARLY,
        ], $data));
    }

    /**
     * دریافت آمار شماره‌گذاری‌ها
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        
        $byModule = self::byCompany($companyId)
            ->select('module')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('module')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = self::$moduleLabels[$item->module] ?? $item->module;
                return [$item->module => [
                    'count' => $item->count,
                    'label' => $label,
                ]];
            })
            ->toArray();
        
        $byResetType = self::byCompany($companyId)
            ->select('reset_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('reset_type')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = self::$resetLabels[$item->reset_type] ?? $item->reset_type;
                return [$item->reset_type => [
                    'count' => $item->count,
                    'label' => $label,
                ]];
            })
            ->toArray();
        
        $hasDocuments = self::byCompany($companyId)->hasDocuments()->count();
        $withoutDocuments = $total - $hasDocuments;
        
        return [
            'total' => $total,
            'has_documents' => $hasDocuments,
            'without_documents' => $withoutDocuments,
            'by_module' => $byModule,
            'by_reset_type' => $byResetType,
        ];
    }

    /**
     * ایجاد شماره‌گذاری با اعتبارسنجی
     * @param int $companyId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($companyId, array $data)
    {
        // بررسی وجود شماره‌گذاری تکراری
        $exists = self::existsDuplicate(
            $companyId,
            $data['module'],
            $data['prefix'] ?? null,
            $data['suffix'] ?? null
        );
        
        if ($exists) {
            throw new \Exception('شماره‌گذاری با این مشخصات قبلاً ثبت شده است.');
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * ایجاد شماره‌گذاری‌های پیش‌فرض برای یک شرکت
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createDefaultSequences($companyId)
    {
        $defaults = [
            [
                'module' => self::MODULE_SALES,
                'prefix' => 'SINV-',
                'suffix' => null,
                'padding' => 5,
                'reset_type' => self::RESET_YEARLY,
            ],
            [
                'module' => self::MODULE_PURCHASE,
                'prefix' => 'PINV-',
                'suffix' => null,
                'padding' => 5,
                'reset_type' => self::RESET_YEARLY,
            ],
            [
                'module' => self::MODULE_ACCOUNTING,
                'prefix' => 'DOC-',
                'suffix' => null,
                'padding' => 6,
                'reset_type' => self::RESET_YEARLY,
            ],
            [
                'module' => self::MODULE_INVENTORY,
                'prefix' => 'INV-',
                'suffix' => null,
                'padding' => 5,
                'reset_type' => self::RESET_YEARLY,
            ],
            [
                'module' => self::MODULE_HR,
                'prefix' => 'EMP-',
                'suffix' => null,
                'padding' => 4,
                'reset_type' => self::RESET_NEVER,
            ],
            [
                'module' => self::MODULE_BANK,
                'prefix' => 'TRX-',
                'suffix' => null,
                'padding' => 6,
                'reset_type' => self::RESET_DAILY,
            ],
        ];

        $created = collect();
        
        foreach ($defaults as $data) {
            try {
                $sequence = self::createWithValidation($companyId, $data);
                $created->push($sequence);
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return $created;
    }

    /**
     * دریافت شماره‌گذاری‌های یک ماژول
     * @param int $companyId
     * @param string $module
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByModule($companyId, $module)
    {
        return self::byCompany($companyId)
            ->byModule($module)
            ->orderBy('prefix')
            ->get();
    }

    /**
     * به‌روزرسانی شماره جاری برای همه شماره‌گذاری‌های یک ماژول
     * @param int $companyId
     * @param string $module
     * @param int $newNumber
     * @return int
     */
    public static function updateCurrentNumberForModule($companyId, $module, $newNumber)
    {
        return self::byCompany($companyId)
            ->byModule($module)
            ->update(['current_number' => $newNumber]);
    }

    /**
     * دریافت شماره‌گذاری‌ها برای گزارش
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForReport($companyId, array $filters = [])
    {
        $query = self::byCompany($companyId);
        
        // فیلتر بر اساس ماژول
        if (isset($filters['module']) && $filters['module']) {
            $query->byModule($filters['module']);
        }
        
        // فیلتر بر اساس نوع ریست
        if (isset($filters['reset_type']) && $filters['reset_type']) {
            $query->byResetType($filters['reset_type']);
        }
        
        // فیلتر بر اساس جستجو
        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }
        
        return $query->orderBy('module')
                     ->orderBy('prefix')
                     ->get();
    }
}