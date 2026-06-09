<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContentSetting extends Model
{
    protected $fillable = [
        'contact_content',
        'footer_content',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'contact_content' => static::defaultContactContent(),
            'footer_content' => static::defaultFooterContent(),
        ]);
    }

    public static function defaultContactContent(): string
    {
        return <<<'HTML'
<div class="grid md:grid-cols-2 gap-8">
    <div>
        <p class="font-semibold">Medveczkiné Magos Mónika</p>
        <p><a href="mailto:medmo1973@gmail.com" class="text-emerald-700 hover:underline">medmo1973@gmail.com</a></p>
        <p><a href="tel:+36309689532" class="text-emerald-700 hover:underline">+36 30/968 9532</a></p>
    </div>
    <div>
        <p class="font-semibold">Medveczki István</p>
        <p><a href="tel:+36303037196" class="text-emerald-700 hover:underline">+36 30/303 7196</a></p>
    </div>
</div>
HTML;
    }

    public static function defaultFooterContent(): string
    {
        return <<<'HTML'
<h2 class="font-display text-2xl md:text-3xl font-semibold mb-6">Kapcsolat</h2>
<div class="grid md:grid-cols-2 gap-8">
    <div>
        <p class="font-semibold">Medveczkiné Magos Mónika</p>
        <p><a href="mailto:medmo1973@gmail.com" class="hover:text-emerald-200 underline">medmo1973@gmail.com</a></p>
        <p><a href="tel:+36309689532" class="hover:text-emerald-200">+36 30/968 9532</a></p>
    </div>
    <div>
        <p class="font-semibold">Medveczki István</p>
        <p><a href="tel:+36303037196" class="hover:text-emerald-200">+36 30/303 7196</a></p>
    </div>
</div>
HTML;
    }
}
