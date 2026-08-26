<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaymentTerm extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'payment_terms';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'due_days',
        'is_immediate',
        'is_default',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'due_days' => 'integer',
        'is_immediate' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // ========== ثابت‌های مربوط به شرایط پرداخت پیش‌فرض ==========

    /**
     * نوع: نقدی (پرداخت فوری)
     */
    const IMMEDIATE = 'immediate';

    /**
     * نوع: مدت‌دار (با سررسید مشخص)
     */
    const CREDIT = 'credit';

    /**
     * نوع: چند قسطی
     */
    const INSTALLMENT = 'installment';

    /**
     * لیست انواع با برچسب فارسی
     * @var array
     */
    public static $typeLabels = [
        self::IMMEDIATE => 'نقدی (فوری)',
        self::CREDIT => 'مدت‌دار (اعتباری)',
        self::INSTALLMENT => 'قسطی',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\PaymentTermsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\PaymentTermsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر شرط پرداخت متعلق به یک شرکت است
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه hasMany برای طرف‌های حساب
     * تمام طرف‌های حسابی که از این شرط پرداخت استفاده می‌کنند
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function partners()
    {
        return $this->hasMany(Partners::class, 'payment_term_id');
    }

    /**
     * رابطه hasMany برای فاکتورها
     * تمام فاکتورهایی که از این شرط پرداخت استفاده می‌کنند
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'payment_term_id');
    }

    /**
     * دریافت تعداد طرف‌های حساب استفاده‌کننده از این شرط
     * @return int
     */
    public function getPartnersCount()
    {
        return $this->partners()->count();
    }

    /**
     * دریافت تعداد فاکتورهای استفاده‌کننده از این شرط
     * @return int
     */
    public function getInvoicesCount()
    {
        return $this->invoices()->count();
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر شرایط پرداخت فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر شرایط پرداخت غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای فیلتر شرایط پرداخت پیش‌فرض
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * سکوپ برای فیلتر شرایط پرداخت فوری (نقدی)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeImmediate($query)
    {
        return $query->where('is_immediate', true);
    }

    /**
     * سکوپ برای فیلتر شرایط پرداخت غیرفوری (مدت‌دار)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotImmediate($query)
    {
        return $query->where('is_immediate', false);
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
     * سکوپ برای فیلتر بر اساس تعداد روزهای سررسید
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDueDays($query, $days)
    {
        return $query->where('due_days', $days);
    }

    /**
     * سکوپ برای فیلتر شرایط پرداخت با سررسید کمتر از مقدار مشخص
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueDaysLessThan($query, $days)
    {
        return $query->where('due_days', '<', $days);
    }

    /**
     * سکوپ برای فیلتر شرایط پرداخت با سررسید بیشتر از مقدار مشخص
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueDaysGreaterThan($query, $days)
    {
        return $query->where('due_days', '>', $days);
    }

    /**
     * سکوپ برای جستجوی شرایط پرداخت
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
     * سکوپ برای دریافت شرایط پرداخت پرکاربرد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query)
    {
        return $query->withCount('partners')
                     ->orderBy('partners_count', 'desc');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت نوع شرط پرداخت
     * @return string
     */
    public function getTypeAttribute()
    {
        if ($this->is_immediate) {
            return self::IMMEDIATE;
        } elseif ($this->due_days > 0) {
            return self::CREDIT;
        }
        return self::CREDIT;
    }

    /**
     * دریافت نوع به صورت متنی
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    /**
     * دریافت توضیحات کامل شرط پرداخت
     * @return string
     */
    public function getFullDescriptionAttribute()
    {
        if ($this->is_immediate) {
            return 'پرداخت نقدی و فوری';
        } elseif ($this->due_days > 0) {
            return 'پرداخت تا ' . $this->due_days . ' روز پس از تاریخ فاکتور';
        }
        return $this->description ?? 'پرداخت مدت‌دار';
    }

    /**
     * دریافت وضعیت به صورت متنی
     * @return string
     */
    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'غیرفعال';
        }

        if ($this->is_default) {
            return 'فعال - پیش‌فرض';
        }

        return 'فعال';
    }

    /**
     * دریافت کلاس CSS برای وضعیت
     * @return string
     */
    public function getStatusClassAttribute()
    {
        if (!$this->is_active) {
            return 'danger';
        }

        if ($this->is_default) {
            return 'success';
        }

        return 'info';
    }

    /**
     * دریافت کلاس CSS برای نوع
     * @return string
     */
    public function getTypeClassAttribute()
    {
        $classes = [
            self::IMMEDIATE => 'success',
            self::CREDIT => 'warning',
            self::INSTALLMENT => 'info',
        ];

        return $classes[$this->type] ?? 'secondary';
    }

    /**
     * دریافت تاریخ سررسید بر اساس تاریخ فاکتور
     * @param string $invoiceDate
     * @return \Carbon\Carbon
     */
    public function getDueDate($invoiceDate)
    {
        $date = \Carbon\Carbon::parse($invoiceDate);

        if ($this->is_immediate) {
            return $date;
        }

        return $date->addDays($this->due_days);
    }

    /**
     * بررسی فعال بودن شرط پرداخت
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * بررسی پیش‌فرض بودن شرط پرداخت
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }

    /**
     * بررسی فوری بودن شرط پرداخت
     * @return bool
     */
    public function isImmediate()
    {
        return (bool) $this->is_immediate;
    }

    /**
     * فعال کردن شرط پرداخت
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن شرط پرداخت
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * تنظیم به‌عنوان شرط پرداخت پیش‌فرض شرکت
     * سایر شرایط پرداخت پیش‌فرض شرکت غیرفعال می‌شوند
     * @return bool
     */
    public function setAsDefault()
    {
        // ابتدا تمام شرایط پرداخت پیش‌فرض این شرکت را غیرپیش‌فرض می‌کنیم
        self::where('company_id', $this->company_id)
            ->where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // سپس این شرط پرداخت را پیش‌فرض می‌کنیم
        $this->is_default = true;
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
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'due_days' => $this->due_days,
            'is_immediate' => $this->is_immediate,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'full_description' => $this->full_description,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
            'status_text' => $this->status_text,
            'partners_count' => $this->getPartnersCount(),
            'invoices_count' => $this->getInvoicesCount(),
            'created_at' => $this->created_at,
        ];
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست شرایط پرداخت برای استفاده در dropdown
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getList($companyId, $onlyActive = true)
    {
        $query = self::byCompany($companyId);

        if ($onlyActive) {
            $query->active();
        }

        return $query->orderBy('is_immediate', 'desc')
                     ->orderBy('due_days')
                     ->pluck('name', 'id')
                     ->toArray();
    }

    /**
     * دریافت لیست شرایط پرداخت با فرمت کامل (نام به همراه کد)
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getListWithCode($companyId, $onlyActive = true)
    {
        $query = self::byCompany($companyId);

        if ($onlyActive) {
            $query->active();
        }

        $terms = $query->orderBy('is_immediate', 'desc')
                       ->orderBy('due_days')
                       ->get();

        $list = [];
        foreach ($terms as $term) {
            $list[$term->id] = $term->full_name;
        }

        return $list;
    }

    /**
     * دریافت لیست شرایط پرداخت به صورت گروه‌بندی شده
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getGroupedList($companyId, $onlyActive = true)
    {
        $query = self::byCompany($companyId);

        if ($onlyActive) {
            $query->active();
        }

        $terms = $query->orderBy('is_immediate', 'desc')
                       ->orderBy('due_days')
                       ->get();

        $grouped = [
            'immediate' => [],
            'credit' => [],
        ];

        foreach ($terms as $term) {
            $key = $term->is_immediate ? 'immediate' : 'credit';
            $grouped[$key][$term->id] = $term->full_name;
        }

        return $grouped;
    }

    /**
     * دریافت شرط پرداخت بر اساس کد و شرکت
     * @param int $companyId
     * @param string $code
     * @return self|null
     */
    public static function getByCodeAndCompany($companyId, $code)
    {
        return self::byCompany($companyId)
            ->byCode($code)
            ->first();
    }

    /**
     * دریافت شرط پرداخت پیش‌فرض شرکت
     * @param int $companyId
     * @return self|null
     */
    public static function getDefaultTerm($companyId)
    {
        return self::byCompany($companyId)
            ->active()
            ->default()
            ->first();
    }

    /**
     * دریافت شرط پرداخت فوری (نقدی)
     * @param int $companyId
     * @return self|null
     */
    public static function getImmediateTerm($companyId)
    {
        return self::byCompany($companyId)
            ->active()
            ->immediate()
            ->first();
    }

    /**
     * دریافت آمار شرایط پرداخت
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        $active = self::byCompany($companyId)->active()->count();
        $inactive = $total - $active;
        $default = self::byCompany($companyId)->default()->count();
        $immediate = self::byCompany($companyId)->immediate()->count();
        $notImmediate = $total - $immediate;

        // دریافت شرایط پرداخت با تعداد طرف‌های حساب
        $popular = self::byCompany($companyId)
            ->active()
            ->withCount('partners')
            ->orderBy('partners_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($term) {
                return [
                    'id' => $term->id,
                    'name' => $term->name,
                    'partners_count' => $term->partners_count,
                ];
            })
            ->toArray();

        // دریافت میانگین روزهای سررسید
        $avgDueDays = self::byCompany($companyId)
            ->where('is_immediate', false)
            ->avg('due_days');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'default' => $default,
            'immediate' => $immediate,
            'credit' => $notImmediate,
            'avg_due_days' => round($avgDueDays ?? 0, 0),
            'popular_terms' => $popular,
        ];
    }

    /**
     * ایجاد شرط پرداخت جدید با اعتبارسنجی
     * @param int $companyId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($companyId, array $data)
    {
        // بررسی وجود کد تکراری
        if (isset($data['code'])) {
            $exists = self::byCompany($companyId)
                ->where('code', $data['code'])
                ->exists();

            if ($exists) {
                throw new \Exception('کد شرط پرداخت تکراری است.');
            }
        }

        // اگر شرط فوری است، due_days باید 0 باشد
        if (isset($data['is_immediate']) && $data['is_immediate']) {
            $data['due_days'] = 0;
        }

        // اگر پیش‌فرض است، سایر شرایط پرداخت را غیرپیش‌فرض کن
        if (isset($data['is_default']) && $data['is_default']) {
            self::byCompany($companyId)->update(['is_default' => false]);
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * ایجاد شرایط پرداخت پیش‌فرض برای یک شرکت
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createDefaultTerms($companyId)
    {
        $defaultTerms = [
            [
                'code' => 'CASH',
                'name' => 'نقدی',
                'description' => 'پرداخت نقدی و فوری',
                'due_days' => 0,
                'is_immediate' => true,
                'is_default' => true,
            ],
            [
                'code' => 'NET15',
                'name' => '۱۵ روزه',
                'description' => 'پرداخت تا ۱۵ روز پس از تاریخ فاکتور',
                'due_days' => 15,
                'is_immediate' => false,
                'is_default' => false,
            ],
            [
                'code' => 'NET30',
                'name' => '۳۰ روزه',
                'description' => 'پرداخت تا ۳۰ روز پس از تاریخ فاکتور',
                'due_days' => 30,
                'is_immediate' => false,
                'is_default' => false,
            ],
            [
                'code' => 'NET45',
                'name' => '۴۵ روزه',
                'description' => 'پرداخت تا ۴۵ روز پس از تاریخ فاکتور',
                'due_days' => 45,
                'is_immediate' => false,
                'is_default' => false,
            ],
            [
                'code' => 'NET60',
                'name' => '۶۰ روزه',
                'description' => 'پرداخت تا ۶۰ روز پس از تاریخ فاکتور',
                'due_days' => 60,
                'is_immediate' => false,
                'is_default' => false,
            ],
            [
                'code' => 'EOM',
                'name' => 'پایان ماه',
                'description' => 'پرداخت در پایان ماه جاری',
                'due_days' => 0,
                'is_immediate' => false,
                'is_default' => false,
            ],
        ];

        $created = collect();

        foreach ($defaultTerms as $termData) {
            try {
                $term = self::createWithValidation($companyId, $termData);
                $created->push($term);
            } catch (\Exception $e) {
                continue;
            }
        }

        return $created;
    }

    /**
     * دریافت شرایط پرداخت برای یک طرف حساب خاص
     * @param int $companyId
     * @param int $partnerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForPartner($companyId, $partnerId)
    {
        $partner = Partners::find($partnerId);

        if ($partner && $partner->payment_term_id) {
            return self::byCompany($companyId)
                ->where('id', $partner->payment_term_id)
                ->get();
        }

        return self::byCompany($companyId)
            ->active()
            ->orderBy('is_default', 'desc')
            ->orderBy('is_immediate', 'desc')
            ->get();
    }

    /**
     * محاسبه تاریخ سررسید برای یک شرط پرداخت
     * @param int $companyId
     * @param string $termCode
     * @param string $invoiceDate
     * @return string
     */
    public static function calculateDueDate($companyId, $termCode, $invoiceDate)
    {
        $term = self::getByCodeAndCompany($companyId, $termCode);

        if (!$term) {
            return $invoiceDate;
        }

        return $term->getDueDate($invoiceDate)->format('Y-m-d');
    }

    /**
     * دریافت شرایط پرداخت برای استفاده در گزارش
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForReport($companyId, array $filters = [])
    {
        $query = self::byCompany($companyId)->with(['company']);

        // فیلتر بر اساس وضعیت
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }

        // فیلتر بر اساس نوع
        if (isset($filters['type'])) {
            if ($filters['type'] === 'immediate') {
                $query->immediate();
            } elseif ($filters['type'] === 'credit') {
                $query->notImmediate();
            }
        }

        // فیلتر بر اساس جستجو
        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        // فیلتر بر اساس روزهای سررسید
        if (isset($filters['due_days_min']) && isset($filters['due_days_max'])) {
            $query->whereBetween('due_days', [$filters['due_days_min'], $filters['due_days_max']]);
        } elseif (isset($filters['due_days_min'])) {
            $query->where('due_days', '>=', $filters['due_days_min']);
        } elseif (isset($filters['due_days_max'])) {
            $query->where('due_days', '<=', $filters['due_days_max']);
        }

        return $query->orderBy('is_immediate', 'desc')
                     ->orderBy('due_days')
                     ->get();
    }
}
