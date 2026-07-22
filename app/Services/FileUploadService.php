<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public const DEFAULT_IMAGE_MAX_BYTES = 30 * 1024 * 1024;  // 30MB

    public const DEFAULT_DOCUMENT_MAX_BYTES = 500 * 1024 * 1024;  // 500MB

    /** @var array<string, int> */
    public const WEBP_IMAGE_MIME_TO_TYPE = [
        'image/jpeg' => IMAGETYPE_JPEG,
        'image/png' => IMAGETYPE_PNG,
        'image/webp' => IMAGETYPE_WEBP,
    ];

    /**
     * Store an image under public/, converting to WebP when GD supports it.
     *
     * @param  array{
     *     max_bytes?: int,
     *     webp_quality?: int,
     *     filename_fallback?: string,
     * }  $options
     * @return array{filename: string, url: string, relative_path: string}
     */
    public function uploadImageAsWebp(UploadedFile $file, string $publicRelativeDirectory, array $options = []): array
    {
        if (!$file->isValid()) {
            throw new \Exception('Invalid uploaded file');
        }

        $maxBytes = (int) ($options['max_bytes'] ?? self::DEFAULT_IMAGE_MAX_BYTES);
        if ($file->getSize() > $maxBytes) {
            throw new \Exception('File size must be less than ' . (int) ($maxBytes / 1024 / 1024) . 'MB');
        }

        $mime = $file->getMimeType();
        if (!isset(self::WEBP_IMAGE_MIME_TO_TYPE[$mime])) {
            throw new \Exception('Only JPG, PNG or WebP images are allowed');
        }

        $imgType = self::WEBP_IMAGE_MIME_TO_TYPE[$mime];
        $relativeDir = $this->normalizePublicRelativePath($publicRelativeDirectory);
        $absoluteDir = public_path($relativeDir);
        $this->ensureDirectory($absoluteDir);

        $filename = $this->makeWebpFilename($file, $options);
        $fullPath = $absoluteDir . DIRECTORY_SEPARATOR . $filename;

        $webpQuality = (int) ($options['webp_quality'] ?? 80);

        switch ($imgType) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($file->getRealPath());
                break;

            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($file->getRealPath());
                if ($src) {
                    imagepalettetotruecolor($src);
                    imagealphablending($src, true);
                    imagesavealpha($src, true);
                }
                break;

            case IMAGETYPE_WEBP:
                if (!function_exists('imagecreatefromwebp')) {
                    copy($file->getRealPath(), $fullPath);

                    return $this->buildPublicFileResult($relativeDir, $filename);
                }
                $src = imagecreatefromwebp($file->getRealPath());
                break;

            default:
                throw new \Exception('Unsupported image type');
        }

        if (!$src || !function_exists('imagewebp')) {
            throw new \Exception('Server does not support WebP processing');
        }

        $src = $this->applyExifOrientation($src, $file->getRealPath());

        imagewebp($src, $fullPath, $webpQuality);
        imagedestroy($src);

        return $this->buildPublicFileResult($relativeDir, $filename);
    }

    /**
     * Store a non-image file under public/ with MIME validation.
     *
     * @param  array<string>  $allowedMimes
     * @param  array{
     *     max_bytes?: int,
     *     mkdir_mode?: int,
     *     forbidden_mime_message?: string,
     * }  $options
     * @return array{filename: string, url: string, relative_path: string}
     */
    public function uploadPublicDocument(UploadedFile $file, string $publicRelativeDirectory, array $allowedMimes, array $options = []): array
    {
        if (!$file->isValid()) {
            throw new \Exception('Invalid uploaded file');
        }

        $tmpPath = $file->getRealPath();
        if (!$tmpPath || !file_exists($tmpPath)) {
            throw new \Exception('Uploaded file not found.');
        }

        $maxBytes = (int) ($options['max_bytes'] ?? self::DEFAULT_DOCUMENT_MAX_BYTES);
        if (filesize($tmpPath) > $maxBytes) {
            throw new \Exception('File size must not exceed ' . (int) ($maxBytes / 1024 / 1024) . 'MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes, true)) {
            $template = $options['forbidden_mime_message'] ?? 'File type is not allowed. (MIME: %s)';
            throw new \Exception(sprintf($template, $mime));
        }

        $relativeDir = $this->normalizePublicRelativePath($publicRelativeDirectory);
        $absoluteDir = public_path($relativeDir);
        $mkdirMode = (int) ($options['mkdir_mode'] ?? 0755);
        $this->ensureDirectory($absoluteDir, $mkdirMode);

        $extension = pathinfo(basename($file->getClientOriginalName()), PATHINFO_EXTENSION);
        $extension = $extension !== '' ? strtolower($extension) : $this->guessExtensionFromMime($mime);
        $filename = now()->format('Ymd-His') . ($extension !== '' ? '.' . $extension : '');

        $file->move($absoluteDir, $filename);

        return $this->buildPublicFileResult($relativeDir, $filename);
    }

    private function normalizePublicRelativePath(string $publicRelativeDirectory): string
    {
        $trimmed = trim(str_replace('\\', '/', $publicRelativeDirectory), '/');

        return $trimmed;
    }

    private function ensureDirectory(string $absolutePath, int $mode = 0755): void
    {
        if (!is_dir($absolutePath)) {
            mkdir($absolutePath, $mode, true);
        }
    }

    /**
     * @param  array{filename_fallback?: string}  $options
     */
    private function makeWebpFilename(UploadedFile $file, array $options): string
    {
        $originalBase = pathinfo(basename($file->getClientOriginalName()), PATHINFO_FILENAME);
        $base = $this->sanitizeFilenameBase($originalBase);

        if ($base === '') {
            $base = $this->sanitizeFilenameBase((string) ($options['filename_fallback'] ?? $options['slug_fallback'] ?? 'image'));
        }

        if ($base === '') {
            $base = 'image';
        }

        return $base . '_' . now()->format('Ymd-His-u') . '.webp';
    }

    private function sanitizeFilenameBase(string $name): string
    {
        $name = preg_replace('/[\/\\\\:\*\?"<>\|\x00,\s]/', '_', trim($name)) ?? '';
        $name = preg_replace('/_+/', '_', $name) ?? '';

        return trim($name, "._ \t\n\r\0\x0B");
    }

    /**
     * @return array{filename: string, url: string, relative_path: string}
     */
    private function buildPublicFileResult(string $relativeDir, string $filename): array
    {
        $relativePath = $relativeDir . '/' . $filename;

        return [
            'filename' => $filename,
            'relative_path' => $relativePath,
            'url' => asset($relativePath),
        ];
    }

    private function guessExtensionFromMime(string $mime): string
    {
        return match ($mime) {
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            default => '',
        };
    }

    /**
     * Bake EXIF orientation into pixel data so WebP output displays correctly.
     *
     * @param  \GdImage|resource  $image
     * @return \GdImage|resource
     */
    private function applyExifOrientation($image, ?string $sourcePath)
    {
        if (!$sourcePath || !is_file($sourcePath)) {
            return $image;
        }

        $orientation = $this->readExifOrientation($sourcePath);
        if ($orientation === null || $orientation === 1) {
            return $image;
        }

        if (!$this->shouldApplyExifOrientation($image, $orientation)) {
            return $image;
        }

        switch ($orientation) {
            case 2:
                imageflip($image, IMG_FLIP_HORIZONTAL);

                return $image;

            case 3:
                return $this->rotateGdImage($image, 180);

            case 4:
                imageflip($image, IMG_FLIP_VERTICAL);

                return $image;

            case 5:
                $image = $this->rotateGdImage($image, -90);
                imageflip($image, IMG_FLIP_HORIZONTAL);

                return $image;

            case 6:
                return $this->rotateGdImage($image, -90);

            case 7:
                $image = $this->rotateGdImage($image, 90);
                imageflip($image, IMG_FLIP_HORIZONTAL);

                return $image;

            case 8:
                return $this->rotateGdImage($image, 90);

            default:
                return $image;
        }
    }

    /**
     * Skip 90-degree EXIF corrections when pixels already look display-oriented.
     * Browsers and mobile OSes sometimes bake rotation into pixels but leave a stale tag.
     *
     * @param  \GdImage|resource  $image
     */
    private function shouldApplyExifOrientation($image, int $orientation): bool
    {
        if (!in_array($orientation, [5, 6, 7, 8], true)) {
            return true;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($width === $height) {
            return true;
        }

        if ($height > $width) {
            return false;
        }

        return true;
    }

    private function readExifOrientation(string $sourcePath): ?int
    {
        if (!function_exists('exif_read_data')) {
            return null;
        }

        $exif = @exif_read_data($sourcePath);
        if ($exif === false || !isset($exif['Orientation'])) {
            return null;
        }

        $orientation = (int) $exif['Orientation'];

        return ($orientation >= 1 && $orientation <= 8) ? $orientation : null;
    }

    /**
     * @param  \GdImage|resource  $image
     * @return \GdImage|resource
     */
    private function rotateGdImage($image, float $angle)
    {
        $background = imagecolorallocatealpha($image, 0, 0, 0, 127);
        $rotated = imagerotate($image, $angle, $background);

        if ($rotated === false) {
            return $image;
        }

        imagealphablending($rotated, false);
        imagesavealpha($rotated, true);
        imagedestroy($image);

        return $rotated;
    }
}
