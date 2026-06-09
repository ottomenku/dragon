<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethodSetting;
use App\Models\PickupPoint;
use App\Models\ShippingMethodSetting;
use App\Services\BarionPaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private BarionPaymentService $barionPayment
    ) {}

    public function store(Request $request)
    {
        $enabledPaymentMethods = PaymentMethodSetting::enabledMethodKeys();
        if ($enabledPaymentMethods === []) {
            return response()->json([
                'success' => false,
                'message' => 'Jelenleg nem érhető el fizetési mód. Kérjük, próbálja újra később.',
            ], 422);
        }

        $enabledShippingMethods = ShippingMethodSetting::enabledMethodKeys();
        if ($enabledShippingMethods === []) {
            return response()->json([
                'success' => false,
                'message' => 'Jelenleg nem érhető el szállítási mód. Kérjük, próbálja újra később.',
            ], 422);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required_if:delivery_type,home', 'nullable', 'string', 'max:500'],
            'billing_address' => ['required', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.title' => ['required', 'string'],
            'items.*.price' => ['required', 'integer', 'min:0'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'integer', 'min:0'],
            'terms_accepted' => ['required', 'accepted'],
            'payment_method' => ['required', 'in:'.implode(',', $enabledPaymentMethods)],
            'shipping_method' => ['required', 'in:'.implode(',', $enabledShippingMethods)],
            'delivery_type' => ['required', 'in:home,pickup'],
            'pickup_point_external_id' => ['required_if:delivery_type,pickup', 'nullable', 'string', 'max:64'],
            'pickup_point_name' => ['required_if:delivery_type,pickup', 'nullable', 'string', 'max:255'],
            'pickup_point_address' => ['required_if:delivery_type,pickup', 'nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string'],
        ]);

        $pickupPoint = null;
        if ($data['delivery_type'] === 'pickup') {
            $pickupPoint = PickupPoint::query()
                ->where('carrier', $data['shipping_method'])
                ->where('external_id', $data['pickup_point_external_id'])
                ->first();

            if (! $pickupPoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'A kiválasztott átvételi pont nem érvényes. Kérjük, válasszon másikat.',
                ], 422);
            }

            $data['pickup_point_name'] = $pickupPoint->name;
            $data['pickup_point_address'] = $pickupPoint->fullAddress();
            $data['shipping_address'] = $pickupPoint->displayLabel();
        }

        $itemsTotal = collect($data['items'])->sum(
            fn (array $item): int => ((int) $item['price']) * ((int) $item['qty'])
        );
        $shippingFee = ShippingMethodSetting::current()->feeFor($data['shipping_method']);
        $expectedTotal = $itemsTotal + $shippingFee;

        if ((int) $data['total_price'] !== $expectedTotal) {
            return response()->json([
                'success' => false,
                'message' => 'A végösszeg nem egyezik (termékek + szállítási díj). Kérjük, frissítse az oldalt és próbálja újra.',
            ], 422);
        }

        $order = Order::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'],
            'items' => $data['items'],
            'total_price' => $expectedTotal,
            'shipping_fee' => $shippingFee,
            'payment_method' => $data['payment_method'],
            'shipping_method' => $data['shipping_method'],
            'delivery_type' => $data['delivery_type'],
            'pickup_point_external_id' => $data['pickup_point_external_id'] ?? null,
            'pickup_point_name' => $data['pickup_point_name'] ?? null,
            'pickup_point_address' => $data['pickup_point_address'] ?? null,
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
