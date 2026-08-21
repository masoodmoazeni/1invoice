<?php

namespace Modules\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Files extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * نام جدول در دیتابیس
     * @var string
     */
    protected $table = 'files';

    /**
     * فیلدهایی که امکان Mass Assignment دارند
     * @var array
     */
    protected $fillable = [
        'disk',
        'path',
        'hash',
        'size',
        'mime',
        'uploaded_at',
    ];

    /**
     * فیلدهایی که باید به نوع داده‌های خاص تبدیل شوند
     * @var array
     */
    protected $casts = [
        'size' => 'integer',
        'uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * فیلدهایی که نباید در JSON نمایش داده شوند
     * @var array
     */
    protected $hidden = [];

    /**
     * دیسک‌های پشتیبانی شده
     * @var array
     */
    public static $supportedDisks = [
        'local',
        'public',
        's3',
        'ftp',
        'sftp',
    ];

    /**
     * انواع MIME معروف
     * @var array
     */
    public static $mimeTypes = [
        // تصاویر
        'image/jpeg' => 'JPEG Image',
        'image/png' => 'PNG Image',
        'image/gif' => 'GIF Image',
        'image/svg+xml' => 'SVG Image',
        'image/webp' => 'WebP Image',
        'image/bmp' => 'BMP Image',
        
        // اسناد
        'application/pdf' => 'PDF Document',
        'application/msword' => 'Word Document',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word Document (DOCX)',
        'application/vnd.ms-excel' => 'Excel Spreadsheet',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel Spreadsheet (XLSX)',
        'application/vnd.ms-powerpoint' => 'PowerPoint Presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint Presentation (PPTX)',
        
        // متنی
        'text/plain' => 'Text File',
        'text/html' => 'HTML File',
        'text/csv' => 'CSV File',
        'text/xml' => 'XML File',
        'text/json' => 'JSON File',
        
        // فشرده
        'application/zip' => 'ZIP Archive',
        'application/x-rar-compressed' => 'RAR Archive',
        'application/x-tar' => 'TAR Archive',
        'application/gzip' => 'GZIP Archive',
        
        // صوتی/ویدیویی
        'audio/mpeg' => 'MP3 Audio',
        'audio/wav' => 'WAV Audio',
        'video/mp4' => 'MP4 Video',
        'video/mpeg' => 'MPEG Video',
        'video/quicktime' => 'QuickTime Video',
    ];

    // ========== ایجاد یک نمونه از Factory ==========

    /**
     * ایجاد یک نمونه از Factory برای این مدل
     * @return \Modules\Accounting\Database\factories\FilesFactory
     */
    protected static function newFactory()
    {
        return \Modules\Accounting\Database\factories\FilesFactory::new();
    }

    // ========== روابط (Relationships) ==========

    /**
     * رابطه polymorphic برای اتصال به سایر مدل‌ها
     * می‌تواند برای هر مدلی که فایل دارد استفاده شود
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    

    // ========== سکوپ‌های پرکاربرد (Scopes) ==========

    /**
     * سکوپ برای فیلتر بر اساس دیسک
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $disk
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDisk($query, $disk)
    {
        return $query->where('disk', $disk);
    }

    /**
     * سکوپ برای فیلتر بر اساس نوع MIME
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $mime
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMime($query, $mime)
    {
        return $query->where('mime', $mime);
    }

    /**
     * سکوپ برای فیلتر بر اساس پسوند فایل
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $extension
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByExtension($query, $extension)
    {
        return $query->where('path', 'LIKE', '%.' . $extension);
    }

    /**
     * سکوپ برای فیلتر تصاویر
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeImages($query)
    {
        return $query->where('mime', 'LIKE', 'image/%');
    }

    /**
     * سکوپ برای فیلتر اسناد
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDocuments($query)
    {
        return $query->whereIn('mime', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);
    }

    /**
     * سکوپ برای فیلتر فایل‌های با حجم مشخص
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minSize
     * @param int|null $maxSize
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySize($query, $minSize, $maxSize = null)
    {
        $query->where('size', '>=', $minSize);
        
        if ($maxSize !== null) {
            $query->where('size', '<=', $maxSize);
        }
        
        return $query;
    }

    /**
     * سکوپ برای فیلتر فایل‌های بزرگ
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $threshold (bytes)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLarge($query, $threshold = 1048576) // 1MB
    {
        return $query->where('size', '>', $threshold);
    }

    /**
     * سکوپ برای فیلتر فایل‌های کوچک
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $threshold (bytes)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSmall($query, $threshold = 1048576) // 1MB
    {
        return $query->where('size', '<', $threshold);
    }

    /**
     * سکوپ برای فیلتر فایل‌های قدیمی (قبل از تاریخ مشخص)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOlderThan($query, $date)
    {
        return $query->where('uploaded_at', '<', $date);
    }

    /**
     * سکوپ برای فیلتر فایل‌های جدید (بعد از تاریخ مشخص)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewerThan($query, $date)
    {
        return $query->where('uploaded_at', '>', $date);
    }

    /**
     * سکوپ برای جستجوی فایل‌ها بر اساس مسیر یا هش
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('path', 'LIKE', "%{$search}%")
                     ->orWhere('hash', 'LIKE', "%{$search}%");
    }

    // ========== متدهای کمکی (Helper Methods) ==========

    /**
     * دریافت نام فایل (آخرین بخش مسیر)
     * @return string
     */
    public function getFileNameAttribute()
    {
        return basename($this->path);
    }

    /**
     * دریافت نام فایل بدون پسوند
     * @return string
     */
    public function getFileNameWithoutExtensionAttribute()
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * دریافت پسوند فایل
     * @return string
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * دریافت نوع MIME به صورت متنی
     * @return string
     */
    public function getMimeLabelAttribute()
    {
        return self::$mimeTypes[$this->mime] ?? $this->mime ?? 'Unknown';
    }

    /**
     * دریافت حجم فایل به صورت فرمت شده
     * @return string
     */
    public function getFormattedSizeAttribute()
    {
        return $this->formatSize($this->size);
    }

    /**
     * دریافت URL فایل
     * @return string|null
     */
    public function getUrlAttribute()
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->url($this->path);
        }
        return null;
    }

    /**
     * دریافت مسیر کامل فایل
     * @return string|null
     */
    public function getFullPathAttribute()
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->path($this->path);
        }
        return null;
    }

    /**
     * دریافت محتویات فایل
     * @return string|null
     */
    public function getContentsAttribute()
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->get($this->path);
        }
        return null;
    }

    /**
     * دریافت اطلاعات کامل فایل برای گزارش
     * @return array
     */
    public function getInfoAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->file_name,
            'extension' => $this->extension,
            'size' => $this->formatted_size,
            'mime' => $this->mime_label,
            'disk' => $this->disk,
            'path' => $this->path,
            'hash' => $this->hash,
            'url' => $this->url,
            'uploaded_at' => $this->uploaded_at ? $this->uploaded_at->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * فرمت کردن حجم فایل
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatSize($bytes, $precision = 2)
    {
        if ($bytes == 0) {
            return '0 Bytes';
        }

        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = floor(log($bytes, 1024));
        $size = $bytes / pow(1024, $index);

        return round($size, $precision) . ' ' . $units[$index];
    }

    /**
     * بررسی وجود فایل در دیسک
     * @return bool
     */
    public function exists()
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    /**
     * دانلود فایل
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download()
    {
        if ($this->exists()) {
            return Storage::disk($this->disk)->download($this->path, $this->file_name);
        }
        
        abort(404, 'File not found.');
    }

    /**
     * حذف فایل از دیسک
     * @return bool
     */
    public function deleteFile()
    {
        if ($this->exists()) {
            Storage::disk($this->disk)->delete($this->path);
        }
        
        return $this->delete();
    }

    /**
     * دریافت هش فایل
     * @param string $algorithm
     * @return string|null
     */
    public function getFileHash($algorithm = 'sha256')
    {
        if ($this->exists()) {
            return hash_file($algorithm, $this->full_path);
        }
        return null;
    }

    /**
     * بررسی تکراری بودن فایل بر اساس هش
     * @return bool
     */
    public function isDuplicate()
    {
        return self::where('hash', $this->hash)
                   ->where('id', '!=', $this->id)
                   ->exists();
    }

    /**
     * دریافت فایل‌های تکراری
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDuplicates()
    {
        return self::where('hash', $this->hash)
                   ->where('id', '!=', $this->id)
                   ->get();
    }

    /**
     * آپلود فایل جدید
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $disk
     * @param string $path
     * @return self
     */
    public static function upload($file, $disk = 'public', $path = 'uploads')
    {
        // محاسبه هش فایل
        $hash = hash_file('sha256', $file->getRealPath());
        
        // بررسی وجود فایل تکراری
        $existing = self::where('hash', $hash)->first();
        if ($existing) {
            return $existing;
        }

        // ذخیره فایل
        $storedPath = $file->store($path, $disk);
        
        // ایجاد رکورد در دیتابیس
        return self::create([
            'disk' => $disk,
            'path' => $storedPath,
            'hash' => $hash,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'uploaded_at' => Carbon::now(),
        ]);
    }

    /**
     * آپلود فایل از محتوا
     * @param string $content
     * @param string $filename
     * @param string $disk
     * @param string $path
     * @return self
     */
    public static function uploadFromContent($content, $filename, $disk = 'public', $path = 'uploads')
    {
        // محاسبه هش محتوا
        $hash = hash('sha256', $content);
        
        // بررسی وجود فایل تکراری
        $existing = self::where('hash', $hash)->first();
        if ($existing) {
            return $existing;
        }

        // ذخیره فایل
        $storedPath = $path . '/' . $filename;
        Storage::disk($disk)->put($storedPath, $content);
        
        // ایجاد رکورد در دیتابیس
        return self::create([
            'disk' => $disk,
            'path' => $storedPath,
            'hash' => $hash,
            'size' => strlen($content),
            'mime' => Storage::disk($disk)->mimeType($storedPath),
            'uploaded_at' => Carbon::now(),
        ]);
    }

    /**
     * آپلود فایل از URL
     * @param string $url
     * @param string $disk
     * @param string $path
     * @return self
     */
    public static function uploadFromUrl($url, $disk = 'public', $path = 'uploads')
    {
        $content = file_get_contents($url);
        if ($content === false) {
            throw new \Exception('Unable to download file from URL.');
        }

        $filename = basename(parse_url($url, PHP_URL_PATH));
        if (empty($filename)) {
            $filename = 'file_' . time();
        }

        return self::uploadFromContent($content, $filename, $disk, $path);
    }

    /**
     * دریافت فایل‌های یک مدل خاص
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getForModel($model)
    {
        return self::where('fileable_type', get_class($model))
                   ->where('fileable_id', $model->id)
                   ->get();
    }

    /**
     * اتصال فایل به یک مدل
     * @param Model $model
     * @param string $relation
     * @return bool
     */
    public function attachTo($model, $relation = null)
    {
        if ($relation) {
            return $model->{$relation}()->save($this);
        }
        
        $this->fileable_type = get_class($model);
        $this->fileable_id = $model->id;
        return $this->save();
    }

    /**
     * دریافت آمار فایل‌ها
     * @return array
     */
    public static function getStatistics()
    {
        $total = self::count();
        $totalSize = self::sum('size');
        $averageSize = $total > 0 ? $totalSize / $total : 0;
        
        $byDisk = self::select('disk')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(size) as total_size')
            ->groupBy('disk')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->disk => [
                    'count' => $item->count,
                    'total_size' => $item->total_size,
                    'formatted_size' => self::formatSize($item->total_size),
                ]];
            })
            ->toArray();
        
        $byMime = self::select('mime')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(size) as total_size')
            ->whereNotNull('mime')
            ->groupBy('mime')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->mapWithKeys(function ($item) {
                $label = self::$mimeTypes[$item->mime] ?? $item->mime;
                return [$item->mime => [
                    'label' => $label,
                    'count' => $item->count,
                    'total_size' => $item->total_size,
                    'formatted_size' => self::formatSize($item->total_size),
                ]];
            })
            ->toArray();
        
        $images = self::images()->count();
        $documents = self::documents()->count();
        $large = self::large()->count();
        $small = self::small()->count();
        
        return [
            'total_files' => $total,
            'total_size' => $totalSize,
            'formatted_total_size' => self::formatSize($totalSize),
            'average_size' => self::formatSize($averageSize),
            'images' => $images,
            'documents' => $documents,
            'large_files' => $large,
            'small_files' => $small,
            'by_disk' => $byDisk,
            'by_mime' => $byMime,
        ];
    }

    /**
     * پاکسازی فایل‌های قدیمی و بدون استفاده
     * @param int $days
     * @return int
     */
    public static function cleanupOldFiles($days = 30)
    {
        $date = Carbon::now()->subDays($days);
        
        $files = self::where('uploaded_at', '<', $date)
            ->whereDoesntHave('fileable')
            ->get();
        
        $count = 0;
        foreach ($files as $file) {
            if ($file->deleteFile()) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * دریافت فایل‌های بدون استفاده (orphaned)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOrphanedFiles()
    {
        return self::whereDoesntHave('fileable')->get();
    }
}