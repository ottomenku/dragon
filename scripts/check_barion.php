<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$s = App\Models\BarionSetting::current();
$p = App\Models\PaymentMethodSetting::current();
echo 'use_test='.(int) ($s->use_test ?? 0).PHP_EOL;
echo 'payee='.($s->payee ?: 'null').PHP_EOL;
echo 'pos_key='.($s->pos_key ? 'set' : 'empty').PHP_EOL;
echo 'pixel_id='.($s->pixel_id ?: 'null').PHP_EOL;
echo 'barion_enabled='.(int) ($p->barion_enabled ?? 0).PHP_EOL;
echo 'APP_URL='.config('app.url').PHP_EOL;
