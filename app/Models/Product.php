<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'title',
        'category',
        'intro',
        'moreinfo',
        'ar',
        'kedv',
        'public',
        'tomain',
    ];

    protected $casts = [
        'public' => 'boolean',
        'tomain' => 'boolean',
    ];
}
