<?php

namespace App\Models;

use App\Support\PublicUploads;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BlogGalleryImage extends Model
{
    protected $fillable = [
        'blog_gallery_id',
        'image_path',
        'title',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(BlogGallery::class, 'blog_gallery_id');
    }

    public function imageUrl(): ?string
    {
        return PublicUploads::url($this->image_path);
    }

    public function deleteStoredFile(): void
    {
        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }
    }
}
