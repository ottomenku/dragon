<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodSetting extends Model
{
    public const METHODS = [
        'cod' => 'Utánvét',
        'otp' => 'OTP kártya',
        'barion' => 'Barion kártya (online)',
    ];

    protected $fillable = [
        'cod_enabled',
        'otp_enabled',
        'barion_enabled',
    ];

    protected $casts = [
        'cod_enabled' => 'boolean',
        'otp_enabled' => 'boolean',
        'barion_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'cod_enabled' => true,
            'otp_enabled' => true,
            'barion_enabled' => true,
        ]);
    }

    /** @return array<string, string> */
    public function enabledMethods(): array
    {
        $enabled = [];

        foreach (self::METHODS as $key => $label) {
            $column = $key.'_enabled';
            if ($this->{$column}) {
                $enabled[$key] = $label;
            }
        }

        return $enabled;
    }

    /** @return list<string> */
    public static function enabledMethodKeys(): array
    {
        return array_keys(static::current()->enabledMethods());
    }

    public static function isEnabled(string $method): bool
    {
        return in_array($method, static::enabledMethodKeys(), true);
    }
}
