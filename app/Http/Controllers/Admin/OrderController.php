<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

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

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'billing_address' => ['required', 'string', 'max:500'],
            'total_price' => ['required', 'integer', 'min:0'],
            'shipped' => ['sometimes', 'boolean'],
            'note' => ['nullable', 'string'],
        ]);

        $data['shipped'] = $request->boolean('shipped');

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

