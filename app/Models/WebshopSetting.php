<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebshopSetting extends Model
{
    protected $fillable = [
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['enabled' => true]);
    }

    public static function isOpen(): bool
    {
        return static::current()->enabled;
    }

    public static function userMayAccess(?User $user): bool
    {
        if (static::isOpen()) {
            return true;
        }

        return $user !== null && $user->isAdmin();
    }
}
