<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class ImageOptimizer
{
    private const MAX_DIMENSION = 1600;

    private const TARGET_BYTES = 900_000;

    private const JPEG_QUALITY_START = 85;

    private const JPEG_QUALITY_MIN = 60;

    public static function tryOptimizeToJpeg(UploadedFile $file): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $source = self::loadImage($file);
        if ($source === null) {
            return null;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        [$newWidth, $newHeight] = self::scaledDimensions($width, $height);

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        if ($canvas === false) {
            imagedestroy($source);

            return null;
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);

        $quality = self::JPEG_QUALITY_START;
        $binary = null;

        while ($quality >= self::JPEG_QUALITY_MIN) {
            ob_start();
            imagejpeg($canvas, null, $quality);
            $binary = ob_get_clean();

            if ($binary === false) {
                break;
            }

            if (strlen($binary) <= self::TARGET_BYTES) {
                break;
            }

            $quality -= 5;
        }

        imagedestroy($canvas);

        return is_string($binary) && $binary !== '' ? $binary : null;
    }

    private static function loadImage(UploadedFile $file): ?\GdImage
    {
        $path = $file->getRealPath();
        if ($path === false) {
            return null;
        }

        $image = match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        return $image instanceof \GdImage ? $image : null;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private static function scaledDimensions(int $width, int $height): array
    {
        $max = max($width, $height);
        if ($max <= self::MAX_DIMENSION) {
            return [$width, $height];
        }

        $ratio = self::MAX_DIMENSION / $max;

        return [(int) round($width * $ratio), (int) round($height * $ratio)];
    }
}
