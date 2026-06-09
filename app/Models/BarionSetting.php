<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarionSetting extends Model
{
    protected $fillable = [
        'pos_key',
        'payee',
        'use_test',
        'pixel_id',
    ];

    protected $casts = [
        'use_test' => 'boolean',
        'pos_key' => 'encrypted',
    ];

    public static function current(): ?self
    {
        return static::query()->first();
    }

    public function isConfigured(): bool
    {
        return filled($this->payee) && filled($this->pos_key);
    }
}
