<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TaxGroup extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'tax_groups';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
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

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\TaxGroupsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\TaxGroupsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر گروه مالیاتی متعلق به یک شرکت است
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsToMany با مدل Taxes
     * هر گروه مالیاتی می‌تواند شامل چندین مالیات باشد
     * از جدول رابط tax_group_items برای اتصال استفاده می‌شود
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxes()
    {
        return $this->belongsToMany(Taxes::class, 'tax_group_items', 'tax_group_id', 'tax_id')
                    ->withPivot('priority', 'is_required', 'created_at')
                    ->withTimestamps()
                    ->orderByPivot('priority');
    }

    /**
     * رابطه belongsToMany با مدل Taxes (فقط مالیات‌های فعال)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activeTaxes()
    {
        return $this->belongsToMany(Taxes::class, 'tax_group_items', 'tax_group_id', 'tax_id')
                    ->where('taxes.is_active', true)
                    ->withPivot('priority', 'is_required', 'created_at')
                    ->withTimestamps()
                    ->orderByPivot('priority');
    }

    /**
     * رابطه hasMany با مدل TaxGroupItems
     * برای دسترسی مستقیم به آیتم‌های گروه مالیاتی
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupItems()
    {
        return $this->hasMany(TaxGroupItems::class, 'tax_group_id');
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
     * سکوپ برای جستجو بر اساس کد گروه مالیاتی
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای جستجوی گروه‌های مالیاتی بر اساس نام
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
     * سکوپ برای دریافت گروه‌هایی که حداقل یک مالیات دارند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasTaxes($query)
    {
        return $query->has('taxes');
    }

    /**
     * سکوپ برای دریافت گروه‌هایی که مالیات ندارند (خالی)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmpty($query)
    {
        return $query->doesntHave('taxes');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل گروه مالیاتی (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت تعداد مالیات‌های موجود در گروه
     * @return int
     */
    public function getTaxCountAttribute()
    {
        return $this->taxes()->count();
    }

    /**
     * دریافت تعداد مالیات‌های فعال در گروه
     * @return int
     */
    public function getActiveTaxCountAttribute()
    {
        return $this->activeTaxes()->count();
    }

    /**
     * بررسی اینکه آیا گروه مالیاتی خالی است
     * @return bool
     */
    public function isEmpty()
    {
        return $this->taxes()->count() === 0;
    }

    /**
     * اضافه کردن یک مالیات به گروه
     * @param int $taxId
     * @param int $priority
     * @param bool $isRequired
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addTax($taxId, $priority = 0, $isRequired = true)
    {
        return $this->taxes()->attach($taxId, [
            'priority' => $priority,
            'is_required' => $isRequired,
        ]);
    }

    /**
     * اضافه کردن چندین مالیات به گروه به‌صورت همزمان
     * @param array $taxIds
     * @param int $priority
     * @param bool $isRequired
     * @return void
     */
    public function addMultipleTaxes(array $taxIds, $priority = 0, $isRequired = true)
    {
        $items = [];
        foreach ($taxIds as $taxId) {
            $items[$taxId] = [
                'priority' => $priority,
                'is_required' => $isRequired,
            ];
        }
        $this->taxes()->attach($items);
    }

    /**
     * حذف یک مالیات از گروه
     * @param int $taxId
     * @return int
     */
    public function removeTax($taxId)
    {
        return $this->taxes()->detach($taxId);
    }

    /**
     * حذف تمام مالیات‌ها از گروه
     * @return int
     */
    public function clearAllTaxes()
    {
        return $this->taxes()->detach();
    }

    /**
     * به‌روزرسانی اولویت یک مالیات در گروه
     * @param int $taxId
     * @param int $priority
     * @return bool
     */
    public function updateTaxPriority($taxId, $priority)
    {
        return $this->taxes()->updateExistingPivot($taxId, [
            'priority' => $priority
        ]);
    }

    /**
     * به‌روزرسانی وضعیت الزامی بودن یک مالیات در گروه
     * @param int $taxId
     * @param bool $isRequired
     * @return bool
     */
    public function updateTaxRequirement($taxId, $isRequired)
    {
        return $this->taxes()->updateExistingPivot($taxId, [
            'is_required' => $isRequired
        ]);
    }

    /**
     * محاسبه مجموع مالیات‌های گروه برای یک مبلغ مشخص
     * @param float $amount
     * @param float $discount
     * @return array
     */
    public function calculateTaxes($amount, $discount = 0)
    {
        $results = [];
        $totalTax = 0;

        foreach ($this->activeTaxes as $tax) {
            $taxAmount = $tax->calculateTaxWithDiscount($amount, $discount);
            $results[] = [
                'tax_id' => $tax->id,
                'tax_code' => $tax->code,
                'tax_name' => $tax->name,
                'tax_rate' => $tax->rate,
                'tax_amount' => $taxAmount,
                'is_required' => $tax->pivot->is_required ?? true,
                'priority' => $tax->pivot->priority ?? 0,
            ];
            $totalTax += $taxAmount;
        }

        return [
            'group_id' => $this->id,
            'group_name' => $this->name,
            'taxes' => $results,
            'total_tax' => $totalTax,
        ];
    }

    /**
     * دریافت لیست مالیات‌های الزامی گروه
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRequiredTaxes()
    {
        return $this->taxes()->wherePivot('is_required', true)->get();
    }

    /**
     * دریافت لیست مالیات‌های اختیاری گروه
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOptionalTaxes()
    {
        return $this->taxes()->wherePivot('is_required', false)->get();
    }

    /**
     * دریافت مالیات‌ها به ترتیب اولویت
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTaxesByPriority()
    {
        return $this->taxes()->orderByPivot('priority')->get();
    }

    /**
     * کلون کردن گروه مالیاتی با تمام مالیات‌های آن
     * @param string $newCode
     * @param string $newName
     * @return self
     */
    public function duplicate($newCode, $newName)
    {
        $newGroup = $this->replicate();
        $newGroup->code = $newCode;
        $newGroup->name = $newName;
        $newGroup->save();

        // کپی کردن مالیات‌ها
        foreach ($this->taxes as $tax) {
            $newGroup->taxes()->attach($tax->id, [
                'priority' => $tax->pivot->priority ?? 0,
                'is_required' => $tax->pivot->is_required ?? true,
            ]);
        }

        return $newGroup;
    }

    /**
     * بررسی وجود مالیات در گروه
     * @param int $taxId
     * @return bool
     */
    public function hasTax($taxId)
    {
        return $this->taxes()->where('tax_id', $taxId)->exists();
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست گروه‌های مالیاتی یک شرکت برای استفاده در dropdown
     * @param int $companyId
     * @return array
     */
    public static function getList($companyId)
    {
        return self::byCompany($companyId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * دریافت لیست گروه‌های مالیاتی با تعداد مالیات‌های آنها
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getWithTaxCount($companyId)
    {
        return self::byCompany($companyId)
            ->withCount('taxes')
            ->orderBy('name')
            ->get();
    }

    /**
     * دریافت گروه مالیاتی بر اساس کد و شرکت
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
     * ایجاد گروه مالیاتی با مالیات‌های مشخص
     * @param int $companyId
     * @param string $code
     * @param string $name
     * @param array $taxIds
     * @param string|null $description
     * @return self
     */
    public static function createWithTaxes($companyId, $code, $name, array $taxIds, $description = null)
    {
        $group = self::create([
            'company_id' => $companyId,
            'code' => $code,
            'name' => $name,
            'description' => $description,
        ]);

        $group->addMultipleTaxes($taxIds);

        return $group;
    }
}
