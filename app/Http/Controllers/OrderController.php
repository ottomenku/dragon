<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BarionPaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private BarionPaymentService $barionPayment
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'billing_address' => ['required', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.title' => ['required', 'string'],
            'items.*.price' => ['required', 'integer', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cod,otp,barion'],
            'note' => ['nullable', 'string'],
        ]);

        $order = Order::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'],
            'items' => $data['items'],
            'total_price' => $data['total_price'],
            'payment_method' => $data['payment_method'],
            'note' => $data['note'] ?? null,
            'payment_status' => $data['payment_method'] === 'barion' ? 'pending' : null,
        ]);

        if ($data['payment_method'] === 'barion') {
            if (! $this->barionPayment->isReady()) {
                $this->barionPayment->markCardPaymentFailed($order);

                return response()->json([
                    'success' => false,
                    'message' => 'A Barion kártyás fizetés jelenleg nem elérhető. Válasszon másik fizetési módot, vagy próbálja újra később.',
                ], 422);
            }

            $start = $this->barionPayment->startPayment($order);
            if (! $start['ok']) {
                $this->barionPayment->markCardPaymentFailed($order);

                return response()->json([
                    'success' => false,
                    'message' => $start['message'],
                ], 422);
            }

            $order->forceFill([
                'barion_payment_id' => $start['payment_id'],
                'payment_status' => 'pending',
            ])->save();

            $this->barionPayment->recordPaymentStarted($order, $start['payment_id']);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'redirect_url' => $start['redirect_url'],
                'payment_method' => 'barion',
            ]);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
        ]);
    }
}
