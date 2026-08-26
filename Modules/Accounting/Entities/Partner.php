<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'partners';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'partner_type',
        'name',
        'legal_name',
        'display_name',
        'tax_number',
        'registration_number',
        'country_id',
        'state',
        'city',
        'address',
        'postal_code',
        'phone',
        'mobile',
        'email',
        'website',
        'currency_id',
        'credit_limit',
        'payment_term_id',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'credit_limit' => 'decimal:2',
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

    // ========== ثابت‌های مربوط به نوع طرف حساب ==========

    /**
     * نوع: مشتری
     */
    const TYPE_CUSTOMER = 'customer';

    /**
     * نوع: تامین‌کننده
     */
    const TYPE_VENDOR = 'vendor';

    /**
     * نوع: کارمند
     */
    const TYPE_EMPLOYEE = 'employee';

    /**
     * نوع: بانک
     */
    const TYPE_BANK = 'bank';

    /**
     * نوع: سازمان دولتی
     */
    const TYPE_GOVERNMENT = 'government';

    /**
     * نوع: سایر
     */
    const TYPE_OTHER = 'other';

    /**
     * لیست انواع طرف حساب معتبر
     * @var array
     */
    public static $types = [
        self::TYPE_CUSTOMER,
        self::TYPE_VENDOR,
        self::TYPE_EMPLOYEE,
        self::TYPE_BANK,
        self::TYPE_GOVERNMENT,
        self::TYPE_OTHER,
    ];

    /**
     * لیست انواع طرف حساب با برچسب فارسی
     * @var array
     */
    public static $typeLabels = [
        self::TYPE_CUSTOMER => 'مشتری',
        self::TYPE_VENDOR => 'تامین‌کننده',
        self::TYPE_EMPLOYEE => 'کارمند',
        self::TYPE_BANK => 'بانک',
        self::TYPE_GOVERNMENT => 'سازمان دولتی',
        self::TYPE_OTHER => 'سایر',
    ];

    /**
     * لیست انواع طرف حساب با آیکون‌ها
     * @var array
     */
    public static $typeIcons = [
        self::TYPE_CUSTOMER => 'fa-user-tie',
        self::TYPE_VENDOR => 'fa-truck',
        self::TYPE_EMPLOYEE => 'fa-user',
        self::TYPE_BANK => 'fa-university',
        self::TYPE_GOVERNMENT => 'fa-landmark',
        self::TYPE_OTHER => 'fa-users',
    ];

    /**
     * لیست انواع طرف حساب با رنگ‌ها
     * @var array
     */
    public static $typeColors = [
        self::TYPE_CUSTOMER => 'primary',
        self::TYPE_VENDOR => 'success',
        self::TYPE_EMPLOYEE => 'info',
        self::TYPE_BANK => 'warning',
        self::TYPE_GOVERNMENT => 'danger',
        self::TYPE_OTHER => 'secondary',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\PartnersFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\PartnersFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر طرف حساب متعلق به یک شرکت است
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsTo با مدل Country
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    /**
     * رابطه belongsTo با مدل Currency
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currencies::class, 'currency_id');
    }

    /**
     * رابطه belongsTo با مدل PaymentTerm
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerms::class, 'payment_term_id');
    }

    /**
     * رابطه hasMany برای حساب‌های بانکی
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccounts::class, 'partner_id');
    }

    /**
     * رابطه hasMany برای فاکتورهای فروش
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesInvoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    /**
     * رابطه hasMany برای فاکتورهای خرید
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchaseInvoices()
    {
        return $this->hasMany(Invoice::class, 'vendor_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌ها
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'partner_id');
    }

    /**
     * دریافت آدرس کامل
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $parts = [];

        if ($this->address) {
            $parts[] = $this->address;
        }

        if ($this->city) {
            $parts[] = $this->city;
        }

        if ($this->state) {
            $parts[] = $this->state;
        }

        if ($this->country) {
            $parts[] = $this->country->name;
        }

        if ($this->postal_code) {
            $parts[] = 'کد پستی: ' . $this->postal_code;
        }

        return implode('، ', $parts);
    }

    /**
     * دریافت اطلاعات تماس کامل
     * @return string
     */
    public function getContactInfoAttribute()
    {
        $parts = [];

        if ($this->phone) {
            $parts[] = 'تلفن: ' . $this->phone;
        }

        if ($this->mobile) {
            $parts[] = 'موبایل: ' . $this->mobile;
        }

        if ($this->email) {
            $parts[] = 'ایمیل: ' . $this->email;
        }

        if ($this->website) {
            $parts[] = 'وب‌سایت: ' . $this->website;
        }

        return implode(' | ', $parts);
    }

    /**
     * دریافت نام نمایشی (اولویت با display_name، سپس name)
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->attributes['display_name'] ?? $this->name;
    }

    /**
     * دریافت نام کامل (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت نوع به صورت متنی
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::$typeLabels[$this->partner_type] ?? $this->partner_type;
    }

    /**
     * دریافت آیکون برای نوع
     * @return string
     */
    public function getTypeIconAttribute()
    {
        return self::$typeIcons[$this->partner_type] ?? 'fa-user';
    }

    /**
     * دریافت رنگ برای نوع
     * @return string
     */
    public function getTypeColorAttribute()
    {
        return self::$typeColors[$this->partner_type] ?? 'secondary';
    }

    /**
     * دریافت کلاس CSS برای نوع
     * @return string
     */
    public function getTypeBadgeClassAttribute()
    {
        $classes = [
            self::TYPE_CUSTOMER => 'bg-primary',
            self::TYPE_VENDOR => 'bg-success',
            self::TYPE_EMPLOYEE => 'bg-info',
            self::TYPE_BANK => 'bg-warning',
            self::TYPE_GOVERNMENT => 'bg-danger',
            self::TYPE_OTHER => 'bg-secondary',
        ];

        return $classes[$this->partner_type] ?? 'bg-secondary';
    }

    /**
     * دریافت وضعیت به صورت متنی
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'فعال' : 'غیرفعال';
    }

    /**
     * دریافت کلاس CSS برای وضعیت
     * @return string
     */
    public function getStatusClassAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    /**
     * دریافت سقف اعتباری به صورت فرمت شده
     * @return string
     */
    public function getFormattedCreditLimitAttribute()
    {
        return number_format($this->credit_limit, 2);
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر طرف‌های حساب فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر طرف‌های حساب غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
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
     * سکوپ برای فیلتر بر اساس نوع
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('partner_type', $type);
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
     * سکوپ برای فیلتر بر اساس ایمیل
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * سکوپ برای فیلتر بر اساس شهر
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $city
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', 'LIKE', "%{$city}%");
    }

    /**
     * سکوپ برای فیلتر مشتریان
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCustomers($query)
    {
        return $query->where('partner_type', self::TYPE_CUSTOMER);
    }

    /**
     * سکوپ برای فیلتر تامین‌کنندگان
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVendors($query)
    {
        return $query->where('partner_type', self::TYPE_VENDOR);
    }

    /**
     * سکوپ برای فیلتر کارمندان
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmployees($query)
    {
        return $query->where('partner_type', self::TYPE_EMPLOYEE);
    }

    /**
     * سکوپ برای فیلتر طرف‌های حساب با سقف اعتباری
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasCreditLimit($query)
    {
        return $query->where('credit_limit', '>', 0);
    }

    /**
     * سکوپ برای فیلتر طرف‌های حساب بدون سقف اعتباری
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoCreditLimit($query)
    {
        return $query->where('credit_limit', '<=', 0);
    }

    /**
     * سکوپ برای جستجوی طرف‌های حساب
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%")
                     ->orWhere('legal_name', 'LIKE', "%{$search}%")
                     ->orWhere('display_name', 'LIKE', "%{$search}%")
                     ->orWhere('email', 'LIKE', "%{$search}%")
                     ->orWhere('phone', 'LIKE', "%{$search}%")
                     ->orWhere('mobile', 'LIKE', "%{$search}%")
                     ->orWhere('tax_number', 'LIKE', "%{$search}%")
                     ->orWhere('city', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای دریافت طرف‌های حساب دارای تراکنش
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasTransactions($query)
    {
        return $query->has('transactions');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * بررسی فعال بودن طرف حساب
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * فعال کردن طرف حساب
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن طرف حساب
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * بررسی اینکه آیا طرف حساب مشتری است
     * @return bool
     */
    public function isCustomer()
    {
        return $this->partner_type === self::TYPE_CUSTOMER;
    }

    /**
     * بررسی اینکه آیا طرف حساب تامین‌کننده است
     * @return bool
     */
    public function isVendor()
    {
        return $this->partner_type === self::TYPE_VENDOR;
    }

    /**
     * بررسی اینکه آیا طرف حساب کارمند است
     * @return bool
     */
    public function isEmployee()
    {
        return $this->partner_type === self::TYPE_EMPLOYEE;
    }

    /**
     * دریافت تعداد کل تراکنش‌ها
     * @return int
     */
    public function getTransactionsCount()
    {
        return $this->transactions()->count();
    }

    /**
     * دریافت مجموع تراکنش‌ها
     * @return float
     */
    public function getTotalTransactionsAmount()
    {
        return $this->transactions()->sum('amount');
    }

    /**
     * دریافت مانده حساب طرف حساب
     * @return float
     */
    public function getBalance()
    {
        $totalDebit = $this->transactions()->where('type', 'debit')->sum('amount');
        $totalCredit = $this->transactions()->where('type', 'credit')->sum('amount');

        return $totalDebit - $totalCredit;
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
            'display_name' => $this->display_name,
            'type' => $this->partner_type,
            'type_label' => $this->type_label,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'city' => $this->city,
            'country' => $this->country ? $this->country->name : null,
            'credit_limit' => $this->credit_limit,
            'formatted_credit_limit' => $this->formatted_credit_limit,
            'balance' => $this->getBalance(),
            'is_active' => $this->is_active,
            'status_text' => $this->status_text,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * دریافت طرف‌های حساب برای گزارش
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForReport($companyId, array $filters = [])
    {
        $query = self::byCompany($companyId)->with(['country', 'currency', 'paymentTerm']);

        // فیلتر بر اساس نوع
        if (isset($filters['type']) && $filters['type']) {
            $query->byType($filters['type']);
        }

        // فیلتر بر اساس وضعیت
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }

        // فیلتر بر اساس جستجو
        if (isset($filters['search']) && $filters['search']) {
            $query->search($filters['search']);
        }

        // فیلتر بر اساس شهر
        if (isset($filters['city']) && $filters['city']) {
            $query->byCity($filters['city']);
        }

        return $query->orderBy('name')->get();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست طرف‌های حساب برای استفاده در dropdown
     * @param int $companyId
     * @param string|null $type
     * @param bool $onlyActive
     * @return array
     */
    public static function getList($companyId, $type = null, $onlyActive = true)
    {
        $query = self::byCompany($companyId);

        if ($type) {
            $query->byType($type);
        }

        if ($onlyActive) {
            $query->active();
        }

        return $query->orderBy('name')
                     ->pluck('name', 'id')
                     ->toArray();
    }

    /**
     * دریافت لیست طرف‌های حساب با فرمت کامل (نام به همراه کد)
     * @param int $companyId
     * @param string|null $type
     * @param bool $onlyActive
     * @return array
     */
    public static function getListWithCode($companyId, $type = null, $onlyActive = true)
    {
        $query = self::byCompany($companyId);

        if ($type) {
            $query->byType($type);
        }

        if ($onlyActive) {
            $query->active();
        }

        $partners = $query->orderBy('name')->get();
        $list = [];

        foreach ($partners as $partner) {
            $list[$partner->id] = $partner->full_name;
        }

        return $list;
    }

    /**
     * دریافت طرف حساب بر اساس کد و شرکت
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
     * دریافت طرف حساب بر اساس ایمیل و شرکت
     * @param int $companyId
     * @param string $email
     * @return self|null
     */
    public static function getByEmailAndCompany($companyId, $email)
    {
        return self::byCompany($companyId)
            ->byEmail($email)
            ->first();
    }

    /**
     * دریافت آمار طرف‌های حساب
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        $active = self::byCompany($companyId)->active()->count();
        $inactive = $total - $active;

        $byType = [];
        foreach (self::$types as $type) {
            $byType[$type] = [
                'count' => self::byCompany($companyId)->byType($type)->count(),
                'active' => self::byCompany($companyId)->byType($type)->active()->count(),
                'label' => self::$typeLabels[$type],
                'icon' => self::$typeIcons[$type],
            ];
        }

        $hasCreditLimit = self::byCompany($companyId)->hasCreditLimit()->count();
        $hasTransactions = self::byCompany($companyId)->hasTransactions()->count();
        $withoutTransactions = $total - $hasTransactions;

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'has_credit_limit' => $hasCreditLimit,
            'has_transactions' => $hasTransactions,
            'without_transactions' => $withoutTransactions,
            'by_type' => $byType,
        ];
    }

    /**
     * ایجاد طرف حساب جدید با اعتبارسنجی
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
                throw new \Exception('کد طرف حساب تکراری است.');
            }
        }

        // بررسی وجود ایمیل تکراری
        if (isset($data['email']) && $data['email']) {
            $exists = self::byCompany($companyId)
                ->where('email', $data['email'])
                ->exists();

            if ($exists) {
                throw new \Exception('ایمیل تکراری است.');
            }
        }

        // اگر display_name خالی بود، از name استفاده کن
        if (empty($data['display_name'])) {
            $data['display_name'] = $data['name'];
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * ایجاد خودکار کد طرف حساب
     * @param string $type
     * @param int $companyId
     * @return string
     */
    public static function generateCode($type, $companyId)
    {
        $prefix = [
            self::TYPE_CUSTOMER => 'CUS',
            self::TYPE_VENDOR => 'VEN',
            self::TYPE_EMPLOYEE => 'EMP',
            self::TYPE_BANK => 'BNK',
            self::TYPE_GOVERNMENT => 'GOV',
            self::TYPE_OTHER => 'OTH',
        ];

        $prefix = $prefix[$type] ?? 'PRT';

        $lastPartner = self::byCompany($companyId)
            ->where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastPartner) {
            $lastNumber = (int) substr($lastPartner->code, strlen($prefix));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            return $prefix . $newNumber;
        }

        return $prefix . '00001';
    }

    /**
     * دریافت مشتریان برتر بر اساس میزان خرید
     * @param int $companyId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopCustomers($companyId, $limit = 10)
    {
        return self::byCompany($companyId)
            ->customers()
            ->withSum('transactions', 'amount')
            ->orderBy('transactions_sum_amount', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * دریافت تامین‌کنندگان برتر بر اساس میزان خرید
     * @param int $companyId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopVendors($companyId, $limit = 10)
    {
        return self::byCompany($companyId)
            ->vendors()
            ->withSum('transactions', 'amount')
            ->orderBy('transactions_sum_amount', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * ایمپورت طرف‌های حساب از فایل CSV
     * @param int $companyId
     * @param string $filePath
     * @param array $mapping
     * @return array
     */
    public static function importFromCsv($companyId, $filePath, array $mapping)
    {
        $created = 0;
        $errors = [];
        $handle = fopen($filePath, 'r');

        if (!$handle) {
            throw new \Exception('Unable to open file.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            throw new \Exception('Invalid CSV file.');
        }

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = [];
                foreach ($mapping as $dbField => $csvIndex) {
                    $data[$dbField] = $row[$csvIndex] ?? null;
                }

                // تنظیم مقادیر پیش‌فرض
                $data['company_id'] = $companyId;
                $data['code'] = $data['code'] ?? self::generateCode($data['partner_type'] ?? 'other', $companyId);
                $data['is_active'] = $data['is_active'] ?? true;

                self::createWithValidation($companyId, $data);
                $created++;
            } catch (\Exception $e) {
                $errors[] = 'Row ' . ($created + count($errors) + 1) . ': ' . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'created' => $created,
            'errors' => $errors,
        ];
    }

    /**
     * اکسل خروجی گرفتن از طرف‌های حساب
     * @param int $companyId
     * @param array $filters
     * @return string
     */
    public static function exportToCsv($companyId, array $filters = [])
    {
        $partners = self::getForReport($companyId, $filters);

        $filename = 'partners_' . date('Y-m-d_His') . '.csv';
        $handle = fopen($filename, 'w');

        // هدر
        fputcsv($handle, [
            'کد', 'نام', 'نام حقوقی', 'نوع', 'ایمیل', 'تلفن', 'موبایل',
            'شهر', 'کشور', 'سقف اعتباری', 'وضعیت'
        ]);

        // داده‌ها
        foreach ($partners as $partner) {
            fputcsv($handle, [
                $partner->code,
                $partner->name,
                $partner->legal_name,
                $partner->type_label,
                $partner->email,
                $partner->phone,
                $partner->mobile,
                $partner->city,
                $partner->country ? $partner->country->name : '',
                $partner->credit_limit,
                $partner->status_text,
            ]);
        }

        fclose($handle);

        return $filename;
    }
}
