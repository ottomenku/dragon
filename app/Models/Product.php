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
        'intro',
        'moreinfo',
        'ar',
        'kedv',
        'public',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];
}
