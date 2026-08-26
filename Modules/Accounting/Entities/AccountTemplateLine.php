<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class AccountTemplateLine extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'account_template_lines';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'template_id',
        'account_code',
        'account_name',
        'parent_code',
        'category',
        'type',
        'normal_balance',
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

    // ========== ثابت‌های مربوط به دسته‌بندی حساب‌ها ==========

    /**
     * دسته‌بندی: دارایی
     */
    const CATEGORY_ASSET = 'asset';
    const CATEGORY_LIABILITY = 'liability';
    const CATEGORY_EQUITY = 'equity';
    const CATEGORY_REVENUE = 'revenue';
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
     * @return \Modules\Accounting\Database\factories\AccountTemplateLinesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\AccountTemplateLinesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل AccountTemplate
     * هر ردیف متعلق به یک قالب است
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template()
    {
        return $this->belongsTo(AccountTemplates::class, 'template_id');
    }

    /**
     * رابطه belongsTo برای والد (از طریق کد)
     * دریافت حساب بالادستی (سطح بالاتر)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_code', 'account_code')
                    ->where('template_id', $this->template_id);
    }

    /**
     * رابطه hasMany برای فرزندان
     * دریافت تمام حساب‌های زیرمجموعه
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_code', 'account_code')
                    ->where('template_id', $this->template_id);
    }

    /**
     * دریافت تمام فرزندان به صورت بازگشتی (تا هر سطح)
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
     * دریافت تمام والدین به صورت بازگشتی (تا ریشه)
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
     * دریافت مسیر کامل حساب (از ریشه تا خودش)
     *
     * @return string
     */
    public function getFullPath()
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
     * دریافت سطح حساب در ساختار سلسله‌مراتبی
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

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر بر اساس قالب
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $templateId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    /**
     * سکوپ برای دریافت ردیف‌های ریشه (بدون والد)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_code');
    }

    /**
     * سکوپ برای دریافت ردیف‌های غیرریشه (دارای والد)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotRoot($query)
    {
        return $query->whereNotNull('parent_code');
    }

    /**
     * سکوپ برای فیلتر بر اساس دسته‌بندی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * سکوپ برای فیلتر بر اساس نوع
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * سکوپ برای فیلتر بر اساس مانده عادی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $balance
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByNormalBalance($query, $balance)
    {
        return $query->where('normal_balance', $balance);
    }

    /**
     * سکوپ برای دریافت حساب‌های کل (گروهی)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHeaders($query)
    {
        return $query->where('type', self::TYPE_HEADER);
    }

    /**
     * سکوپ برای دریافت حساب‌های جزئی (قابل ثبت)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDetails($query)
    {
        return $query->where('type', self::TYPE_DETAIL);
    }

    /**
     * سکوپ برای دریافت حساب‌های بدهکار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDebitBalance($query)
    {
        return $query->where('normal_balance', self::BALANCE_DEBIT);
    }

    /**
     * سکوپ برای دریافت حساب‌های بستانکار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreditBalance($query)
    {
        return $query->where('normal_balance', self::BALANCE_CREDIT);
    }

    /**
     * سکوپ برای جستجوی ردیف‌ها بر اساس نام یا کد
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
     * سکوپ برای دریافت ردیف‌هایی که فرزند دارند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasChildren($query)
    {
        return $query->has('children');
    }

    /**
     * سکوپ برای دریافت ردیف‌هایی که فرزند ندارند (برگ)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeaf($query)
    {
        return $query->doesntHave('children');
    }

    /**
     * سکوپ برای مرتب‌سازی بر اساس کد حساب
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByCode($query)
    {
        return $query->orderBy('account_code');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل (کد - نام)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->account_code . ' - ' . $this->account_name;
    }

    /**
     * دریافت نام با تورفتگی برای نمایش درختواره
     * @return string
     */
    public function getIndentedNameAttribute()
    {
        $indent = str_repeat('— ', $this->getLevel());
        return $indent . $this->account_name;
    }

    /**
     * دریافت نام دسته‌بندی به فارسی
     * @return string
     */
    public function getCategoryLabelAttribute()
    {
        return self::$categoryLabels[$this->category] ?? $this->category;
    }

    /**
     * دریافت نام نوع به فارسی
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return self::$typeLabels[$this->type] ?? $this->type;
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
     * دریافت کلاس CSS برای دسته‌بندی
     * @return string
     */
    public function getCategoryClassAttribute()
    {
        $classes = [
            self::CATEGORY_ASSET => 'primary',
            self::CATEGORY_LIABILITY => 'warning',
            self::CATEGORY_EQUITY => 'success',
            self::CATEGORY_REVENUE => 'info',
            self::CATEGORY_EXPENSE => 'danger',
        ];

        return $classes[$this->category] ?? 'secondary';
    }

    /**
     * دریافت کلاس CSS برای نوع
     * @return string
     */
    public function getTypeClassAttribute()
    {
        return $this->type === self::TYPE_HEADER ? 'info' : 'success';
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
     * بررسی اینکه آیا ردیف ریشه است
     * @return bool
     */
    public function isRoot()
    {
        return is_null($this->parent_code);
    }

    /**
     * بررسی اینکه آیا ردیف برگ (بدون فرزند) است
     * @return bool
     */
    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    /**
     * بررسی اینکه آیا ردیف از نوع کل است
     * @return bool
     */
    public function isHeader()
    {
        return $this->type === self::TYPE_HEADER;
    }

    /**
     * بررسی اینکه آیا ردیف از نوع جزئی است
     * @return bool
     */
    public function isDetail()
    {
        return $this->type === self::TYPE_DETAIL;
    }

    /**
     * بررسی اینکه آیا ردیف بدهکار است
     * @return bool
     */
    public function isDebitBalance()
    {
        return $this->normal_balance === self::BALANCE_DEBIT;
    }

    /**
     * بررسی اینکه آیا ردیف بستانکار است
     * @return bool
     */
    public function isCreditBalance()
    {
        return $this->normal_balance === self::BALANCE_CREDIT;
    }

    /**
     * دریافت مسیر کامل کد (برای ساخت کد فرزند)
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
     * ایجاد کد برای فرزند جدید
     * @return string
     */
    public function generateChildCode()
    {
        $maxCode = self::where('template_id', $this->template_id)
            ->where('parent_code', $this->account_code)
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
     * تبدیل ردیف به آرایه برای وارد کردن به حساب‌های شرکت
     * @return array
     */
    public function toAccountArray()
    {
        return [
            'account_code' => $this->account_code,
            'account_name' => $this->account_name,
            'account_category' => $this->category,
            'account_type' => $this->type,
            'normal_balance' => $this->normal_balance,
            'allow_posting' => $this->type === self::TYPE_DETAIL,
            'is_system' => false,
            'is_active' => true,
        ];
    }

    /**
     * دریافت اطلاعات کامل برای گزارش
     * @return array
     */
    public function getReportInfo()
    {
        return [
            'id' => $this->id,
            'account_code' => $this->account_code,
            'account_name' => $this->account_name,
            'parent_code' => $this->parent_code,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'normal_balance' => $this->normal_balance,
            'balance_label' => $this->balance_label,
            'level' => $this->getLevel(),
            'full_path' => $this->full_path,
            'is_root' => $this->isRoot(),
            'is_leaf' => $this->isLeaf(),
            'has_children' => $this->children()->count() > 0,
            'children_count' => $this->children()->count(),
            'created_at' => $this->created_at,
        ];
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست ردیف‌های یک قالب برای استفاده در dropdown
     * @param int $templateId
     * @param bool $flat
     * @return array
     */
    public static function getList($templateId, $flat = true)
    {
        if ($flat) {
            $tree = self::getTree($templateId);
            $list = [];
            self::flattenTree($tree, $list);
            return $list;
        }

        return self::byTemplate($templateId)
            ->orderBy('account_code')
            ->pluck('account_name', 'id')
            ->toArray();
    }

    /**
     * دریافت لیست ردیف‌ها با فرمت کامل (کد - نام)
     * @param int $templateId
     * @param bool $flat
     * @return array
     */
    public static function getListWithCode($templateId, $flat = true)
    {
        if ($flat) {
            $tree = self::getTree($templateId);
            $list = [];
            self::flattenTree($tree, $list, 0, true);
            return $list;
        }

        $lines = self::byTemplate($templateId)
            ->orderBy('account_code')
            ->get();

        $list = [];
        foreach ($lines as $line) {
            $list[$line->id] = $line->full_name;
        }

        return $list;
    }

    /**
     * دریافت درختواره ردیف‌های یک قالب
     * @param int $templateId
     * @param string|null $parentCode
     * @return \Illuminate\Support\Collection
     */
    public static function getTree($templateId, $parentCode = null)
    {
        $lines = self::byTemplate($templateId)
            ->where('parent_code', $parentCode)
            ->orderBy('account_code')
            ->get();

        foreach ($lines as $line) {
            $line->children = self::getTree($templateId, $line->account_code);
        }

        return $lines;
    }

    /**
     * تخت کردن درختواره برای استفاده در dropdown
     * @param \Illuminate\Support\Collection $tree
     * @param array &$list
     * @param int $level
     * @param bool $withCode
     */
    public static function flattenTree($tree, &$list, $level = 0, $withCode = false)
    {
        foreach ($tree as $item) {
            $indent = str_repeat('— ', $level);
            if ($withCode) {
                $list[$item->id] = $indent . $item->account_code . ' - ' . $item->account_name;
            } else {
                $list[$item->id] = $indent . $item->account_name;
            }

            if ($item->children && $item->children->count() > 0) {
                self::flattenTree($item->children, $list, $level + 1, $withCode);
            }
        }
    }

    /**
     * دریافت ردیف‌های ریشه یک قالب
     * @param int $templateId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRootLines($templateId)
    {
        return self::byTemplate($templateId)
            ->root()
            ->orderBy('account_code')
            ->get();
    }

    /**
     * دریافت ردیف بر اساس کد و قالب
     * @param int $templateId
     * @param string $accountCode
     * @return self|null
     */
    public static function getByCode($templateId, $accountCode)
    {
        return self::byTemplate($templateId)
            ->where('account_code', $accountCode)
            ->first();
    }

    /**
     * دریافت آمار ردیف‌های یک قالب
     * @param int $templateId
     * @return array
     */
    public static function getStatistics($templateId)
    {
        $total = self::byTemplate($templateId)->count();
        $root = self::byTemplate($templateId)->root()->count();
        $leaf = self::byTemplate($templateId)->leaf()->count();
        $hasChildren = self::byTemplate($templateId)->hasChildren()->count();

        $headers = self::byTemplate($templateId)->headers()->count();
        $details = self::byTemplate($templateId)->details()->count();

        $assets = self::byTemplate($templateId)->byCategory(self::CATEGORY_ASSET)->count();
        $liabilities = self::byTemplate($templateId)->byCategory(self::CATEGORY_LIABILITY)->count();
        $equity = self::byTemplate($templateId)->byCategory(self::CATEGORY_EQUITY)->count();
        $revenue = self::byTemplate($templateId)->byCategory(self::CATEGORY_REVENUE)->count();
        $expense = self::byTemplate($templateId)->byCategory(self::CATEGORY_EXPENSE)->count();

        // محاسبه میانگین سطح
        $lines = self::byTemplate($templateId)->get();
        $totalLevel = 0;
        foreach ($lines as $line) {
            $totalLevel += $line->getLevel();
        }
        $avgLevel = $total > 0 ? $totalLevel / $total : 0;

        return [
            'total' => $total,
            'root' => $root,
            'leaf' => $leaf,
            'has_children' => $hasChildren,
            'headers' => $headers,
            'details' => $details,
            'avg_level' => round($avgLevel, 2),
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
     * ایجاد ردیف جدید با اعتبارسنجی
     * @param int $templateId
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function createWithValidation($templateId, array $data)
    {
        // بررسی وجود کد تکراری در قالب
        if (isset($data['account_code'])) {
            $exists = self::byTemplate($templateId)
                ->where('account_code', $data['account_code'])
                ->exists();

            if ($exists) {
                throw new \Exception('کد حساب تکراری است.');
            }
        }

        // بررسی والد (اگر وجود دارد)
        if (isset($data['parent_code']) && $data['parent_code']) {
            $parent = self::byTemplate($templateId)
                ->where('account_code', $data['parent_code'])
                ->first();

            if (!$parent) {
                throw new \Exception('والد مشخص شده وجود ندارد.');
            }

            // بررسی اینکه والد از نوع کل باشد
            if ($parent->type !== self::TYPE_HEADER) {
                throw new \Exception('والد باید از نوع کل (گروهی) باشد.');
            }
        }

        // اگر حساب از نوع جزئی است، باید قابل ثبت باشد
        if (isset($data['type']) && $data['type'] === self::TYPE_DETAIL) {
            $data['normal_balance'] = $data['normal_balance'] ?? self::BALANCE_DEBIT;
        }

        return self::create(array_merge($data, ['template_id' => $templateId]));
    }

    /**
     * ایجاد چندین ردیف به صورت گروهی
     * @param int $templateId
     * @param array $lines
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createMultiple($templateId, array $lines)
    {
        $created = collect();

        foreach ($lines as $lineData) {
            $line = self::createWithValidation($templateId, $lineData);
            $created->push($line);
        }

        return $created;
    }

    /**
     * دریافت ردیف‌ها بر اساس دسته‌بندی
     * @param int $templateId
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCategory($templateId, $category)
    {
        return self::byTemplate($templateId)
            ->byCategory($category)
            ->orderBy('account_code')
            ->get();
    }

    /**
     * دریافت ردیف‌های قابل تبدیل به حساب (جزئی)
     * @param int $templateId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPostableLines($templateId)
    {
        return self::byTemplate($templateId)
            ->details()
            ->orderBy('account_code')
            ->get();
    }

    /**
     * کپی ردیف‌ها از یک قالب به قالب دیگر
     * @param int $sourceTemplateId
     * @param int $targetTemplateId
     * @return int
     */
    public static function copyFromTemplate($sourceTemplateId, $targetTemplateId)
    {
        $sourceLines = self::byTemplate($sourceTemplateId)->get();
        $count = 0;
        $map = [];

        foreach ($sourceLines as $sourceLine) {
            // ایجاد ردیف جدید در قالب هدف
            $newLine = self::create([
                'template_id' => $targetTemplateId,
                'account_code' => $sourceLine->account_code,
                'account_name' => $sourceLine->account_name,
                'parent_code' => null, // ابتدا والد را null می‌گذاریم
                'category' => $sourceLine->category,
                'type' => $sourceLine->type,
                'normal_balance' => $sourceLine->normal_balance,
            ]);

            // ذخیره نگاشت کد قدیمی به جدید
            $map[$sourceLine->account_code] = $newLine->account_code;
            $count++;
        }

        // به‌روزرسانی والدها
        foreach ($sourceLines as $sourceLine) {
            if ($sourceLine->parent_code && isset($map[$sourceLine->parent_code])) {
                $newLine = self::where('template_id', $targetTemplateId)
                    ->where('account_code', $map[$sourceLine->account_code])
                    ->first();

                if ($newLine) {
                    $newLine->parent_code = $map[$sourceLine->parent_code];
                    $newLine->save();
                }
            }
        }

        return $count;
    }

    /**
     * اعتبارسنجی ساختار سلسله‌مراتبی یک قالب
     * @param int $templateId
     * @return array
     */
    public static function validateHierarchy($templateId)
    {
        $errors = [];
        $lines = self::byTemplate($templateId)->get();

        foreach ($lines as $line) {
            // بررسی وجود والد
            if ($line->parent_code) {
                $parent = self::byTemplate($templateId)
                    ->where('account_code', $line->parent_code)
                    ->first();

                if (!$parent) {
                    $errors[] = "حساب {$line->account_code} دارای والد ناموجود است.";
                }

                // بررسی اینکه والد از نوع کل باشد
                if ($parent && $parent->type !== self::TYPE_HEADER) {
                    $errors[] = "والد حساب {$line->account_code} باید از نوع کل باشد.";
                }
            }

            // بررسی اینکه حساب‌های کل نباید قابل ثبت باشند
            if ($line->type === self::TYPE_HEADER) {
                // بررسی اینکه آیا حساب‌های کل فرزند دارند
                $hasChildren = self::byTemplate($templateId)
                    ->where('parent_code', $line->account_code)
                    ->exists();

                if (!$hasChildren) {
                    $errors[] = "حساب کل {$line->account_code} هیچ فرزندی ندارد.";
                }
            }
        }

        // بررسی وجود دور در ساختار
        // (برای ساده‌سازی، این بخش در اینجا پیاده‌سازی نشده است)

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
