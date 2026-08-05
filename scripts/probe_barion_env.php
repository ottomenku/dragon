<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$settings = App\Models\BarionSetting::current();
if (! $settings || ! $settings->isConfigured()) {
    echo "Barion nincs konfigurálva.\n";
    exit(1);
}

$posKey = $settings->pos_key;
$payee = $settings->payee;

foreach ([
    'test' => 'https://api.test.barion.com',
    'live' => 'https://api.barion.com',
] as $label => $base) {
    $payload = [
        'POSKey' => $posKey,
        'PaymentType' => 'Immediate',
        'GuestCheckOut' => true,
        'FundingSources' => ['All'],
        'PaymentRequestId' => 'probe-'.bin2hex(random_bytes(4)),
        'Locale' => 'hu-HU',
        'Currency' => 'HUF',
        'OrderNumber' => 'probe',
        'RedirectUrl' => config('app.url').'/payment/barion/return',
        'CallbackUrl' => config('app.url').'/payment/barion/callback',
        'Transactions' => [[
            'POSTransactionId' => 'probe-tx',
            'Payee' => $payee,
            'Total' => 100,
            'Comment' => 'Probe',
            'Items' => [[
                'Name' => 'Teszt',
                'Description' => '',
                'Quantity' => 1,
                'Unit' => 'db',
                'UnitPrice' => 100,
                'ItemTotal' => 100,
                'SKU' => '1',
            ]],
        ]],
    ];

    $response = Illuminate\Support\Facades\Http::timeout(20)
        ->withHeaders(['x-pos-key' => $posKey, 'Accept' => 'application/json'])
        ->asJson()
        ->post($base.'/v2/Payment/Start', $payload);

    $json = $response->json();
    $code = is_array($json) && ! empty($json['Errors'][0]['ErrorCode'])
        ? $json['Errors'][0]['ErrorCode']
        : ($response->successful() && ! empty($json['PaymentId']) ? 'OK' : 'Unknown');

    echo strtoupper($label).": {$code}\n";
}

echo 'admin_use_test='.(int) $settings->use_test."\n";
echo 'payee='.$payee."\n";
