<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Departments extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'departments';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'parent_id',
        'manager_id',
        'is_active',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
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
     * @return \Modules\Accounting\Database\factories\DepartmentsFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\DepartmentsFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل Company
     * هر دپارتمان متعلق به یک شرکت است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * رابطه belongsTo برای دپارتمان والد
     * دریافت دپارتمان بالادستی (سطح بالاتر)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * رابطه hasMany برای دپارتمان‌های فرزند
     * دریافت تمام دپارتمان‌های زیرمجموعه
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * رابطه hasMany برای دپارتمان‌های فرزند فعال
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeChildren()
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->where('is_active', true);
    }

    /**
     * رابطه belongsTo برای مدیر دپارتمان
     * فرض بر این است که جدول users یا employees وجود دارد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id'); // یا Employee::class
    }

    /**
     * رابطه hasMany برای کارمندان دپارتمان (در صورت وجود)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id'); // در صورت وجود جدول employees
    }

    /**
     * دریافت تمام دپارتمان‌های فرزند به صورت بازگشتی (تا هر سطح)
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
     * دریافت تمام دپارتمان‌های والد به صورت بازگشتی (تا ریشه)
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
     * دریافت مسیر کامل دپارتمان (از ریشه تا خودش)
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
     * دریافت سطح دپارتمان در ساختار سلسله‌مراتبی
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
     * سکوپ برای فیلتر دپارتمان‌های فعال
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * سکوپ برای فیلتر دپارتمان‌های غیرفعال
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
     * سکوپ برای دریافت دپارتمان‌های ریشه (بدون والد)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * سکوپ برای دریافت دپارتمان‌های غیرریشه (دارای والد)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotRoot($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * سکوپ برای دریافت دپارتمان‌های یک سطح خاص
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLevel($query, $level)
    {
        if ($level === 0) {
            return $query->whereNull('parent_id');
        }
        
        // برای سطوح بالاتر نیاز به کوئری‌های پیچیده‌تر یا استفاده از nested set
        return $query->whereHas('parent', function ($q) use ($level) {
            $q->where('level', $level - 1);
        });
    }

    /**
     * سکوپ برای جستجو بر اساس کد دپارتمان
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * سکوپ برای جستجوی دپارتمان‌ها بر اساس نام یا کد
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
     * سکوپ برای دریافت دپارتمان‌هایی که دارای دپارتمان فرزند هستند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasChildren($query)
    {
        return $query->has('children');
    }

    /**
     * سکوپ برای دریافت دپارتمان‌هایی که دپارتمان فرزند ندارند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeaf($query)
    {
        return $query->doesntHave('children');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام کامل دپارتمان (نام به همراه کد در پرانتز)
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * دریافت نام دپارتمان با پیش‌فراز (برای نمایش درختواره)
     * @return string
     */
    public function getIndentedNameAttribute()
    {
        $indent = str_repeat('— ', $this->getLevel());
        return $indent . $this->name;
    }

    /**
     * دریافت تعداد کل کارمندان دپارتمان (شامل زیرمجموعه‌ها)
     * @return int
     */
    public function getTotalEmployeesCount()
    {
        $count = $this->employees()->count();
        
        foreach ($this->children as $child) {
            $count += $child->getTotalEmployeesCount();
        }
        
        return $count;
    }

    /**
     * بررسی اینکه آیا دپارتمان ریشه است
     * @return bool
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * بررسی اینکه آیا دپارتمان برگ (بدون فرزند) است
     * @return bool
     */
    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    /**
     * بررسی فعال بودن دپارتمان
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->is_active;
    }

    /**
     * فعال کردن دپارتمان
     * @return bool
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * غیرفعال کردن دپارتمان
     * @return bool
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * غیرفعال کردن دپارتمان به همراه تمام زیرمجموعه‌ها
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
     * دریافت مسیر کامل به صورت آرایه
     * @return array
     */
    public function getPathArray()
    {
        $path = [$this->id];
        $parent = $this->parent;
        
        while ($parent) {
            $path[] = $parent->id;
            $parent = $parent->parent;
        }
        
        return array_reverse($path);
    }

    /**
     * دریافت درختواره دپارتمان‌ها به صورت بازگشتی
     * @param int|null $parentId
     * @return \Illuminate\Support\Collection
     */
    public static function getTree($parentId = null)
    {
        $departments = self::active()
            ->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
        
        foreach ($departments as $department) {
            $department->children = self::getTree($department->id);
        }
        
        return $departments;
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
        $departments = self::active()
            ->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
        
        foreach ($departments as $department) {
            $department->level = $level;
            $result->push($department);
            $result = $result->merge(self::getFlatTree($department->id, $level + 1));
        }
        
        return $result;
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت لیست دپارتمان‌های یک شرکت برای استفاده در dropdown
     * @param int $companyId
     * @param bool $flat
     * @return array
     */
    public static function getList($companyId, $flat = true)
    {
        if ($flat) {
            $departments = self::getFlatTree(null, 0);
            $list = [];
            foreach ($departments as $dept) {
                $indent = str_repeat('— ', $dept->level);
                $list[$dept->id] = $indent . $dept->name;
            }
            return $list;
        }
        
        return self::active()
            ->byCompany($companyId)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * دریافت لیست دپارتمان‌های ریشه یک شرکت
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRootDepartments($companyId)
    {
        return self::active()
            ->byCompany($companyId)
            ->root()
            ->orderBy('name')
            ->get();
    }

    /**
     * دریافت دپارتمان بر اساس کد و شرکت
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
     * بررسی وجود دپارتمان با کد مشخص در شرکت
     * @param int $companyId
     * @param string $code
     * @return bool
     */
    public static function existsByCode($companyId, $code)
    {
        return self::byCompany($companyId)
            ->byCode($code)
            ->exists();
    }

    /**
     * ایجاد خودکار کد دپارتمان بر اساس نام
     * @param string $name
     * @return string
     */
    public static function generateCode($name)
    {
        $code = strtoupper(Str::slug($name, '_'));
        
        // اگر کد تکراری باشد، شماره اضافه می‌شود
        $counter = 1;
        $originalCode = $code;
        while (self::where('code', $code)->exists()) {
            $code = $originalCode . '_' . $counter;
            $counter++;
        }
        
        return $code;
    }
}