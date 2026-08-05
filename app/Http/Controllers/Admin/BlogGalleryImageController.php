<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PublicUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogGalleryImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['file', 'mimes:jpeg,jpg,png,gif,webp', 'max:20480'],
        ]);

        $uploaded = [];

        foreach ($request->file('images', []) as $file) {
            $path = PublicUploads::storeImage($file, 'images');
            $uploaded[] = [
                'path' => $path,
                'url' => PublicUploads::url($path),
            ];
        }

        return response()->json(['images' => $uploaded]);
    }

    public static function resolveStoredPath(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (! PublicUploads::isValidImagePath($path)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => 'Érvénytelen kép elérési út.',
            ]);
        }

        if (! Storage::disk('public')->exists($path)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => 'A feltöltött kép fájl nem található. Töltse fel újra.',
            ]);
        }

        return $path;
    }
}
