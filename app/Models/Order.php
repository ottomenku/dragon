<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const FIZETVE_NONE = null;

    public const FIZETVE_FIZETVE = 'fizetve';

    public const FIZETVE_VISSZATERITVE = 'visszateritve';

    public const FIZETVE_TOROLVE = 'torolve';

    public const FIZETVE_PROBLEMAS = 'problemas';

    public const FIZETVE_SIKERTELEN_KARTYAS = 'sikertelen_kartyas';

    protected $fillable = [
        'name',
        'phone',
        'shipping_address',
        'billing_address',
        'items',
        'total_price',
        'payment_method',
        'barion_payment_id',
        'payment_status',
        'fizetve',
        'shipped',
        'note',
    ];

    protected $casts = [
        'items' => 'array',
        'shipped' => 'boolean',
    ];

    public static function fizetveOptions(): array
    {
        return [
            '' => 'None',
            self::FIZETVE_FIZETVE => 'Fizetve',
            self::FIZETVE_VISSZATERITVE => 'Visszatérítve',
            self::FIZETVE_TOROLVE => 'Törölve',
            self::FIZETVE_PROBLEMAS => 'Problémás',
            self::FIZETVE_SIKERTELEN_KARTYAS => 'Sikertelen kártyás',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('created_at');
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'otp' => 'OTP kártya',
            'barion' => 'Barion kártya',
            default => 'Utánvét',
        };
    }

    public function fizetveLabel(): string
    {
        return self::fizetveOptions()[$this->fizetve ?? ''] ?? 'None';
    }
}
