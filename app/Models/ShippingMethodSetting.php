<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethodSetting extends Model
{
    public const METHODS = [
        'mpl' => 'MPL',
        'foxpost' => 'Foxpost',
        'dhl' => 'DHL',
        'gls' => 'GLS',
        'packeta' => 'Packeta',
    ];

    protected $fillable = [
        'mpl_enabled',
        'foxpost_enabled',
        'dhl_enabled',
        'gls_enabled',
        'packeta_enabled',
        'mpl_fee',
        'foxpost_fee',
        'dhl_fee',
        'gls_fee',
        'packeta_fee',
    ];

    protected $casts = [
        'mpl_enabled' => 'boolean',
        'foxpost_enabled' => 'boolean',
        'dhl_enabled' => 'boolean',
        'gls_enabled' => 'boolean',
        'packeta_enabled' => 'boolean',
        'mpl_fee' => 'integer',
        'foxpost_fee' => 'integer',
        'dhl_fee' => 'integer',
        'gls_fee' => 'integer',
        'packeta_fee' => 'integer',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'mpl_enabled' => true,
            'foxpost_enabled' => true,
            'dhl_enabled' => true,
            'gls_enabled' => true,
            'packeta_enabled' => true,
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

    public function feeFor(string $method): int
    {
        $column = $method.'_fee';

        return (int) ($this->{$column} ?? 0);
    }

    /** @return array<string, int> */
    public function feesMap(): array
    {
        $fees = [];

        foreach (self::METHODS as $key => $label) {
            $fees[$key] = $this->feeFor($key);
        }

        return $fees;
    }
}
