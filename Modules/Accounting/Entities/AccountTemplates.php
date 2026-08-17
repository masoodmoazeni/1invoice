<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class AccountTemplates extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'account_templates';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'country_id',
        'name',
        'version',
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
     * @return \Modules\Accounting\Database\factories\AccountTemplatesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\AccountTemplatesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Country
     * هر قالب متعلق به یک کشور است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    /**
     * رابطه hasMany برای آیتم‌های قالب
     * هر قالب شامل چندین حساب است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(AccountTemplateItems::class, 'account_template_id');
    }

    /**
     * دریافت حساب‌های ریشه (بدون والد)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRootItems()
    {
        return $this->items()->whereNull('parent_code')->orderBy('sort_order')->get();
    }

    /**
     * دریافت حساب‌های یک دسته‌بندی خاص
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getItemsByCategory($category)
    {
        return $this->items()->where('account_category', $category)->orderBy('account_code')->get();
    }

    /**
     * دریافت تعداد کل حساب‌های قالب
     * @return int
     */
    public function getTotalAccountsCount()
    {
        return $this->items()->count();
    }

    /**
     * دریافت تعداد حساب‌های هر دسته‌بندی
     * @return array
     */
    public function getCategoryCounts()
    {
        $categories = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $counts = [];
        
        foreach ($categories as $category) {
            $counts[$category] = $this->items()->where('account_category', $category)->count();
        }
        
        return $counts;
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر بر اساس کشور
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * سکوپ برای فیلتر بر اساس نام
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }

    /**
     * سکوپ برای فیلتر بر اساس نسخه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $version
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByVersion($query, $version)
    {
        return $query->where('version', $version);
    }

    /**
     * سکوپ برای دریافت آخرین نسخه قالب‌ها
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatestVersion($query)
    {
        return $query->orderBy('version', 'desc');
    }

    /**
     * سکوپ برای جستجوی قالب‌ها
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('version', 'LIKE', "%{$search}%");
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل قالب (نام به همراه نسخه)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (نسخه ' . $this->version . ')';
    }

    /**
     * دریافت نام کشور به همراه قالب
     * @return string
     */
    public function getCountryTemplateAttribute()
    {
        return ($this->country ? $this->country->name : '') . ' - ' . $this->name;
    }

    /**
     * بررسی وجود نسخه جدیدتر
     * @return bool
     */
    public function hasNewerVersion()
    {
        return self::byCountry($this->country_id)
            ->where('id', '!=', $this->id)
            ->where('version', '>', $this->version)
            ->exists();
    }

    /**
     * دریافت نسخه جدیدتر
     * @return self|null
     */
    public function getNewerVersion()
    {
        return self::byCountry($this->country_id)
            ->where('id', '!=', $this->id)
            ->where('version', '>', $this->version)
            ->orderBy('version', 'desc')
            ->first();
    }

    /**
     * دریافت نسخه قدیمی‌تر
     * @return self|null
     */
    public function getOlderVersion()
    {
        return self::byCountry($this->country_id)
            ->where('id', '!=', $this->id)
            ->where('version', '<', $this->version)
            ->orderBy('version', 'desc')
            ->first();
    }

    /**
     * دریافت درختواره حساب‌های قالب
     * @param string|null $parentCode
     * @return \Illuminate\Support\Collection
     */
    public function getTree($parentCode = null)
    {
        $items = $this->items()
            ->where('parent_code', $parentCode)
            ->orderBy('sort_order')
            ->orderBy('account_code')
            ->get();
        
        foreach ($items as $item) {
            $item->children = $this->getTree($item->account_code);
        }
        
        return $items;
    }

    /**
     * دریافت لیست تخت حساب‌های قالب با تورفتگی
     * @param string|null $parentCode
     * @param int $level
     * @return \Illuminate\Support\Collection
     */
    public function getFlatTree($parentCode = null, $level = 0)
    {
        $result = collect();
        $items = $this->items()
            ->where('parent_code', $parentCode)
            ->orderBy('sort_order')
            ->orderBy('account_code')
            ->get();
        
        foreach ($items as $item) {
            $item->level = $level;
            $result->push($item);
            $result = $result->merge($this->getFlatTree($item->account_code, $level + 1));
        }
        
        return $result;
    }

    /**
     * دریافت اطلاعات کامل قالب برای گزارش
     * @return array
     */
    public function getReportInfo()
    {
        $categoryCounts = $this->getCategoryCounts();
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'version' => $this->version,
            'country' => $this->country ? $this->country->name : null,
            'total_accounts' => $this->getTotalAccountsCount(),
            'category_counts' => $categoryCounts,
            'has_newer_version' => $this->hasNewerVersion(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * کلون کردن قالب با نسخه جدید
     * @param string $newVersion
     * @param string|null $newName
     * @return self
     */
    public function duplicate($newVersion, $newName = null)
    {
        // ایجاد قالب جدید
        $newTemplate = $this->replicate();
        $newTemplate->version = $newVersion;
        $newTemplate->name = $newName ?? $this->name . ' (نسخه ' . $newVersion . ')';
        $newTemplate->save();
        
        // کپی کردن آیتم‌ها
        foreach ($this->items as $item) {
            $newItem = $item->replicate();
            $newItem->account_template_id = $newTemplate->id;
            $newItem->save();
        }
        
        return $newTemplate;
    }

    /**
     * اعمال قالب به یک شرکت (ایجاد حساب‌ها)
     * @param int $companyId
     * @return int
     */
    public function applyToCompany($companyId)
    {
        $count = 0;
        $map = []; // برای نگاشت کدهای والد
        
        // دریافت آیتم‌های قالب به ترتیب
        $items = $this->items()->orderBy('level')->orderBy('account_code')->get();
        
        foreach ($items as $item) {
            // بررسی وجود حساب با کد مشابه
            $existingAccount = Accounts::byCompany($companyId)
                ->where('account_code', $item->account_code)
                ->first();
            
            if ($existingAccount) {
                continue; // حساب وجود دارد
            }
            
            // پیدا کردن والد
            $parentId = null;
            if ($item->parent_code && isset($map[$item->parent_code])) {
                $parentId = $map[$item->parent_code];
            }
            
            // ایجاد حساب
            $account = Accounts::create([
                'company_id' => $companyId,
                'account_code' => $item->account_code,
                'account_name' => $item->account_name,
                'parent_id' => $parentId,
                'account_category' => $item->account_category,
                'account_type' => $item->account_type,
                'normal_balance' => $item->normal_balance,
                'allow_posting' => $item->allow_posting,
                'is_system' => $item->is_system,
                'is_active' => true,
                'level' => $item->level,
                'sort_order' => $item->sort_order,
            ]);
            
            $map[$item->account_code] = $account->id;
            $count++;
        }
        
        return $count;
    }

    /**
     * دریافت تفاوت بین دو قالب
     * @param int $templateId
     * @return array
     */
    public function getDifferences($templateId)
    {
        $otherTemplate = self::find($templateId);
        
        if (!$otherTemplate) {
            return ['error' => 'قالب مورد نظر یافت نشد.'];
        }
        
        $thisItems = $this->items()->pluck('account_code')->toArray();
        $otherItems = $otherTemplate->items()->pluck('account_code')->toArray();
        
        $onlyInThis = array_diff($thisItems, $otherItems);
        $onlyInOther = array_diff($otherItems, $thisItems);
        
        return [
            'template1' => [
                'id' => $this->id,
                'name' => $this->name,
                'total_items' => count($thisItems),
            ],
            'template2' => [
                'id' => $otherTemplate->id,
                'name' => $otherTemplate->name,
                'total_items' => count($otherItems),
            ],
            'only_in_template1' => $onlyInThis,
            'only_in_template2' => $onlyInOther,
            'common_items' => array_intersect($thisItems, $otherItems),
        ];
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست قالب‌ها برای استفاده در dropdown
     * @param int $countryId
     * @return array
     */
    public static function getList($countryId)
    {
        return self::byCountry($countryId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * دریافت لیست قالب‌ها با فرمت کامل (نام به همراه نسخه)
     * @param int $countryId
     * @return array
     */
    public static function getListWithVersion($countryId)
    {
        $templates = self::byCountry($countryId)
            ->orderBy('name')
            ->get();
        
        $list = [];
        foreach ($templates as $template) {
            $list[$template->id] = $template->full_name;
        }
        
        return $list;
    }

    /**
     * دریافت آخرین نسخه قالب برای یک کشور
     * @param int $countryId
     * @param string $templateName
     * @return self|null
     */
    public static function getLatestVersion($countryId, $templateName)
    {
        return self::byCountry($countryId)
            ->where('name', $templateName)
            ->latestVersion()
            ->first();
    }

    /**
     * دریافت آمار قالب‌ها
     * @param int|null $countryId
     * @return array
     */
    public static function getStatistics($countryId = null)
    {
        $query = self::query();
        
        if ($countryId) {
            $query->byCountry($countryId);
        }
        
        $total = $query->count();
        $withItems = $query->has('items')->count();
        $withoutItems = $total - $withItems;
        
        // دریافت تعداد قالب‌ها بر اساس کشور
        $byCountry = self::with('country')
            ->select('country_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('country_id')
            ->get()
            ->map(function ($item) {
                return [
                    'country_name' => $item->country ? $item->country->name : 'نامشخص',
                    'count' => $item->count,
                ];
            });
        
        return [
            'total_templates' => $total,
            'with_items' => $withItems,
            'without_items' => $withoutItems,
            'by_country' => $byCountry,
        ];
    }

    /**
     * ایجاد قالب با آیتم‌ها
     * @param int $countryId
     * @param array $templateData
     * @param array $itemsData
     * @return self
     */
    public static function createWithItems($countryId, array $templateData, array $itemsData)
    {
        // ایجاد قالب
        $template = self::create(array_merge($templateData, [
            'country_id' => $countryId,
        ]));
        
        // ایجاد آیتم‌ها
        foreach ($itemsData as $itemData) {
            $template->items()->create($itemData);
        }
        
        return $template;
    }

    /**
     * دریافت قالب استاندارد برای یک کشور
     * @param int $countryId
     * @return self|null
     */
    public static function getDefaultTemplate($countryId)
    {
        return self::byCountry($countryId)
            ->orderBy('version', 'desc')
            ->first();
    }

    /**
     * وارد کردن قالب از فایل JSON
     * @param int $countryId
     * @param string $jsonPath
     * @return self
     */
    public static function importFromJson($countryId, $jsonPath)
    {
        $content = file_get_contents($jsonPath);
        $data = json_decode($content, true);
        
        if (!$data) {
            throw new \Exception('فایل JSON معتبر نیست.');
        }
        
        // ایجاد قالب
        $template = self::create([
            'country_id' => $countryId,
            'name' => $data['name'],
            'version' => $data['version'],
        ]);
        
        // ایجاد آیتم‌ها
        foreach ($data['items'] as $itemData) {
            $template->items()->create($itemData);
        }
        
        return $template;
    }

    /**
     * خروجی گرفتن از قالب به صورت JSON
     * @param int $templateId
     * @param string $filePath
     * @return bool
     */
    public static function exportToJson($templateId, $filePath)
    {
        $template = self::with('items')->find($templateId);
        
        if (!$template) {
            return false;
        }
        
        $data = [
            'name' => $template->name,
            'version' => $template->version,
            'country_id' => $template->country_id,
            'items' => $template->items->toArray(),
            'exported_at' => now()->toDateTimeString(),
        ];
        
        return file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }
}