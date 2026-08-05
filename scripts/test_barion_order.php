<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $settings = App\Models\BarionSetting::current();
    echo 'use_test='.(int) $settings->use_test.PHP_EOL;
    echo 'payee='.$settings->payee.PHP_EOL;
    echo 'pos_key_len='.strlen($settings->pos_key ?? '').PHP_EOL;

    $order = App\Models\Order::create([
        'name' => 'CLI Teszt',
        'phone' => '06301234567',
        'shipping_address' => '3214 Nagyréde, Teszt 1.',
        'billing_address' => '3214 Nagyréde, Teszt 1.',
        'items' => [['id' => 27, 'title' => 'Kakukkfű levél', 'price' => 300, 'qty' => 1]],
        'total_price' => 3799,
        'shipping_fee' => 3499,
        'payment_method' => 'barion',
        'shipping_method' => 'mpl',
        'delivery_type' => 'home',
        'payment_status' => 'pending',
    ]);

    $service = app(App\Services\BarionPaymentService::class);
    $result = $service->startPayment($order);
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
} catch (Throwable $e) {
    echo 'EXCEPTION: '.$e->getMessage().PHP_EOL;
    echo $e->getFile().':'.$e->getLine().PHP_EOL;
    echo $e->getTraceAsString().PHP_EOL;
    exit(1);
}
