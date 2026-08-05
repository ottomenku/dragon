<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$recent = App\Models\Order::query()->orderByDesc('id')->take(5)->get(['id', 'created_at', 'name']);
foreach ($recent as $o) {
    echo $o->id.' '.$o->created_at.' '.$o->name.PHP_EOL;
}

echo 'env readable: '.(is_readable(base_path('.env')) ? 'yes' : 'no').PHP_EOL;
echo 'APP_KEY set: '.(config('app.key') ? 'yes' : 'no').PHP_EOL;
