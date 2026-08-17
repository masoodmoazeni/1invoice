<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class BankAccounts extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'bank_accounts';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'partner_id',
        'bank_name',
        'branch_name',
        'account_number',
        'iban',
        'swift',
        'currency_id',
        'is_default',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند
     * @var array
     */
    protected $hidden = [
        // می‌توانید فیلدهای حساس را مخفی کنید
        // 'iban', 'swift'
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\BankAccountsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\BankAccountsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر حساب بانکی متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsTo با مدل Partner (طرف حساب)
     * هر حساب بانکی می‌تواند به یک طرف حساب متصل باشد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partners::class, 'partner_id');
    }

    /**
     * رابطه belongsTo با مدل Currency
     * هر حساب بانکی دارای یک ارز است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currencies::class, 'currency_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌های بانکی
     * تمام تراکنش‌هایی که به این حساب بانکی مرتبط هستند
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bankTransactions()
    {
        return $this->hasMany(BankTransactions::class, 'bank_account_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌های حسابداری
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bank_account_id');
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر حساب‌های بانکی فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر حساب‌های بانکی غیرفعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * سکوپ برای فیلتر حساب بانکی پیش‌فرض
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
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
     * سکوپ برای فیلتر بر اساس طرف حساب
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $partnerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * سکوپ برای فیلتر بر اساس ارز
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $currencyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCurrency($query, $currencyId)
    {
        return $query->where('currency_id', $currencyId);
    }

    /**
     * سکوپ برای فیلتر بر اساس نام بانک
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $bankName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByBankName($query, $bankName)
    {
        return $query->where('bank_name', 'LIKE', "%{$bankName}%");
    }

    /**
     * سکوپ برای فیلتر بر اساس شماره حساب
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $accountNumber
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAccountNumber($query, $accountNumber)
    {
        return $query->where('account_number', 'LIKE', "%{$accountNumber}%");
    }

    /**
     * سکوپ برای فیلتر بر اساس IBAN
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $iban
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIban($query, $iban)
    {
        return $query->where('iban', $iban);
    }

    /**
     * سکوپ برای فیلتر بر اساس Swift Code
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $swift
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySwift($query, $swift)
    {
        return $query->where('swift', $swift);
    }

    /**
     * سکوپ برای جستجوی حساب‌های بانکی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('bank_name', 'LIKE', "%{$search}%")
                     ->orWhere('branch_name', 'LIKE', "%{$search}%")
                     ->orWhere('account_number', 'LIKE', "%{$search}%")
                     ->orWhere('iban', 'LIKE', "%{$search}%")
                     ->orWhere('swift', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای دریافت حساب‌های بانکی یک طرف حساب خاص
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $partnerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId)
                     ->orWhereNull('partner_id');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل حساب بانکی
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->bank_name . ' - ' . $this->account_number . ' (' . $this->currency->code . ')';
    }

    /**
     * دریافت نام مختصر حساب بانکی
     * @return string
     */
    public function getShortNameAttribute()
    {
        return $this->bank_name . ' - ' . Str::mask($this->account_number, '*', -4);
    }

    /**
     * دریافت IBAN به صورت فرمت شده (با فاصله)
     * @return string|null
     */
    public function getFormattedIbanAttribute()
    {
        if (!$this->iban) {
            return null;
        }
        
        // IBAN را به گروه‌های ۴ رقمی تقسیم می‌کند
        return implode(' ', str_split($this->iban, 4));
    }

    /**
     * دریافت Swift Code به صورت فرمت شده
     * @return string|null
     */
    public function getFormattedSwiftAttribute()
    {
        if (!$this->swift) {
            return null;
        }
        
        return strtoupper($this->swift);
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
     * دریافت اطلاعات کامل بانکی
     * @return string
     */
    public function getBankInfoAttribute()
    {
        $parts = [];
        $parts[] = "بانک: {$this->bank_name}";
        
        if ($this->branch_name) {
            $parts[] = "شعبه: {$this->branch_name}";
        }
        
        $parts[] = "شماره حساب: {$this->account_number}";
        
        if ($this->iban) {
            $parts[] = "شبا: {$this->formatted_iban}";
        }
        
        if ($this->swift) {
            $parts[] = "Swift: {$this->swift}";
        }
        
        $parts[] = "ارز: " . ($this->currency ? $this->currency->code : 'نامشخص');
        
        return implode(' | ', $parts);
    }

    /**
     * بررسی فعال بودن حساب بانکی
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * بررسی پیش‌فرض بودن حساب بانکی
     * @return bool
     */
    public function isDefault()
    {
        return (bool) $this->is_default;
    }

    /**
     * فعال کردن حساب بانکی
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن حساب بانکی
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * تنظیم به‌عنوان حساب بانکی پیش‌فرض شرکت
     * سایر حساب‌های بانکی پیش‌فرض شرکت غیرفعال می‌شوند
     * @return bool
     */
    public function setAsDefault()
    {
        // ابتدا تمام حساب‌های بانکی پیش‌فرض این شرکت را غیرپیش‌فرض می‌کنیم
        self::where('company_id', $this->company_id)
            ->where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // سپس این حساب بانکی را پیش‌فرض می‌کنیم
        $this->is_default = true;
        return $this->save();
    }

    /**
     * دریافت تعداد کل تراکنش‌های بانکی
     * @return int
     */
    public function getTransactionsCount()
    {
        return $this->bankTransactions()->count();
    }

    /**
     * دریافت مجموع مبلغ تراکنش‌های بانکی
     * @return float
     */
    public function getTotalTransactionsAmount()
    {
        return $this->bankTransactions()->sum('amount');
    }

    /**
     * دریافت مانده حساب بانکی
     * @param string|null $dateTo
     * @return float
     */
    public function getBalance($dateTo = null)
    {
        $query = $this->bankTransactions();
        
        if ($dateTo) {
            $query->where('transaction_date', '<=', $dateTo);
        }
        
        $totalDeposits = $query->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $query->where('type', 'withdrawal')->sum('amount');
        
        return $totalDeposits - $totalWithdrawals;
    }

    /**
     * دریافت آخرین تراکنش بانکی
     * @return BankTransactions|null
     */
    public function getLastTransaction()
    {
        return $this->bankTransactions()
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();
    }

    /**
     * اعتبارسنجی IBAN (بر اساس کشور)
     * @return bool
     */
    public function validateIban()
    {
        if (empty($this->iban)) {
            return false;
        }

        // حذف فاصله‌ها
        $iban = strtoupper(str_replace(' ', '', $this->iban));
        
        // بررسی طول IBAN (حداقل ۱۵ و حداکثر ۳۴ کاراکتر)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // بررسی حروف و اعداد
        if (!preg_match('/^[A-Z0-9]+$/', $iban)) {
            return false;
        }

        // بررسی کد کشور
        $countryCode = substr($iban, 0, 2);
        $countryCodes = ['IR', 'DE', 'FR', 'GB', 'US', 'AE', 'SA', 'TR', 'PK'];
        if (!in_array($countryCode, $countryCodes)) {
            return false;
        }

        // برای اعتبارسنجی کامل می‌توان از الگوریتم MOD 97 استفاده کرد
        return true;
    }

    /**
     * اعتبارسنجی Swift Code
     * @return bool
     */
    public function validateSwift()
    {
        if (empty($this->swift)) {
            return false;
        }

        // Swift Code باید 8 یا 11 کاراکتر باشد
        $swift = strtoupper($this->swift);
        
        if (strlen($swift) !== 8 && strlen($swift) !== 11) {
            return false;
        }

        // ۴ کاراکتر اول: حروف (کد بانک)
        if (!preg_match('/^[A-Z]{4}/', $swift)) {
            return false;
        }

        // ۲ کاراکتر بعدی: حروف (کد کشور)
        if (!preg_match('/^[A-Z]{2}/', substr($swift, 4, 2))) {
            return false;
        }

        return true;
    }

    /**
     * دریافت اطلاعات کامل برای چک
     * @return array
     */
    public function getCheckInfo()
    {
        return [
            'bank_name' => $this->bank_name,
            'branch_name' => $this->branch_name,
            'account_number' => $this->account_number,
            'iban' => $this->iban,
            'swift' => $this->swift,
            'currency' => $this->currency ? $this->currency->code : null,
        ];
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست حساب‌های بانکی یک شرکت برای استفاده در dropdown
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
        
        return $query->orderBy('bank_name')
                     ->orderBy('account_number')
                     ->pluck('account_number', 'id')
                     ->toArray();
    }

    /**
     * دریافت لیست حساب‌های بانکی با فرمت کامل
     * @param int $companyId
     * @param bool $onlyActive
     * @return array
     */
    public static function getListWithFullName($companyId, $onlyActive = true)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        $accounts = $query->orderBy('bank_name')
                          ->orderBy('account_number')
                          ->get();
        
        $list = [];
        foreach ($accounts as $account) {
            $list[$account->id] = $account->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت حساب بانکی پیش‌فرض یک شرکت
     * @param int $companyId
     * @return self|null
     */
    public static function getDefaultAccount($companyId)
    {
        return self::byCompany($companyId)
            ->active()
            ->default()
            ->first();
    }

    /**
     * دریافت حساب بانکی بر اساس شماره حساب
     * @param int $companyId
     * @param string $accountNumber
     * @return self|null
     */
    public static function getByAccountNumber($companyId, $accountNumber)
    {
        return self::byCompany($companyId)
            ->where('account_number', $accountNumber)
            ->first();
    }

    /**
     * دریافت حساب بانکی بر اساس IBAN
     * @param string $iban
     * @return self|null
     */
    public static function getByIban($iban)
    {
        return self::byIban($iban)->first();
    }

    /**
     * دریافت حساب‌های بانکی یک طرف حساب
     * @param int $partnerId
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPartnerAccounts($partnerId, $onlyActive = true)
    {
        $query = self::where('partner_id', $partnerId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        return $query->orderBy('bank_name')->get();
    }

    /**
     * دریافت آمار حساب‌های بانکی یک شرکت
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        $active = self::byCompany($companyId)->active()->count();
        $inactive = $total - $active;
        $default = self::byCompany($companyId)->default()->count();
        $withPartner = self::byCompany($companyId)->whereNotNull('partner_id')->count();
        $withoutPartner = $total - $withPartner;
        
        // آماده‌سازی آمار بر اساس ارز
        $currencyStats = self::byCompany($companyId)
            ->active()
            ->with('currency')
            ->get()
            ->groupBy('currency_id')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'currency_code' => $items->first()->currency ? $items->first()->currency->code : 'نامشخص',
                ];
            })
            ->toArray();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'default' => $default,
            'with_partner' => $withPartner,
            'without_partner' => $withoutPartner,
            'by_currency' => $currencyStats,
        ];
    }

    /**
     * ایجاد حساب بانکی با اعتبارسنجی
     * @param int $companyId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($companyId, array $data)
    {
        // بررسی شماره حساب تکراری
        $exists = self::byCompany($companyId)
            ->where('account_number', $data['account_number'])
            ->exists();
        
        if ($exists) {
            throw new \Exception('شماره حساب تکراری است.');
        }

        // بررسی IBAN تکراری
        if (!empty($data['iban'])) {
            $ibanExists = self::where('iban', $data['iban'])->exists();
            if ($ibanExists) {
                throw new \Exception('شماره شبا تکراری است.');
            }
        }

        // اگر پیش‌فرض است، سایر حساب‌ها را غیرپیش‌فرض کن
        if (isset($data['is_default']) && $data['is_default']) {
            self::byCompany($companyId)->update(['is_default' => false]);
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }

    /**
     * به‌روزرسانی حساب بانکی با اعتبارسنجی
     * @param int $id
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public static function updateWithValidation($id, array $data)
    {
        $account = self::findOrFail($id);
        
        // بررسی شماره حساب تکراری (به جز خودش)
        if (isset($data['account_number'])) {
            $exists = self::byCompany($account->company_id)
                ->where('account_number', $data['account_number'])
                ->where('id', '!=', $id)
                ->exists();
            
            if ($exists) {
                throw new \Exception('شماره حساب تکراری است.');
            }
        }

        // بررسی IBAN تکراری (به جز خودش)
        if (!empty($data['iban'])) {
            $ibanExists = self::where('iban', $data['iban'])
                ->where('id', '!=', $id)
                ->exists();
            
            if ($ibanExists) {
                throw new \Exception('شماره شبا تکراری است.');
            }
        }

        // اگر پیش‌فرض است، سایر حساب‌ها را غیرپیش‌فرض کن
        if (isset($data['is_default']) && $data['is_default']) {
            self::byCompany($account->company_id)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        return $account->update($data);
    }

    /**
     * دریافت حساب‌های بانکی با اطلاعات کامل برای گزارش
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForReport($companyId, array $filters = [])
    {
        $query = self::byCompany($companyId)->with(['currency', 'partner']);
        
        // فیلتر بر اساس وضعیت
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->inactive();
            }
        }
        
        // فیلتر بر اساس ارز
        if (isset($filters['currency_id']) && $filters['currency_id']) {
            $query->byCurrency($filters['currency_id']);
        }
        
        // فیلتر بر اساس طرف حساب
        if (isset($filters['partner_id']) && $filters['partner_id']) {
            $query->byPartner($filters['partner_id']);
        }
        
        // فیلتر بر اساس بانک
        if (isset($filters['bank_name']) && $filters['bank_name']) {
            $query->byBankName($filters['bank_name']);
        }
        
        return $query->orderBy('bank_name')
                     ->orderBy('account_number')
                     ->get();
    }
}