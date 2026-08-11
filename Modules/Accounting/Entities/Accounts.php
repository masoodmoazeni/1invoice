<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Accounts extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'accounts';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'account_code',
        'account_name',
        'parent_id',
        'account_category',
        'account_type',
        'normal_balance',
        'currency_id',
        'allow_posting',
        'is_system',
        'is_active',
        'level',
        'sort_order',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'allow_posting' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer',
        'sort_order' => 'integer',
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

    // ========== ثابت‌های مربوط به دسته‌بندی حساب‌ها ==========

    /**
     * دسته‌بندی: دارایی
     */
    const CATEGORY_ASSET = 'asset';
    
    /**
     * دسته‌بندی: بدهی
     */
    const CATEGORY_LIABILITY = 'liability';
    
    /**
     * دسته‌بندی: سرمایه
     */
    const CATEGORY_EQUITY = 'equity';
    
    /**
     * دسته‌بندی: درآمد
     */
    const CATEGORY_REVENUE = 'revenue';
    
    /**
     * دسته‌بندی: هزینه
     */
    const CATEGORY_EXPENSE = 'expense';

    /**
     * لیست دسته‌بندی‌های معتبر
     * @var array
     */
    public static $categories = [
        self::CATEGORY_ASSET,
        self::CATEGORY_LIABILITY,
        self::CATEGORY_EQUITY,
        self::CATEGORY_REVENUE,
        self::CATEGORY_EXPENSE,
    ];

    /**
     * لیست دسته‌بندی‌ها با برچسب فارسی
     * @var array
     */
    public static $categoryLabels = [
        self::CATEGORY_ASSET => 'دارایی',
        self::CATEGORY_LIABILITY => 'بدهی',
        self::CATEGORY_EQUITY => 'سرمایه',
        self::CATEGORY_REVENUE => 'درآمد',
        self::CATEGORY_EXPENSE => 'هزینه',
    ];

    // ========== ثابت‌های مربوط به نوع حساب ==========

    /**
     * نوع: کل (گروهی)
     */
    const TYPE_HEADER = 'header';
    
    /**
     * نوع: جزئی (قابل ثبت)
     */
    const TYPE_DETAIL = 'detail';

    /**
     * لیست انواع حساب معتبر
     * @var array
     */
    public static $types = [
        self::TYPE_HEADER,
        self::TYPE_DETAIL,
    ];

    /**
     * لیست انواع حساب با برچسب فارسی
     * @var array
     */
    public static $typeLabels = [
        self::TYPE_HEADER => 'کل (گروهی)',
        self::TYPE_DETAIL => 'جزئی (قابل ثبت)',
    ];

    // ========== ثابت‌های مربوط به مانده عادی ==========

    /**
     * مانده عادی: بدهکار
     */
    const BALANCE_DEBIT = 'debit';
    
    /**
     * مانده عادی: بستانکار
     */
    const BALANCE_CREDIT = 'credit';

    /**
     * لیست مانده‌های عادی معتبر
     * @var array
     */
    public static $balances = [
        self::BALANCE_DEBIT,
        self::BALANCE_CREDIT,
    ];

    /**
     * لیست مانده‌های عادی با برچسب فارسی
     * @var array
     */
    public static $balanceLabels = [
        self::BALANCE_DEBIT => 'بدهکار',
        self::BALANCE_CREDIT => 'بستانکار',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\AccountsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\AccountsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر حساب متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsTo برای حساب والد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * رابطه hasMany برای حساب‌های فرزند
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * رابطه hasMany برای حساب‌های فرزند فعال
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeChildren()
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->where('is_active', true);
    }

    /**
     * رابطه belongsTo با مدل Currency
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currencies::class, 'currency_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌های بدهکار
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function debitTransactions()
    {
        return $this->hasMany(Transaction::class, 'debit_account_id');
    }

    /**
     * رابطه hasMany برای تراکنش‌های بستانکار
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function creditTransactions()
    {
        return $this->hasMany(Transaction::class, 'credit_account_id');
    }

    /**
     * دریافت تمام تراکنش‌های حساب (بدهکار و بستانکار)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTransactions()
    {
        return $this->debitTransactions->merge($this->creditTransactions);
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر حساب‌های فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر حساب‌های غیرفعال
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
     * سکوپ برای فیلتر حساب‌های کل (گروهی)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHeaders($query)
    {
        return $query->where('account_type', self::TYPE_HEADER);
    }

    /**
     * سکوپ برای فیلتر حساب‌های جزئی (قابل ثبت)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDetails($query)
    {
        return $query->where('account_type', self::TYPE_DETAIL);
    }

    /**
     * سکوپ برای فیلتر حساب‌های سیستمی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * سکوپ برای فیلتر حساب‌های غیرسیستمی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonSystem($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * سکوپ برای فیلتر حساب‌های دارای مانده بدهکار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDebitBalance($query)
    {
        return $query->where('normal_balance', self::BALANCE_DEBIT);
    }

    /**
     * سکوپ برای فیلتر حساب‌های دارای مانده بستانکار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreditBalance($query)
    {
        return $query->where('normal_balance', self::BALANCE_CREDIT);
    }

    /**
     * سکوپ برای فیلتر حساب‌های ریشه (بدون والد)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * سکوپ برای فیلتر بر اساس دسته‌بندی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('account_category', $category);
    }

    /**
     * سکوپ برای فیلتر بر اساس نوع حساب
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * سکوپ برای فیلتر حساب‌های قابل ثبت
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllowPosting($query)
    {
        return $query->where('allow_posting', true);
    }

    /**
     * سکوپ برای جستجوی حساب‌ها بر اساس نام یا کد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('account_code', 'LIKE', "%{$search}%")
                     ->orWhere('account_name', 'LIKE', "%{$search}%");
    }

    /**
     * سکوپ برای دریافت حساب‌هایی که دارای حساب فرزند هستند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasChildren($query)
    {
        return $query->has('children');
    }

    /**
     * سکوپ برای دریافت حساب‌هایی که حساب فرزند ندارند (برگ)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeaf($query)
    {
        return $query->doesntHave('children');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل حساب (کد - نام)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->account_code . ' - ' . $this->account_name;
    }

    /**
     * دریافت نام حساب با تورفتگی برای نمایش درختواره
     * @return string
     */
    public function getIndentedNameAttribute()
    {
        $indent = str_repeat('— ', $this->level);
        return $indent . $this->account_name;
    }

    /**
     * دریافت نام دسته‌بندی به فارسی
     * @return string
     */
    public function getCategoryLabelAttribute()
    {
        return self::$categoryLabels[$this->account_category] ?? $this->account_category;
    }

    /**
     * دریافت نام نوع حساب به فارسی
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::$typeLabels[$this->account_type] ?? $this->account_type;
    }

    /**
     * دریافت نام مانده عادی به فارسی
     * @return string
     */
    public function getBalanceLabelAttribute()
    {
        return self::$balanceLabels[$this->normal_balance] ?? $this->normal_balance;
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
     * دریافت وضعیت به صورت متنی
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'فعال' : 'غیرفعال';
    }

    /**
     * دریافت کلاس CSS برای مانده عادی
     * @return string
     */
    public function getBalanceClassAttribute()
    {
        return $this->normal_balance === self::BALANCE_DEBIT ? 'text-primary' : 'text-success';
    }

    /**
     * دریافت مسیر کامل حساب (از ریشه تا خودش)
     * @return string
     */
    public function getFullPathAttribute()
    {
        $path = collect([$this->account_name]);
        $parent = $this->parent;
        
        while ($parent) {
            $path->prepend($parent->account_name);
            $parent = $parent->parent;
        }
        
        return $path->implode(' / ');
    }

    /**
     * دریافت تمام حساب‌های فرزند به صورت بازگشتی
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
     * دریافت تمام حساب‌های والد به صورت بازگشتی
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
     * بررسی اینکه آیا حساب ریشه است؟
     * @return bool
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * بررسی اینکه آیا حساب برگ (بدون فرزند) است؟
     * @return bool
     */
    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    /**
     * بررسی اینکه آیا حساب قابل ثبت است؟
     * @return bool
     */
    public function isPostable()
    {
        return $this->allow_posting && $this->is_active && $this->account_type === self::TYPE_DETAIL;
    }

    /**
     * بررسی اینکه آیا حساب سیستمی است؟
     * @return bool
     */
    public function isSystem()
    {
        return (bool) $this->is_system;
    }

    /**
     * فعال کردن حساب
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن حساب
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * غیرفعال کردن حساب به همراه تمام زیرمجموعه‌ها
     * @return bool
     */
    public function deactivateWithChildren()
    {
        $this->is_active = false;
        $this->save();
        
        foreach ($this->children as $child) {
            $child->deactivateWithChildren();
        }
        
        return true;
    }

    /**
     * دریافت مانده حساب
     * @param string|null $dateTo
     * @return float
     */
    public function getBalance($dateTo = null)
    {
        $query = Transaction::where(function ($q) {
            $q->where('debit_account_id', $this->id)
              ->orWhere('credit_account_id', $this->id);
        });

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        $transactions = $query->get();
        
        $totalDebit = $transactions->sum('debit_amount');
        $totalCredit = $transactions->sum('credit_amount');

        if ($this->normal_balance === self::BALANCE_DEBIT) {
            return $totalDebit - $totalCredit;
        } else {
            return $totalCredit - $totalDebit;
        }
    }

    /**
     * دریافت مانده حساب به همراه فرزندان
     * @param string|null $dateTo
     * @return float
     */
    public function getBalanceWithChildren($dateTo = null)
    {
        $balance = $this->getBalance($dateTo);
        
        foreach ($this->children as $child) {
            $balance += $child->getBalanceWithChildren($dateTo);
        }
        
        return $balance;
    }

    /**
     * دریافت مجموع بدهکاران حساب
     * @param string|null $dateTo
     * @return float
     */
    public function getTotalDebit($dateTo = null)
    {
        $query = $this->debitTransactions();
        
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        
        return $query->sum('amount');
    }

    /**
     * دریافت مجموع بستانکاران حساب
     * @param string|null $dateTo
     * @return float
     */
    public function getTotalCredit($dateTo = null)
    {
        $query = $this->creditTransactions();
        
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }
        
        return $query->sum('amount');
    }

    /**
     * دریافت مسیر کامل کد حساب (برای ساخت کد فرزند)
     * @return string
     */
    public function getFullCodePath()
    {
        $codes = collect([$this->account_code]);
        $parent = $this->parent;
        
        while ($parent) {
            $codes->prepend($parent->account_code);
            $parent = $parent->parent;
        }
        
        return $codes->implode('.');
    }

    /**
     * ایجاد کد حساب برای فرزند جدید
     * @return string
     */
    public function generateChildCode()
    {
        $maxCode = self::where('parent_id', $this->id)
            ->orderBy('account_code', 'desc')
            ->first();
        
        if ($maxCode) {
            $lastPart = explode('.', $maxCode->account_code);
            $newNumber = (int) end($lastPart) + 1;
            return $this->account_code . '.' . $newNumber;
        }
        
        return $this->account_code . '.1';
    }

    /**
     * به‌روزرسانی سطح حساب‌ها به صورت بازگشتی
     * @return bool
     */
    public function updateLevel()
    {
        $this->level = $this->getLevel();
        $this->save();
        
        foreach ($this->children as $child) {
            $child->updateLevel();
        }
        
        return true;
    }

    /**
     * دریافت سطح حساب
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
     * دریافت حساب‌های قابل استفاده برای ثبت سند
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPostableAccounts($companyId)
    {
        return self::byCompany($companyId)
            ->active()
            ->details()
            ->allowPosting()
            ->orderBy('account_code')
            ->get();
    }

    /**
     * دریافت درختواره حساب‌ها
     * @param int $companyId
     * @param bool $onlyActive
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTree($companyId, $onlyActive = true)
    {
        $query = self::byCompany($companyId);
        
        if ($onlyActive) {
            $query->active();
        }
        
        $accounts = $query->orderBy('sort_order')
                         ->orderBy('account_code')
                         ->get();
        
        return self::buildTree($accounts);
    }

    /**
     * ساخت درختواره از مجموعه حساب‌ها
     * @param \Illuminate\Database\Eloquent\Collection $accounts
     * @param int|null $parentId
     * @return array
     */
    public static function buildTree($accounts, $parentId = null)
    {
        $result = [];
        
        foreach ($accounts as $account) {
            if ($account->parent_id === $parentId) {
                $children = self::buildTree($accounts, $account->id);
                if ($children) {
                    $account->children = $children;
                }
                $result[] = $account;
            }
        }
        
        return $result;
    }

    /**
     * دریافت لیست حساب‌ها برای استفاده در dropdown
     * @param int $companyId
     * @param bool $onlyPostable
     * @return array
     */
    public static function getList($companyId, $onlyPostable = false)
    {
        $query = self::byCompany($companyId)->active();
        
        if ($onlyPostable) {
            $query->details()->allowPosting();
        }
        
        $accounts = $query->orderBy('account_code')->get();
        $list = [];
        
        foreach ($accounts as $account) {
            $list[$account->id] = $account->account_code . ' - ' . $account->account_name;
        }
        
        return $list;
    }

    /**
     * دریافت لیست حساب‌ها با تورفتگی برای نمایش درختواره
     * @param int $companyId
     * @param bool $onlyPostable
     * @return array
     */
    public static function getIndentedList($companyId, $onlyPostable = false)
    {
        $query = self::byCompany($companyId)->active();
        
        if ($onlyPostable) {
            $query->details()->allowPosting();
        }
        
        $accounts = $query->orderBy('sort_order')
                         ->orderBy('account_code')
                         ->get();
        
        $tree = self::buildTree($accounts);
        $list = [];
        
        self::flattenTree($tree, $list);
        
        return $list;
    }

    /**
     * تخت کردن درختواره برای استفاده در dropdown
     * @param array $tree
     * @param array &$list
     * @param int $level
     * @param int $indent
     */
    public static function flattenTree($tree, &$list, $level = 0, $indent = 0)
    {
        foreach ($tree as $account) {
            $prefix = str_repeat('— ', $level);
            $list[$account->id] = $prefix . $account->account_code . ' - ' . $account->account_name;
            
            if (isset($account->children) && count($account->children) > 0) {
                self::flattenTree($account->children, $list, $level + 1);
            }
        }
    }

    /**
     * دریافت آمار حساب‌های یک شرکت
     * @param int $companyId
     * @return array
     */
    public static function getStatistics($companyId)
    {
        $total = self::byCompany($companyId)->count();
        $active = self::byCompany($companyId)->active()->count();
        $inactive = $total - $active;
        $headers = self::byCompany($companyId)->headers()->count();
        $details = self::byCompany($companyId)->details()->count();
        $system = self::byCompany($companyId)->system()->count();
        $postable = self::byCompany($companyId)->details()->allowPosting()->count();
        
        $assets = self::byCompany($companyId)->byCategory(self::CATEGORY_ASSET)->count();
        $liabilities = self::byCompany($companyId)->byCategory(self::CATEGORY_LIABILITY)->count();
        $equity = self::byCompany($companyId)->byCategory(self::CATEGORY_EQUITY)->count();
        $revenue = self::byCompany($companyId)->byCategory(self::CATEGORY_REVENUE)->count();
        $expense = self::byCompany($companyId)->byCategory(self::CATEGORY_EXPENSE)->count();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'headers' => $headers,
            'details' => $details,
            'system' => $system,
            'postable' => $postable,
            'by_category' => [
                'asset' => $assets,
                'liability' => $liabilities,
                'equity' => $equity,
                'revenue' => $revenue,
                'expense' => $expense,
            ],
        ];
    }

    /**
     * دریافت حساب‌های یک دسته‌بندی خاص
     * @param int $companyId
     * @param string $category
     * @param bool $onlyPostable
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCategory($companyId, $category, $onlyPostable = false)
    {
        $query = self::byCompany($companyId)
            ->byCategory($category)
            ->active();
        
        if ($onlyPostable) {
            $query->details()->allowPosting();
        }
        
        return $query->orderBy('account_code')->get();
    }

    /**
     * اعتبارسنجی و ایجاد حساب جدید
     * @param int $companyId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($companyId, array $data)
    {
        // بررسی وجود کد حساب تکراری
        if (self::byCompany($companyId)->where('account_code', $data['account_code'])->exists()) {
            throw new \Exception('کد حساب تکراری است.');
        }

        // اگر والد وجود دارد، بررسی کنید که والد از نوع کل باشد
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = self::find($data['parent_id']);
            if (!$parent || $parent->account_type !== self::TYPE_HEADER) {
                throw new \Exception('حساب والد باید از نوع کل (گروهی) باشد.');
            }
        }

        // اگر حساب از نوع جزئی است، باید قابل ثبت باشد
        if (isset($data['account_type']) && $data['account_type'] === self::TYPE_DETAIL) {
            $data['allow_posting'] = $data['allow_posting'] ?? true;
        }

        // محاسبه سطح
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = self::find($data['parent_id']);
            $data['level'] = $parent ? $parent->level + 1 : 0;
        } else {
            $data['level'] = 0;
        }

        return self::create(array_merge($data, ['company_id' => $companyId]));
    }
}