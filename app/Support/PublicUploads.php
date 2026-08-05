<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PublicUploads
{
    public const IMAGE_DIR = 'products';

    public static function storeImage(UploadedFile $file, string $field = 'image'): string
    {
        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                $field => self::uploadErrorMessage($file->getError()),
            ]);
        }

        $optimized = ImageOptimizer::tryOptimizeToJpeg($file);

        if ($optimized !== null) {
            $path = self::IMAGE_DIR.'/'.Str::random(40).'.jpg';

            if (! Storage::disk('public')->put($path, $optimized)) {
                throw ValidationException::withMessages([
                    $field => 'A kép mentése sikertelen. Ellenőrizze a storage mappa írási jogosultságát.',
                ]);
            }

            return $path;
        }

        $path = $file->store(self::IMAGE_DIR, 'public');

        if ($path === false) {
            throw ValidationException::withMessages([
                $field => 'A kép mentése sikertelen. Ellenőrizze a storage mappa írási jogosultságát.',
            ]);
        }

        return $path;
    }

    public static function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'A kép túl nagy a szerver számára. Próbáljon kisebb fájlt, vagy frissítse az oldalt és töltse fel újra.',
            UPLOAD_ERR_PARTIAL => 'A kép feltöltése megszakadt. Próbálja újra.',
            UPLOAD_ERR_NO_FILE => 'Nem érkezett kép a szerverre.',
            default => 'A kép feltöltése sikertelen. Próbálja újra.',
        };
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
