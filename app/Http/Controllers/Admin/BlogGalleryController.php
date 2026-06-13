<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogGallery;
use App\Models\BlogGalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BlogGalleryController extends Controller
{
    public function edit()
    {
        $gallery = BlogGallery::current();
        $gallery->load('images');

        return view('admin.blog-gallery.edit', [
            'gallery' => $gallery,
            'initialImages' => $this->initialImagesForEditor($gallery),
        ]);
    }

    public function update(Request $request)
    {
        $gallery = BlogGallery::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'intro' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'display_mode' => ['required', Rule::in([BlogGallery::DISPLAY_SLIDER, BlogGallery::DISPLAY_LIST])],
            'images' => ['nullable', 'array'],
            'images.*.id' => ['nullable', 'integer'],
            'images.*.stored_image_path' => ['nullable', 'string', 'max:500'],
            'images.*.title' => ['nullable', 'string', 'max:255'],
            'images.*.description' => ['nullable', 'string'],
            'images.*.sort_order' => ['required', 'integer'],
        ]);

        DB::transaction(function () use ($gallery, $validated, $request) {
            $gallery->update([
                'name' => $validated['name'],
                'intro' => $validated['intro'] ?? '',
                'description' => $validated['description'] ?? null,
                'display_mode' => $validated['display_mode'],
            ]);

            $this->syncImages($gallery, $request->input('images', []));
        });

        return redirect()
            ->route('admin.blog-gallery.edit')
            ->with('success', 'Galéria mentve.');
    }

    private function syncImages(BlogGallery $gallery, array $imagesInput): void
    {
        $submittedIds = collect($imagesInput)
            ->pluck('id')
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        $imagesToDelete = $gallery->images()
            ->when($submittedIds !== [], fn ($query) => $query->whereNotIn('id', $submittedIds))
            ->when($submittedIds === [], fn ($query) => $query)
            ->get();

        foreach ($imagesToDelete as $image) {
            $image->deleteStoredFile();
            $image->delete();
        }

        foreach ($imagesInput as $imageData) {
            $imageId = filled($imageData['id'] ?? null) ? (int) $imageData['id'] : null;
            $image = $imageId
                ? $gallery->images()->whereKey($imageId)->firstOrFail()
                : new BlogGalleryImage(['blog_gallery_id' => $gallery->id]);

            $storedPath = BlogGalleryImageController::resolveStoredPath($imageData['stored_image_path'] ?? null);

            if (! $storedPath && $image->exists) {
                $storedPath = $image->image_path;
            }

            if (! $storedPath) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'images' => 'Minden képhez szükséges a feltöltés.',
                ]);
            }

            $image->fill([
                'image_path' => $storedPath,
                'title' => filled($imageData['title'] ?? null) ? $imageData['title'] : null,
                'description' => filled($imageData['description'] ?? null) ? $imageData['description'] : null,
                'sort_order' => (int) $imageData['sort_order'],
            ]);
            $image->save();
        }
    }

    private function initialImagesForEditor(BlogGallery $gallery): array
    {
        $oldImages = old('images');
        if (is_array($oldImages)) {
            return array_values($oldImages);
        }

        return $gallery->images->map(function (BlogGalleryImage $image) {
            return [
                'id' => $image->id,
                'stored_image_path' => $image->image_path,
                'image_url' => $image->imageUrl(),
                'title' => $image->title,
                'description' => $image->description,
                'sort_order' => $image->sort_order,
            ];
        })->values()->all();
    }
}
