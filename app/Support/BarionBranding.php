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

    public static function showFooterBranding(): bool
    {
        return (bool) (BarionSetting::current()?->pixel_footer_enabled ?? false);
    }

    public static function showCheckoutLogos(): bool
    {
        return PaymentMethodSetting::isEnabled('barion');
    }

    public static function shouldShowBranding(string $context = 'footer'): bool
    {
        return match ($context) {
            'checkout' => self::showCheckoutLogos(),
            default => self::showFooterBranding(),
        };
    }
}
