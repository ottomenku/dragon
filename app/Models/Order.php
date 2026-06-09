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
        'shipping_fee',
        'payment_method',
        'shipping_method',
        'delivery_type',
        'pickup_point_external_id',
        'pickup_point_name',
        'pickup_point_address',
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

    public function shippingMethodLabel(): string
    {
        return ShippingMethodSetting::METHODS[$this->shipping_method] ?? '—';
    }

    public function deliveryTypeLabel(): string
    {
        return match ($this->delivery_type) {
            'pickup' => 'Automata / csomagpont',
            default => 'Házhozszállítás',
        };
    }

    public function deliverySummary(): string
    {
        if ($this->delivery_type === 'pickup' && $this->pickup_point_name) {
            return $this->pickup_point_name.' – '.($this->pickup_point_address ?? '');
        }

        return $this->shipping_address;
    }

    public function fizetveLabel(): string
    {
        return self::fizetveOptions()[$this->fizetve ?? ''] ?? 'None';
    }
}
