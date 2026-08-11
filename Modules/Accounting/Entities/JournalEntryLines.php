<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class JournalEntryLines extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'journal_entry_lines';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'journal_entry_id',
        'line_no',
        'account_id',
        'partner_id',
        'project_id',
        'department_id',
        'cost_center_id',
        'description',
        'debit',
        'credit',
        'currency_id',
        'exchange_rate',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'line_no' => 'integer',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
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

    /**
     * Boot method برای تنظیم شماره ردیف به‌طور خودکار
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->line_no)) {
                $maxLineNo = self::where('journal_entry_id', $model->journal_entry_id)->max('line_no');
                $model->line_no = $maxLineNo ? $maxLineNo + 1 : 1;
            }
        });
    }

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\JournalEntryLinesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\JournalEntryLinesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه belongsTo با مدل JournalEntry
     * هر ردیف متعلق به یک سند حسابداری است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntries::class, 'journal_entry_id');
    }

    /**
     * رابطه belongsTo با مدل Account
     * هر ردیف متعلق به یک حساب مالی است
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Accounts::class, 'account_id');
    }

    /**
     * رابطه belongsTo با مدل Partner (طرف حساب)
     * هر ردیف می‌تواند به یک طرف حساب متصل باشد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partners::class, 'partner_id');
    }

    /**
     * رابطه belongsTo با مدل Project
     * هر ردیف می‌تواند به یک پروژه متصل باشد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }

    /**
     * رابطه belongsTo با مدل Department
     * هر ردیف می‌تواند به یک دپارتمان متصل باشد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Departments::class, 'department_id');
    }

    /**
     * رابطه belongsTo با مدل CostCenter
     * هر ردیف می‌تواند به یک مرکز هزینه متصل باشد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenters::class, 'cost_center_id');
    }

    /**
     * رابطه belongsTo با مدل Currency
     * هر ردیف می‌تواند ارز متفاوتی داشته باشد
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currencies::class, 'currency_id');
    }

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر بر اساس سند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $journalEntryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEntry($query, $journalEntryId)
    {
        return $query->where('journal_entry_id', $journalEntryId);
    }

    /**
     * سکوپ برای فیلتر بر اساس حساب
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $accountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
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
     * سکوپ برای فیلتر بر اساس پروژه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $projectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * سکوپ برای فیلتر بر اساس دپارتمان
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * سکوپ برای فیلتر بر اساس مرکز هزینه
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $costCenterId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCostCenter($query, $costCenterId)
    {
        return $query->where('cost_center_id', $costCenterId);
    }

    /**
     * سکوپ برای فیلتر ردیف‌های بدهکار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDebit($query)
    {
        return $query->where('debit', '>', 0);
    }

    /**
     * سکوپ برای فیلتر ردیف‌های بستانکار
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCredit($query)
    {
        return $query->where('credit', '>', 0);
    }

    /**
     * سکوپ برای فیلتر ردیف‌هایی که مبلغ دارند
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasAmount($query)
    {
        return $query->where(function ($q) {
            $q->where('debit', '>', 0)
              ->orWhere('credit', '>', 0);
        });
    }

    /**
     * سکوپ برای فیلتر ردیف‌های با ارز متفاوت
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDifferentCurrency($query)
    {
        return $query->whereNotNull('currency_id');
    }

    /**
     * سکوپ برای مرتب‌سازی بر اساس شماره ردیف
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByLine($query)
    {
        return $query->orderBy('line_no');
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نوع ردیف (بدهکار یا بستانکار)
     * @return string
     */
    public function getTypeAttribute()
    {
        if ($this->debit > 0) {
            return 'debit';
        } elseif ($this->credit > 0) {
            return 'credit';
        }
        return 'none';
    }

    /**
     * دریافت نوع ردیف به صورت متنی
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'debit' => 'بدهکار',
            'credit' => 'بستانکار',
            'none' => 'بدون مبلغ',
        ];
        
        return $labels[$this->type] ?? 'نامشخص';
    }

    /**
     * دریافت کلاس CSS برای نوع ردیف
     * @return string
     */
    public function getTypeClassAttribute()
    {
        $classes = [
            'debit' => 'text-danger',
            'credit' => 'text-success',
            'none' => 'text-muted',
        ];
        
        return $classes[$this->type] ?? 'text-muted';
    }

    /**
     * دریافت مبلغ به صورت فرمت شده
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        if ($this->debit > 0) {
            return number_format($this->debit, 2) . ' (بدهکار)';
        } elseif ($this->credit > 0) {
            return number_format($this->credit, 2) . ' (بستانکار)';
        }
        return '0';
    }

    /**
     * دریافت نام کامل ردیف (شماره - حساب - مبلغ)
     * @return string
     */
    public function getFullDescriptionAttribute()
    {
        $parts = [];
        $parts[] = 'ردیف ' . $this->line_no;
        $parts[] = $this->account ? $this->account->account_name : 'حساب نامشخص';
        $parts[] = $this->formatted_amount;
        
        if ($this->description) {
            $parts[] = $this->description;
        }
        
        return implode(' - ', $parts);
    }

    /**
     * دریافت مبلغ به ارز پایه (با اعمال نرخ ارز)
     * @return float
     */
    public function getBaseAmountAttribute()
    {
        $amount = $this->debit > 0 ? $this->debit : $this->credit;
        
        if ($this->exchange_rate && $this->exchange_rate != 1) {
            return $amount * $this->exchange_rate;
        }
        
        return $amount;
    }

    /**
     * بررسی اینکه آیا ردیف بدهکار است؟
     * @return bool
     */
    public function isDebit()
    {
        return $this->debit > 0;
    }

    /**
     * بررسی اینکه آیا ردیف بستانکار است؟
     * @return bool
     */
    public function isCredit()
    {
        return $this->credit > 0;
    }

    /**
     * بررسی اینکه آیا ردیف مبلغ دارد؟
     * @return bool
     */
    public function hasAmount()
    {
        return $this->debit > 0 || $this->credit > 0;
    }

    /**
     * دریافت مبلغ ردیف (بدون توجه به نوع)
     * @return float
     */
    public function getAmount()
    {
        return $this->debit > 0 ? $this->debit : $this->credit;
    }

    /**
     * دریافت مبلغ با علامت (بدهکار مثبت، بستانکار منفی)
     * @return float
     */
    public function getSignedAmount()
    {
        if ($this->debit > 0) {
            return $this->debit;
        } elseif ($this->credit > 0) {
            return -$this->credit;
        }
        return 0;
    }

    /**
     * کلون کردن ردیف برای سند جدید
     * @param int $newEntryId
     * @return self
     */
    public function duplicate($newEntryId)
    {
        $newLine = $this->replicate();
        $newLine->journal_entry_id = $newEntryId;
        $newLine->line_no = null; // شماره ردیف به‌طور خودکار تنظیم می‌شود
        $newLine->save();
        
        return $newLine;
    }

    /**
     * بررسی وجود طرف حساب
     * @return bool
     */
    public function hasPartner()
    {
        return !is_null($this->partner_id);
    }

    /**
     * بررسی وجود پروژه
     * @return bool
     */
    public function hasProject()
    {
        return !is_null($this->project_id);
    }

    /**
     * بررسی وجود دپارتمان
     * @return bool
     */
    public function hasDepartment()
    {
        return !is_null($this->department_id);
    }

    /**
     * بررسی وجود مرکز هزینه
     * @return bool
     */
    public function hasCostCenter()
    {
        return !is_null($this->cost_center_id);
    }

    /**
     * بررسی وجود ارز متفاوت
     * @return bool
     */
    public function hasDifferentCurrency()
    {
        return !is_null($this->currency_id) && !is_null($this->exchange_rate);
    }

    // ========== متدهای استاتیک (Static Methods) ==========

    /**
     * دریافت مجموع بدهکار و بستانکار یک سند
     * @param int $journalEntryId
     * @return array
     */
    public static function getTotals($journalEntryId)
    {
        $totalDebit = self::byEntry($journalEntryId)->sum('debit');
        $totalCredit = self::byEntry($journalEntryId)->sum('credit');
        
        return [
            'debit' => $totalDebit,
            'credit' => $totalCredit,
            'difference' => $totalDebit - $totalCredit,
            'is_balanced' => $totalDebit == $totalCredit,
        ];
    }

    /**
     * دریافت ردیف‌های یک حساب در یک بازه زمانی
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByAccountAndDate($accountId, $startDate, $endDate)
    {
        return self::byAccount($accountId)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->dateBetween($startDate, $endDate)
                  ->posted();
            })
            ->orderBy('created_at')
            ->get();
    }

    /**
     * دریافت مانده یک حساب در تاریخ مشخص
     * @param int $accountId
     * @param string|null $dateTo
     * @return float
     */
    public static function getAccountBalance($accountId, $dateTo = null)
    {
        $query = self::byAccount($accountId)
            ->whereHas('journalEntry', function ($q) use ($dateTo) {
                $q->posted();
                if ($dateTo) {
                    $q->where('posting_date', '<=', $dateTo);
                }
            });
        
        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        
        // دریافت مانده عادی حساب
        $account = Accounts::find($accountId);
        if ($account) {
            if ($account->normal_balance === 'debit') {
                return $totalDebit - $totalCredit;
            } else {
                return $totalCredit - $totalDebit;
            }
        }
        
        return $totalDebit - $totalCredit;
    }

    /**
     * دریافت ردیف‌های یک طرف حساب
     * @param int $partnerId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByPartner($partnerId, $startDate = null, $endDate = null)
    {
        $query = self::byPartner($partnerId)
            ->whereHas('journalEntry', function ($q) {
                $q->posted();
            });
        
        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->dateBetween($startDate, $endDate);
            });
        }
        
        return $query->orderBy('created_at')->get();
    }

    /**
     * دریافت ردیف‌های یک پروژه
     * @param int $projectId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByProject($projectId, $startDate = null, $endDate = null)
    {
        $query = self::byProject($projectId)
            ->whereHas('journalEntry', function ($q) {
                $q->posted();
            });
        
        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->dateBetween($startDate, $endDate);
            });
        }
        
        return $query->orderBy('created_at')->get();
    }

    /**
     * دریافت ردیف‌های یک دپارتمان
     * @param int $departmentId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByDepartment($departmentId, $startDate = null, $endDate = null)
    {
        $query = self::byDepartment($departmentId)
            ->whereHas('journalEntry', function ($q) {
                $q->posted();
            });
        
        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->dateBetween($startDate, $endDate);
            });
        }
        
        return $query->orderBy('created_at')->get();
    }

    /**
     * دریافت ردیف‌های یک مرکز هزینه
     * @param int $costCenterId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCostCenter($costCenterId, $startDate = null, $endDate = null)
    {
        $query = self::byCostCenter($costCenterId)
            ->whereHas('journalEntry', function ($q) {
                $q->posted();
            });
        
        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->dateBetween($startDate, $endDate);
            });
        }
        
        return $query->orderBy('created_at')->get();
    }

    /**
     * دریافت خلاصه ردیف‌ها بر اساس حساب
     * @param int $journalEntryId
     * @return array
     */
    public static function getSummaryByAccount($journalEntryId)
    {
        return self::byEntry($journalEntryId)
            ->select('account_id')
            ->selectRaw('SUM(debit) as total_debit')
            ->selectRaw('SUM(credit) as total_credit')
            ->groupBy('account_id')
            ->with('account')
            ->get()
            ->toArray();
    }

    /**
     * اعتبارسنجی ردیف‌های یک سند
     * @param array $lines
     * @return array
     */
    public static function validateLines(array $lines)
    {
        $errors = [];
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($lines as $index => $line) {
            // بررسی وجود حساب
            if (empty($line['account_id'])) {
                $errors[] = "ردیف " . ($index + 1) . ": حساب مشخص نشده است.";
            }
            
            // بررسی مبلغ
            if (empty($line['debit']) && empty($line['credit'])) {
                $errors[] = "ردیف " . ($index + 1) . ": مبلغ بدهکار یا بستانکار وارد نشده است.";
            }
            
            if (!empty($line['debit']) && !empty($line['credit'])) {
                $errors[] = "ردیف " . ($index + 1) . ": یک ردیف نمی‌تواند هم بدهکار و هم بستانکار باشد.";
            }
            
            $totalDebit += $line['debit'] ?? 0;
            $totalCredit += $line['credit'] ?? 0;
        }
        
        // بررسی تراز بودن
        if ($totalDebit != $totalCredit) {
            $errors[] = "مجموع بدهکار ({$totalDebit}) و بستانکار ({$totalCredit}) برابر نیست.";
        }
        
        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ];
    }

    /**
     * ایجاد ردیف‌های یک سند به صورت گروهی
     * @param int $journalEntryId
     * @param array $lines
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function createLines($journalEntryId, array $lines)
    {
        $createdLines = collect();
        
        foreach ($lines as $lineData) {
            $line = self::create(array_merge($lineData, [
                'journal_entry_id' => $journalEntryId
            ]));
            $createdLines->push($line);
        }
        
        return $createdLines;
    }

    /**
     * به‌روزرسانی ردیف‌های یک سند
     * @param int $journalEntryId
     * @param array $lines
     * @return bool
     */
    public static function updateLines($journalEntryId, array $lines)
    {
        // حذف ردیف‌های قبلی
        self::byEntry($journalEntryId)->delete();
        
        // ایجاد ردیف‌های جدید
        self::createLines($journalEntryId, $lines);
        
        return true;
    }
}