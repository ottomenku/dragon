<?php

namespace App\Services;

use App\Models\BarionSetting;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BarionPaymentService
{
    private const API_TEST = 'https://api.test.barion.com';

    private const API_PROD = 'https://api.barion.com';

    private const PAY_TEST = 'https://secure.test.barion.com/Pay';

    private const PAY_PROD = 'https://secure.barion.com/Pay';

    private const PROBE_PAYMENT_ID = '00000000-0000-0000-0000-000000000001';

    public function isReady(): bool
    {
        $s = BarionSetting::current();

        return $s !== null && $s->isConfigured();
    }

    /**
     * @return array{ok: true, payment_id: string, redirect_url: string}|array{ok: false, message: string}
     */
    public function startPayment(Order $order): array
    {
        $settings = BarionSetting::current();
        if (! $settings || ! $settings->isConfigured()) {
            return ['ok' => false, 'message' => 'A Barion fizetés nincs beállítva.'];
        }

        $posKey = self::normalizePosKey($settings->pos_key);
        if ($posKey === null) {
            return ['ok' => false, 'message' => 'A Barion fizetés nincs beállítva.'];
        }

        $items = [];
        foreach ($order->items ?? [] as $row) {
            $qty = max(1, (int) ($row['qty'] ?? 1));
            $unit = (int) ($row['price'] ?? 0);
            $lineTotal = $unit * $qty;
            $name = trim((string) ($row['title'] ?? ''));
            if ($name === '') {
                $name = 'Termék';
            }
            $items[] = [
                'Name' => $name,
                'Description' => Str::limit($name, 500, ''),
                'Quantity' => $qty,
                'Unit' => 'db',
                'UnitPrice' => $unit,
                'ItemTotal' => $lineTotal,
                'SKU' => (string) ($row['id'] ?? ''),
            ];
        }

        $shippingFee = (int) ($order->shipping_fee ?? 0);
        if ($shippingFee > 0) {
            $shippingName = trim($order->shippingMethodLabel());
            if ($shippingName === '' || $shippingName === '—') {
                $shippingName = 'Szállítás';
            }
            $items[] = [
                'Name' => $shippingName,
                'Description' => $shippingName,
                'Quantity' => 1,
                'Unit' => 'db',
                'UnitPrice' => $shippingFee,
                'ItemTotal' => $shippingFee,
                'SKU' => 'shipping',
            ];
        }

        if ($items === []) {
            return ['ok' => false, 'message' => 'A rendeléshez nem tartozik tétel.'];
        }

        $payload = [
            'POSKey' => $posKey,
            'PaymentType' => 'Immediate',
            'GuestCheckOut' => true,
            'FundingSources' => ['All'],
            'PaymentRequestId' => 'order-'.$order->id.'-'.Str::lower(Str::random(10)),
            'Locale' => 'hu-HU',
            'Currency' => 'HUF',
            'OrderNumber' => (string) $order->id,
            'RedirectUrl' => route('payment.barion.return', [], true),
            'CallbackUrl' => route('payment.barion.callback', [], true),
            'Transactions' => [[
                'POSTransactionId' => 'webshop-'.$order->id,
                'Payee' => $settings->payee,
                'Total' => (int) $order->total_price,
                'Comment' => 'Webshop megrendelés #'.$order->id,
                'Items' => $items,
            ]],
        ];

        $preferTest = (bool) $settings->use_test;
        $started = $this->postStartPayment(
            $preferTest ? self::API_TEST : self::API_PROD,
            $payload
        );

        if ($this->isAuthenticationFailed($started['json'] ?? null)) {
            $started = $this->recoverFromAuthenticationFailure($settings, $preferTest, $payload);
        }

        if (! empty($started['network_error'])) {
            return ['ok' => false, 'message' => 'A fizetési szolgáltató nem érhető el. Próbálja újra később.'];
        }

        $json = $started['json'] ?? null;
        if (! is_array($json)) {
            return ['ok' => false, 'message' => 'Érvénytelen válasz a fizetési szolgáltatótól.'];
        }

        if (! empty($json['Errors']) && is_array($json['Errors'])) {
            $first = $json['Errors'][0] ?? [];
            $msg = is_array($first)
                ? $this->userFacingErrorMessage($first, (bool) $settings->fresh()?->use_test)
                : 'Barion hiba';

            $this->logBarionWarning('Barion StartPayment API hiba', ['errors' => $json['Errors']]);

            return ['ok' => false, 'message' => $msg];
        }

        $paymentId = $json['PaymentId'] ?? null;
        if (! is_string($paymentId) || $paymentId === '') {
            return ['ok' => false, 'message' => 'Hiányzó fizetésazonosító a Barion válaszból.'];
        }

        $useTest = (bool) $settings->fresh()?->use_test;
        $payBase = $useTest ? self::PAY_TEST : self::PAY_PROD;

        return [
            'ok' => true,
            'payment_id' => $paymentId,
            'redirect_url' => $payBase.'?'.http_build_query(['id' => $paymentId]),
        ];
    }

    public function markCardPaymentFailed(Order $order): void
    {
        $order->forceFill([
            'payment_status' => 'failed',
            'fizetve' => Order::FIZETVE_SIKERTELEN_KARTYAS,
        ])->save();
    }

    public function recordPaymentStarted(Order $order, string $paymentId): void
    {
        Payment::query()->updateOrCreate(
            [
                'order_id' => $order->id,
                'transaction_id' => $paymentId,
                'type' => Payment::TYPE_PAYMENT,
            ],
            ['amount' => (int) $order->total_price]
        );
    }

    /**
     * Frissíti a rendelés fizetési állapotát a Barion PaymentState alapján.
     */
    public function syncOrderByBarionPaymentId(string $paymentId): ?Order
    {
        $order = Order::query()->where('barion_payment_id', $paymentId)->first();
        if (! $order) {
            return null;
        }

        $this->refreshOrderPaymentStatus($order);

        return $order->fresh(['payments']);
    }

    public function refreshOrderPaymentStatus(Order $order): void
    {
        if ($order->payment_method !== 'barion' || ! $order->barion_payment_id) {
            return;
        }

        $settings = BarionSetting::current();
        if (! $settings || ! $settings->isConfigured()) {
            return;
        }

        $posKey = self::normalizePosKey($settings->pos_key);
        if ($posKey === null) {
            return;
        }

        $apiBase = $settings->use_test ? self::API_TEST : self::API_PROD;
        $url = $apiBase.'/v4/Payment/'.$order->barion_payment_id.'/PaymentState';

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-pos-key' => $posKey,
                    'Accept' => 'application/json',
                ])
                ->get($url);
        } catch (\Throwable $e) {
            Log::error('Barion PaymentState hálózati hiba', ['exception' => $e]);

            return;
        }

        $json = $response->json();
        if (! is_array($json)) {
            return;
        }

        $this->applyBarionPaymentState($order, $json);
    }

    public function applyBarionPaymentState(Order $order, array $json): void
    {
        $paymentId = $order->barion_payment_id;
        $status = $json['Status'] ?? null;
        if (! is_string($status)) {
            return;
        }

        $total = (int) ($json['Total'] ?? $order->total_price);
        if ($total <= 0) {
            $total = (int) $order->total_price;
        }

        $hasRefund = false;

        foreach ($json['Transactions'] ?? [] as $tx) {
            if (! is_array($tx)) {
                continue;
            }
            $txId = (string) ($tx['TransactionId'] ?? $tx['POSTransactionId'] ?? $paymentId);
            $amount = (int) ($tx['TransactionAmount'] ?? $tx['Total'] ?? 0);
            $txStatus = (string) ($tx['Status'] ?? '');

            if ($amount > 0 && in_array($txStatus, ['Succeeded', 'Reserved', 'Authorized', 'Completed'], true)) {
                Payment::query()->updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'transaction_id' => $txId,
                        'type' => Payment::TYPE_PAYMENT,
                    ],
                    ['amount' => $amount]
                );
            }
        }

        $refundedAmount = (int) ($json['RefundedAmount'] ?? 0);
        if ($refundedAmount > 0) {
            $hasRefund = true;
            Payment::query()->updateOrCreate(
                [
                    'order_id' => $order->id,
                    'transaction_id' => $paymentId.'-refund',
                    'type' => Payment::TYPE_REFUND,
                ],
                ['amount' => $refundedAmount]
            );
        }

        foreach ($json['RefundTransactions'] ?? $json['Refunds'] ?? [] as $refund) {
            if (! is_array($refund)) {
                continue;
            }
            $refId = (string) ($refund['TransactionId'] ?? $refund['RefundId'] ?? $paymentId.'-refund-'.($refund['Amount'] ?? 0));
            $amount = (int) ($refund['Amount'] ?? $refund['Total'] ?? $refund['RefundedAmount'] ?? 0);
            if ($amount > 0) {
                $hasRefund = true;
                Payment::query()->updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'transaction_id' => $refId,
                        'type' => Payment::TYPE_REFUND,
                    ],
                    ['amount' => $amount]
                );
            }
        }

        $paid = in_array($status, ['Succeeded', 'PartiallySucceeded'], true);
        $failed = in_array($status, ['Failed', 'Expired', 'Denied', 'Canceled'], true);

        if ($hasRefund) {
            $order->fizetve = Order::FIZETVE_VISSZATERITVE;
            $order->payment_status = 'refunded';
        } elseif ($paid) {
            Payment::query()->updateOrCreate(
                [
                    'order_id' => $order->id,
                    'transaction_id' => $paymentId,
                    'type' => Payment::TYPE_PAYMENT,
                ],
                ['amount' => $total]
            );
            $order->fizetve = Order::FIZETVE_FIZETVE;
            $order->payment_status = 'paid';
        } elseif ($failed) {
            $order->fizetve = Order::FIZETVE_SIKERTELEN_KARTYAS;
            $order->payment_status = 'failed';
        } else {
            $order->payment_status = 'pending';
        }

        $order->save();
    }

    /**
     * @return 'test'|'live'|null
     */
    public function detectEnvironment(?string $posKey): ?string
    {
        $posKey = self::normalizePosKey($posKey);
        if ($posKey === null) {
            return null;
        }

        $testOk = $this->posKeyAccepted(self::API_TEST, $posKey);
        $liveOk = $this->posKeyAccepted(self::API_PROD, $posKey);

        if ($liveOk && ! $testOk) {
            return 'live';
        }
        if ($testOk && ! $liveOk) {
            return 'test';
        }

        return null;
    }

    /**
     * @return array{ok: bool, environment: 'test'|'live'|null, message: string}
     */
    public function verifyConnection(?string $posKey, bool $useTest): array
    {
        $posKey = self::normalizePosKey($posKey);
        if ($posKey === null) {
            return [
                'ok' => false,
                'environment' => null,
                'message' => 'Nincs megadva POSKey.',
            ];
        }

        $detected = $this->detectEnvironment($posKey);
        $selected = $useTest ? 'test' : 'live';

        if ($detected === $selected) {
            return [
                'ok' => true,
                'environment' => $detected,
                'message' => $detected === 'live'
                    ? 'Az éles POSKey érvényes. A kártyás fizetés használható.'
                    : 'A teszt POSKey érvényes a sandboxban. Éles fizetéshez a secure.barion.com Secret POSKey-e kell.',
            ];
        }

        if ($detected === 'live') {
            return [
                'ok' => false,
                'environment' => 'live',
                'message' => 'Ez a POSKey az éles környezethez tartozik. Válassza az Éles (production) beállítást.',
            ];
        }

        if ($detected === 'test') {
            return [
                'ok' => false,
                'environment' => 'test',
                'message' => 'Ez a POSKey a teszt (sandbox) környezethez tartozik. A Barion elfogadása után az éles Secret POSKey-t a secure.barion.com → Üzlet → Részletek menüből kell bemásolni, és az Éles környezetet kell választani.',
            ];
        }

        return [
            'ok' => false,
            'environment' => null,
            'message' => 'A POSKey egyik Barion környezetben sem érvényes. A Secret (nem a nyilvános) kulcsot adja meg, szóköz és kapcsos zárójel nélkül.',
        ];
    }

    public static function normalizePosKey(?string $posKey): ?string
    {
        if ($posKey === null) {
            return null;
        }

        $posKey = trim($posKey);
        $posKey = trim($posKey, "{} \t\n\r\0\x0B\"'");
        $posKey = preg_replace('/\s+/', '', $posKey) ?? $posKey;

        return $posKey === '' ? null : $posKey;
    }

    /**
     * Teszt kulccsal ne engedjünk élesnek beállított shopon sandbox fizetést.
     *
     * @param  array<string, mixed>  $payload
     * @return array{json: ?array, network_error: bool}
     */
    private function recoverFromAuthenticationFailure(BarionSetting $settings, bool $preferTest, array $payload): array
    {
        $detected = $this->detectEnvironment($payload['POSKey'] ?? null);

        if ($preferTest && $detected === 'live') {
            $retry = $this->postStartPayment(self::API_PROD, $payload);
            if ($this->isSuccessfulStart($retry['json'] ?? null)) {
                $settings->use_test = false;
                $settings->save();
                $this->logBarionWarning('Barion környezet automatikusan élesre váltva: a POSKey az éles API-n érvényes.');
            }

            return $retry;
        }

        if (! $preferTest && $detected === 'test') {
            return [
                'json' => [
                    'Errors' => [[
                        'ErrorCode' => 'AuthenticationFailed',
                        'Description' => 'test_key_on_live',
                    ]],
                ],
                'network_error' => false,
            ];
        }

        return [
            'json' => [
                'Errors' => [[
                    'ErrorCode' => 'AuthenticationFailed',
                    'Description' => 'The login information provided is incorrect.',
                ]],
            ],
            'network_error' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{json: ?array, network_error: bool}
     */
    private function postStartPayment(string $apiBase, array $payload): array
    {
        $url = $apiBase.'/v2/Payment/Start';

        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->asJson()
                ->post($url, $payload);
        } catch (\Throwable $e) {
            Log::error('Barion StartPayment hálózati hiba', ['exception' => $e]);

            return ['json' => null, 'network_error' => true];
        }

        $json = $response->json();

        return [
            'json' => is_array($json) ? $json : null,
            'network_error' => false,
        ];
    }

    private function posKeyAccepted(string $apiBase, string $posKey): bool
    {
        $url = $apiBase.'/v2/Payment/GetPaymentState';

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->get($url, [
                    'POSKey' => $posKey,
                    'PaymentId' => self::PROBE_PAYMENT_ID,
                ]);
        } catch (\Throwable) {
            return false;
        }

        $json = $response->json();
        if (! is_array($json)) {
            return false;
        }

        return ! $this->isAuthenticationFailed($json);
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private function isAuthenticationFailed(?array $json): bool
    {
        if ($json === null || empty($json['Errors']) || ! is_array($json['Errors'])) {
            return false;
        }

        foreach ($json['Errors'] as $error) {
            if (is_array($error) && ($error['ErrorCode'] ?? '') === 'AuthenticationFailed') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>|null  $json
     */
    private function isSuccessfulStart(?array $json): bool
    {
        if ($json === null) {
            return false;
        }

        if (! empty($json['Errors'])) {
            return false;
        }

        $paymentId = $json['PaymentId'] ?? null;

        return is_string($paymentId) && $paymentId !== '';
    }

    /**
     * @param  array<string, mixed>  $error
     */
    private function userFacingErrorMessage(array $error, bool $useTest): string
    {
        $code = (string) ($error['ErrorCode'] ?? '');
        $description = (string) ($error['Description'] ?? '');

        return match (true) {
            $code === 'AuthenticationFailed' && $description === 'test_key_on_live' => 'A megadott POSKey a teszt (sandbox) környezethez tartozik, de az éles fizetés van kiválasztva. A Barion elfogadása után a secure.barion.com → Üzlet → Részletek menüből másolja be az éles Secret POSKey-t, és az adminban válassza az Éles környezetet.',
            $code === 'AuthenticationFailed' && $useTest => 'A Barion teszt POSKey érvénytelen. Ha a Barion már elfogadta a webshopot, válassza az Éles környezetet, és adja meg a secure.barion.com Secret POSKey-ét (ne a nyilvános kulcsot).',
            $code === 'AuthenticationFailed' => 'A Barion éles POSKey érvénytelen. Ellenőrizze, hogy a secure.barion.com → Üzlet → Részletek menüből a Secret POSKey került-e be (nem a teszt.barion.com kulcsa, és nem a nyilvános kulcs).',
            $code === 'ModelValidationError' => 'A fizetés adatai hiányosak vagy hibásak. Próbálja újra, vagy válasszon másik fizetési módot.',
            $code === 'InsufficientFunds' => 'Nincs elegendő fedezet a kártyán.',
            $code === 'CardExpired' => 'A kártya lejárt.',
            default => (string) ($error['Description'] ?? $error['Title'] ?? $error['ErrorCode'] ?? 'Ismeretlen Barion hiba'),
        };
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logBarionWarning(string $message, array $context = []): void
    {
        try {
            Log::warning($message, $context);
        } catch (\Throwable) {
            // Ne omoljon össze a rendelés, ha a naplófájl nem írható.
        }
    }
}
