<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogGallery extends Model
{
    public const DISPLAY_SLIDER = 'slider';

    public const DISPLAY_LIST = 'list';

    protected $fillable = [
        'name',
        'slug',
        'intro',
        'description',
        'display_mode',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(BlogGalleryImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'name' => 'Galéria',
            'slug' => 'galeria',
            'intro' => '',
            'description' => null,
            'display_mode' => self::DISPLAY_SLIDER,
            'sort_order' => 0,
        ]);
    }

    public function isSlider(): bool
    {
        return $this->display_mode !== self::DISPLAY_LIST;
    }

    public function isList(): bool
    {
        return $this->display_mode === self::DISPLAY_LIST;
    }
}
