<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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
            'note' => ['nullable', 'string'],
        ]);

        $order = Order::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'shipping_address' => $data['shipping_address'],
            'billing_address' => $data['billing_address'],
            'items' => $data['items'],
            'total_price' => $data['total_price'],
            'note' => $data['note'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
        ]);
    }
}

