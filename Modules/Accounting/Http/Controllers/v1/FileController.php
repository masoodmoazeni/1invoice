<?php

namespace Modules\Accounting\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Log;
use Modules\Accounting\Services\FileService;
use Modules\Accounting\Http\Requests\File\FileRequest;
use Modules\Accounting\Http\Requests\File\FileUpdateRequest;

class FileController extends BaseController
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * @OA\Get(
     *     path="/file",
     *     summary="نمایش لیست فایل‌ها",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="شماره صفحه",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="تعداد آیتم در هر صفحه",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="جستجو بر اساس مسیر یا هش فایل",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="disk",
     *         in="query",
     *         description="دیسک ذخیره‌سازی",
     *         required=false,
     *         @OA\Schema(type="string", enum={"local", "public", "s3", "ftp", "sftp"})
     *     ),
     *     @OA\Parameter(
     *         name="mime",
     *         in="query",
     *         description="نوع MIME فایل",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="extension",
     *         in="query",
     *         description="پسوند فایل",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_size",
     *         in="query",
     *         description="حداقل حجم (بایت)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="max_size",
     *         in="query",
     *         description="حداکثر حجم (بایت)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="نوع فایل",
     *         required=false,
     *         @OA\Schema(type="string", enum={"image", "document", "all"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="لیست فایل‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت لیست فایل‌ها"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only([
                'search',
                'disk',
                'mime',
                'extension',
                'min_size',
                'max_size',
                'type'
            ]);

            // حذف فیلترهای خالی
            $filters = array_filter($filters, function($value) {
                return !is_null($value) && $value !== '';
            });

            $files = $this->fileService->getPaginate($perPage, $filters);

            return $this->successResponse($files, 'لیست فایل‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in index: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست فایل‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/file",
     *     summary="آپلود فایل جدید",
     *     tags={"File"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"file"},
     *             @OA\Property(
     *                 property="file",
     *                 type="string",
     *                 format="binary",
     *                 description="فایل برای آپلود"
     *             ),
     *             @OA\Property(
     *                 property="disk",
     *                 type="string",
     *                 enum={"local", "public", "s3", "ftp", "sftp"},
     *                 default="public",
     *                 description="دیسک ذخیره‌سازی"
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 default="uploads",
     *                 description="مسیر ذخیره‌سازی"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="فایل با موفقیت آپلود شد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="فایل تکراری وجود دارد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در آپلود فایل"
     *     )
     * )
     */
    public function store(FileRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // آپلود فایل
            $file = $request->file('file');
            $disk = $validatedData['disk'] ?? 'public';
            $path = $validatedData['path'] ?? 'uploads';
            
            $uploadedFile = $this->fileService->upload($file, $disk, $path);

            return $this->successResponse($uploadedFile, 'فایل با موفقیت آپلود شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in store: ' . $ex->getMessage());
            return $this->errorResponse('خطا در آپلود فایل: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/file/from-url",
     *     summary="آپلود فایل از URL",
     *     tags={"File"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 format="url",
     *                 example="https://example.com/file.pdf",
     *                 description="آدرس URL فایل"
     *             ),
     *             @OA\Property(
     *                 property="disk",
     *                 type="string",
     *                 enum={"local", "public", "s3", "ftp", "sftp"},
     *                 default="public",
     *                 description="دیسک ذخیره‌سازی"
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 default="uploads",
     *                 description="مسیر ذخیره‌سازی"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="فایل با موفقیت آپلود شد"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="فایل تکراری وجود دارد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در آپلود فایل"
     *     )
     * )
     */
    public function uploadFromUrl(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'url' => 'required|url',
                'disk' => 'nullable|string|in:' . implode(',', \Modules\Accounting\Entities\File::$supportedDisks),
                'path' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $uploadedFile = $this->fileService->uploadFromUrl(
                $request->url,
                $request->disk ?? 'public',
                $request->path ?? 'uploads'
            );

            return $this->successResponse($uploadedFile, 'فایل با موفقیت از URL آپلود شد', 201);
        } catch (\Exception $ex) {
            Log::error('Error in uploadFromUrl: ' . $ex->getMessage());
            return $this->errorResponse('خطا در آپلود فایل از URL: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/file/{id}",
     *     summary="نمایش اطلاعات یک فایل",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه فایل",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات فایل با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="فایل یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت اطلاعات فایل"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $file = $this->fileService->find($id);
            
            if (!$file) {
                return $this->errorResponse('فایل مورد نظر یافت نشد', 404);
            }

            // دریافت اطلاعات تکمیلی
            $details = $this->fileService->getDetails($id);

            $data = [
                'file' => $file,
                'details' => $details
            ];

            return $this->successResponse($data, 'اطلاعات فایل با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in show: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت اطلاعات فایل: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/file/{id}",
     *     summary="به‌روزرسانی اطلاعات فایل",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه فایل",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="disk",
     *                 type="string",
     *                 enum={"local", "public", "s3", "ftp", "sftp"},
     *                 description="دیسک ذخیره‌سازی"
     *             ),
     *             @OA\Property(
     *                 property="path",
     *                 type="string",
     *                 description="مسیر جدید فایل"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="اطلاعات فایل با موفقیت به‌روزرسانی شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="فایل یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطا در اعتبارسنجی"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در به‌روزرسانی اطلاعات فایل"
     *     )
     * )
     */
    public function update(FileUpdateRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $file = $this->fileService->update($id, $validatedData);

            return $this->successResponse($file, 'اطلاعات فایل با موفقیت به‌روزرسانی شد');
        } catch (\Exception $ex) {
            Log::error('Error in update: ' . $ex->getMessage());
            return $this->errorResponse('خطا در به‌روزرسانی اطلاعات فایل: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/file/{id}",
     *     summary="حذف فایل",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه فایل",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="فایل با موفقیت حذف شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="فایل یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در حذف فایل"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->fileService->delete($id);

            return $this->successResponse(null, 'فایل با موفقیت حذف شد');
        } catch (\Exception $ex) {
            Log::error('Error in destroy: ' . $ex->getMessage());
            return $this->errorResponse('خطا در حذف فایل: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/file/{id}/download",
     *     summary="دانلود فایل",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="شناسه فایل",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="فایل با موفقیت دانلود شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="فایل یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دانلود فایل"
     *     )
     * )
     */
    public function download($id)
    {
        try {
            $file = $this->fileService->find($id);
            
            if (!$file) {
                return $this->errorResponse('فایل مورد نظر یافت نشد', 404);
            }

            return $this->fileService->download($id);
        } catch (\Exception $ex) {
            Log::error('Error in download: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دانلود فایل: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/file/statistics",
     *     summary="دریافت آمار فایل‌ها",
     *     tags={"File"},
     *     @OA\Response(
     *         response=200,
     *         description="آمار فایل‌ها با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت آمار فایل‌ها"
     *     )
     * )
     */
    public function statistics()
    {
        try {
            $statistics = $this->fileService->getStatistics();
            return $this->successResponse($statistics, 'آمار فایل‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in statistics: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت آمار فایل‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/file/duplicates",
     *     summary="دریافت فایل‌های تکراری",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="hash",
     *         in="query",
     *         description="هش فایل برای بررسی تکراری‌ها",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="فایل‌های تکراری با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="فایلی با این هش یافت نشد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت فایل‌های تکراری"
     *     )
     * )
     */
    public function getDuplicates(Request $request)
    {
        try {
            $validator = validator($request->all(), [
                'hash' => 'required|string|min:32',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $duplicates = $this->fileService->getDuplicates($request->hash);

            if ($duplicates->isEmpty()) {
                return $this->errorResponse('فایل تکراری برای این هش یافت نشد', 404);
            }

            return $this->successResponse($duplicates, 'فایل‌های تکراری با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDuplicates: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت فایل‌های تکراری: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/file/orphaned",
     *     summary="دریافت فایل‌های بدون استفاده (orphaned)",
     *     tags={"File"},
     *     @OA\Response(
     *         response=200,
     *         description="فایل‌های بدون استفاده با موفقیت دریافت شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در دریافت فایل‌های بدون استفاده"
     *     )
     * )
     */
    public function getOrphaned()
    {
        try {
            $orphaned = $this->fileService->getOrphanedFiles();
            return $this->successResponse($orphaned, 'فایل‌های بدون استفاده با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getOrphaned: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت فایل‌های بدون استفاده: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/file/cleanup",
     *     summary="پاکسازی فایل‌های قدیمی و بدون استفاده",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="days",
     *         in="query",
     *         description="تعداد روزهای گذشته",
     *         required=false,
     *         @OA\Schema(type="integer", default=30)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="پاکسازی فایل‌ها با موفقیت انجام شد"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطا در پاکسازی فایل‌ها"
     *     )
     * )
     */
    public function cleanup(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $deleted = $this->fileService->cleanupOldFiles($days);

            return $this->successResponse(
                ['deleted_count' => $deleted], 
                "{$deleted} فایل قدیمی با موفقیت پاکسازی شد"
            );
        } catch (\Exception $ex) {
            Log::error('Error in cleanup: ' . $ex->getMessage());
            return $this->errorResponse('خطا در پاکسازی فایل‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/file/disks",
     *     summary="دریافت لیست دیسک‌های پشتیبانی شده",
     *     tags={"File"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست دیسک‌ها با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getDisks()
    {
        try {
            $disks = \Modules\Accounting\Entities\File::$supportedDisks;
            return $this->successResponse($disks, 'لیست دیسک‌ها با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getDisks: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست دیسک‌ها: ' . $ex->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/file/mime-types",
     *     summary="دریافت لیست انواع MIME پشتیبانی شده",
     *     tags={"File"},
     *     @OA\Response(
     *         response=200,
     *         description="لیست انواع MIME با موفقیت دریافت شد"
     *     )
     * )
     */
    public function getMimeTypes()
    {
        try {
            $mimeTypes = \Modules\Accounting\Entities\File::$mimeTypes;
            return $this->successResponse($mimeTypes, 'لیست انواع MIME با موفقیت دریافت شد');
        } catch (\Exception $ex) {
            Log::error('Error in getMimeTypes: ' . $ex->getMessage());
            return $this->errorResponse('خطا در دریافت لیست انواع MIME: ' . $ex->getMessage(), 500);
        }
    }
}