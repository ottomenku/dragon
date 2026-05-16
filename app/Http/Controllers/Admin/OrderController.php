<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::orderByDesc('created_at')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function transactions(Order $order)
    {
        $order->load('payments');

        return response()->json([
            'order_id' => $order->id,
            'fizetve_label' => $order->fizetveLabel(),
            'transactions' => $order->payments->map(fn ($payment) => [
                'transaction_id' => $payment->transaction_id,
                'type' => $payment->type,
                'type_label' => $payment->typeLabel(),
                'amount' => $payment->amount,
                'created_at' => $payment->created_at->format('Y.m.d H:i'),
            ])->values(),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'billing_address' => ['required', 'string', 'max:500'],
            'total_price' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cod,otp,barion'],
            'fizetve' => ['nullable', Rule::in(array_keys(Order::fizetveOptions()))],
            'shipped' => ['sometimes', 'boolean'],
            'note' => ['nullable', 'string'],
        ]);

        $data['shipped'] = $request->boolean('shipped');
        $data['fizetve'] = $request->input('fizetve') ?: null;

        $order->update($data);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'A megrendelés frissítve lett.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'A megrendelés törölve lett.');
    }
}
