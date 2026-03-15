<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'shipping_address',
        'billing_address',
        'items',
        'total_price',
        'shipped',
        'note',
    ];

    protected $casts = [
        'items' => 'array',
        'shipped' => 'boolean',
    ];
}

