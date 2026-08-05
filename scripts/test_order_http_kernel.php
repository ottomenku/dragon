<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$payload = [
    'name' => 'otto',
    'phone' => '30468988888',
    'shipping_address' => 'Nagyréde kossuth 32',
    'billing_address' => 'Nagyréde kossuth 32',
    'items' => [
        ['id' => 1, 'title' => 'Bazsalikom', 'price' => 800, 'qty' => 2],
    ],
    'total_price' => 5099,
    'terms_accepted' => true,
    'payment_method' => 'barion',
    'shipping_method' => 'mpl',
    'delivery_type' => 'home',
];

$json = json_encode($payload);
$request = Illuminate\Http\Request::create(
    '/orders',
    'POST',
    [],
    [],
    [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ],
    $json
);

try {
    $httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $httpKernel->handle($request);
    echo 'STATUS: '.$response->getStatusCode().PHP_EOL;
    echo $response->getContent().PHP_EOL;
    $httpKernel->terminate($request, $response);
} catch (Throwable $e) {
    echo 'EX: '.$e->getMessage().PHP_EOL;
    echo $e->getFile().':'.$e->getLine().PHP_EOL;
}
