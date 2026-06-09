<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
    protected $fillable = [
        'carrier',
        'external_id',
        'name',
        'address',
        'city',
        'zip',
        'latitude',
        'longitude',
        'point_type',
    ];

    public function displayLabel(): string
    {
        $parts = array_filter([
            $this->name,
            $this->zip,
            $this->city,
            $this->address,
        ]);

        return implode(' – ', $parts);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->zip,
            $this->city,
            $this->address,
        ]);

        return implode(', ', $parts);
    }

    /** @return list<string> */
    public static function carriersWithPoints(): array
    {
        return static::query()
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier')
            ->all();
    }
}
