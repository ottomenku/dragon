<?php

namespace App\Http\Controllers;

use App\Services\BarionPaymentService;
use Illuminate\Http\Request;

class BarionPaymentController extends Controller
{
    public function __construct(
        private BarionPaymentService $barionPayment
    ) {}

    public function return(Request $request)
    {
        $paymentId = $request->query('paymentId') ?? $request->query('PaymentId');

        if (! is_string($paymentId) || $paymentId === '') {
            return view('payment.barion-result', [
                'state' => 'unknown',
                'message' => 'Hiányzó fizetésazonosító a visszatérési linkben.',
                'order' => null,
            ]);
        }

        $order = $this->barionPayment->syncOrderByBarionPaymentId($paymentId);

        if (! $order) {
            return view('payment.barion-result', [
                'state' => 'unknown',
                'message' => 'Ehhez a fizetéshez nem található megrendelés a rendszerben.',
                'order' => null,
            ]);
        }

        $state = match ($order->payment_status) {
            'paid' => 'success',
            'failed' => 'failed',
            default => 'pending',
        };

        return view('payment.barion-result', [
            'state' => $state,
            'message' => null,
            'order' => $order,
        ]);
    }

    public function callback(Request $request)
    {
        $paymentId = $request->input('PaymentId');
        if (! is_string($paymentId) || $paymentId === '') {
            $paymentId = $request->json('PaymentId');
        }

        if (is_string($paymentId) && $paymentId !== '') {
            $this->barionPayment->syncOrderByBarionPaymentId($paymentId);
        }

        return response('OK', 200);
    }
}
