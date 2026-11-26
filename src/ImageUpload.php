<?php

declare(strict_types=1);

namespace App;

use GdImage;
use RuntimeException;

final class ImageUpload
{
    public const MAX_FILE_BYTES = 8 * 1024 * 1024;
    public const MAX_BATCH_BYTES = 40 * 1024 * 1024;
    public const MAX_BATCH_FILES = 10;
    public const MAX_PER_CAR = 20;
    public const MIN_SIDE = 400;
    public const MAX_PIXELS = 40_000_000;
    public const SIZES = [1600 => '', 640 => '-640'];

    private const ALLOWED = [
        'image/jpeg' => IMAGETYPE_JPEG,
        'image/png' => IMAGETYPE_PNG,
        'image/webp' => IMAGETYPE_WEBP,
    ];

    public static function normalizeFiles(array $files): array
    {
        if (!isset($files['name']) || !is_array($files['name'])) {
            return isset($files['tmp_name']) ? [$files] : [];
        }
        $out = [];
        foreach ($files['name'] as $i => $name) {
            $out[] = [
                'name' => $name,
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$i] ?? 0,
            ];
        }

        return $out;
    }

    public static function validateBatch(array $files): ?string
    {
        if (count($files) === 0) {
            return 'Kies minimaal één foto.';
        }
        if (count($files) > self::MAX_BATCH_FILES) {
            return 'Upload maximaal ' . self::MAX_BATCH_FILES . ' foto’s tegelijk.';
        }
        $total = array_sum(array_map(static fn ($f) => (int) $f['size'], $files));
        if ($total > self::MAX_BATCH_BYTES) {
            return 'Deze foto’s zijn samen groter dan 40 MB. Upload ze in kleinere groepen.';
        }

        return null;
    }

    public static function store(array $file, string $targetDir): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException(match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'is groter dan 8 MB',
                UPLOAD_ERR_NO_FILE => 'is niet ontvangen',
                default => 'kon niet worden geüpload',
            });
        }
        $tmp = (string) $file['tmp_name'];
        if (!is_uploaded_file($tmp)) {
            throw new RuntimeException('is geen geldige upload');
        }
        $size = filesize($tmp);
        if ($size === false || $size === 0 || $size > self::MAX_FILE_BYTES) {
            throw new RuntimeException('is groter dan 8 MB of leeg');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
        if (!isset(self::ALLOWED[$mime])) {
            throw new RuntimeException('is geen JPG, PNG of WebP');
        }
        $info = @getimagesize($tmp);
        if ($info === false || $info[2] !== self::ALLOWED[$mime]) {
            throw new RuntimeException('is geen geldige afbeelding');
        }
        [$width, $height] = $info;
        if ($width < self::MIN_SIDE || $height < self::MIN_SIDE) {
            throw new RuntimeException('is te klein (minimaal ' . self::MIN_SIDE . ' pixels)');
        }
        if ($width * $height > self::MAX_PIXELS) {
            throw new RuntimeException('heeft te veel pixels (maximaal 40 megapixel)');
        }

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png' => @imagecreatefrompng($tmp),
            'image/webp' => @imagecreatefromwebp($tmp),
        };
        if (!$image instanceof GdImage) {
            throw new RuntimeException('kon niet worden gelezen');
        }
        if ($mime === 'image/jpeg') {
            $image = self::applyOrientation($image, $tmp);
        }
        $image = self::cropTo43($image);

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException('kon niet worden opgeslagen');
        }
        $name = bin2hex(random_bytes(16));
        $written = [];
        try {
            foreach (self::SIZES as $targetWidth => $suffix) {
                $resized = self::resize($image, $targetWidth);
                $path = $targetDir . '/' . $name . $suffix . '.webp';
                if (!imagewebp($resized, $path, $targetWidth > 1000 ? 82 : 78)) {
                    throw new RuntimeException('kon niet worden opgeslagen');
                }
                $written[] = $path;
                if ($resized !== $image) {
                    imagedestroy($resized);
                }
            }
        } catch (RuntimeException $e) {
            array_map('unlink', array_filter($written, 'is_file'));
            throw $e;
        }
        $w = min(1600, imagesx($image));
        $h = (int) round($w * 3 / 4);
        imagedestroy($image);

        return ['filename' => $name . '.webp', 'width' => $w, 'height' => $h];
    }

    public static function delete(string $dir, string $filename): void
    {
        if (is_demo()) {
            return;
        }
        if (!preg_match('/^([a-f0-9]{32})\.webp$/', $filename, $m)) {
            return;
        }
        foreach (self::SIZES as $suffix) {
            $path = $dir . '/' . $m[1] . $suffix . '.webp';
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private static function applyOrientation(GdImage $image, string $path): GdImage
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }
        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
        if ($rotated instanceof GdImage && $rotated !== $image) {
            imagedestroy($image);
            return $rotated;
        }

        return $image;
    }

    private static function cropTo43(GdImage $image): GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $targetH = (int) round($w * 3 / 4);
        if (abs($targetH - $h) <= 2) {
            return $image;
        }
        if ($targetH < $h) {
            $rect = ['x' => 0, 'y' => intdiv($h - $targetH, 2), 'width' => $w, 'height' => $targetH];
        } else {
            $targetW = (int) round($h * 4 / 3);
            $rect = ['x' => intdiv($w - $targetW, 2), 'y' => 0, 'width' => $targetW, 'height' => $h];
        }
        $cropped = imagecrop($image, $rect);
        if (!$cropped instanceof GdImage) {
            return $image;
        }
        imagedestroy($image);

        return $cropped;
    }

    private static function resize(GdImage $image, int $targetWidth): GdImage
    {
        $w = imagesx($image);
        if ($w <= $targetWidth) {
            $copy = imagecreatetruecolor($w, imagesy($image));
            imagecopy($copy, $image, 0, 0, 0, 0, $w, imagesy($image));

            return $copy;
        }
        $targetHeight = (int) round(imagesy($image) * $targetWidth / $w);
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $w, imagesy($image));

        return $resized;
    }
}
