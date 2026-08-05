<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var Illuminate\Contracts\Http\Kernel $httpKernel */
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Warm session + CSRF like a browser on webshop.
$get = Illuminate\Http\Request::create('/webshop', 'GET');
$getResponse = $httpKernel->handle($get);
$session = $get->session();
$token = $session->token();

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

$post = Illuminate\Http\Request::create(
    '/orders',
    'POST',
    [],
    $session->all(),
    [],
    [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X-CSRF-TOKEN' => $token,
    ],
    json_encode($payload)
);
$post->setLaravelSession($session);

$response = $httpKernel->handle($post);
echo 'STATUS: '.$response->getStatusCode().PHP_EOL;
echo $response->getContent().PHP_EOL;

$httpKernel->terminate($post, $response);
