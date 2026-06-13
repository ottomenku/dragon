<?php

namespace App\Support;

class PublicUploads
{
    public const IMAGE_DIR = 'products';

    public static function storeImage($file): string
    {
        return $file->store(self::IMAGE_DIR, 'public');
    }

    public static function url(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        return '/storage/'.ltrim($path, '/');
    }

    public static function isValidImagePath(?string $path): bool
    {
        if (! filled($path)) {
            return false;
        }

        return str_starts_with($path, self::IMAGE_DIR.'/')
            || str_starts_with($path, 'blog-gallery/');
    }
}
