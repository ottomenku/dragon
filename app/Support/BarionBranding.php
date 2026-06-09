<?php

namespace App\Support;

use App\Models\BarionSetting;
use App\Models\PaymentMethodSetting;

class BarionBranding
{
    public static function pixelId(): ?string
    {
        $pixelId = BarionSetting::current()?->pixel_id;

        return filled($pixelId) ? trim($pixelId) : null;
    }

    public static function hasPixel(): bool
    {
        return self::pixelId() !== null;
    }

    public static function showPaymentLogos(): bool
    {
        return PaymentMethodSetting::isEnabled('barion');
    }
}
