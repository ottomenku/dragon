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

        $posKey = $settings->pos_key;
        $apiBase = $settings->use_test ? self::API_TEST : self::API_PROD;
        $payBase = $settings->use_test ? self::PAY_TEST : self::PAY_PROD;

        $paymentRequestId = 'order-'.$order->id.'-'.Str::lower(Str::random(10));
        $redirectUrl = route('payment.barion.return', [], true);
        $callbackUrl = route('payment.barion.callback', [], true);

        $items = [];
        foreach ($order->items ?? [] as $row) {
            $qty = max(1, (int) ($row['qty'] ?? 1));
            $unit = (int) ($row['price'] ?? 0);
            $lineTotal = $unit * $qty;
            $items[] = [
                'Name' => (string) ($row['title'] ?? 'Termék'),
                'Description' => '',
                'Quantity' => $qty,
                'Unit' => 'db',
                'UnitPrice' => $unit,
                'ItemTotal' => $lineTotal,
                'SKU' => (string) ($row['id'] ?? ''),
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
            'PaymentRequestId' => $paymentRequestId,
            'Locale' => 'hu-HU',
            'Currency' => 'HUF',
            'OrderNumber' => (string) $order->id,
            'RedirectUrl' => $redirectUrl,
            'CallbackUrl' => $callbackUrl,
            'Transactions' => [[
                'POSTransactionId' => 'webshop-'.$order->id,
                'Payee' => $settings->payee,
                'Total' => (int) $order->total_price,
                'Comment' => 'Webshop megrendelés #'.$order->id,
                'Items' => $items,
            ]],
        ];

        $url = $apiBase.'/v2/Payment/Start';

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'x-pos-key' => $posKey,
                    'Accept' => 'application/json',
                ])
                ->asJson()
                ->post($url, $payload);
        } catch (\Throwable $e) {
            Log::error('Barion StartPayment hálózati hiba', ['exception' => $e]);

            return ['ok' => false, 'message' => 'A fizetési szolgáltató nem érhető el. Próbálja újra később.'];
        }

        $json = $response->json();
        if (! is_array($json)) {
            return ['ok' => false, 'message' => 'Érvénytelen válasz a fizetési szolgáltatótól.'];
        }

        if (! empty($json['Errors']) && is_array($json['Errors'])) {
            $first = $json['Errors'][0] ?? [];
            $msg = is_array($first)
                ? (string) ($first['Description'] ?? $first['Title'] ?? $first['ErrorCode'] ?? 'Ismeretlen Barion hiba')
                : 'Barion hiba';

            Log::warning('Barion StartPayment API hiba', ['errors' => $json['Errors']]);

            return ['ok' => false, 'message' => $msg];
        }

        $paymentId = $json['PaymentId'] ?? null;
        if (! is_string($paymentId) || $paymentId === '') {
            return ['ok' => false, 'message' => 'Hiányzó fizetésazonosító a Barion válaszból.'];
        }

        $redirectUrlOut = $payBase.'?'.http_build_query(['id' => $paymentId]);

        return [
            'ok' => true,
            'payment_id' => $paymentId,
            'redirect_url' => $redirectUrlOut,
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

        $posKey = $settings->pos_key;
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
}
