@extends('layouts.admin')

@section('title', 'Megrendelés #' . $order->id)

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Megrendelés #{{ $order->id }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $order->created_at->format('Y.m.d H:i') }}</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 space-y-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Vevő adatai</h2>
            <p><span class="font-medium">Név:</span> {{ $order->name }}</p>
            <p><span class="font-medium">Telefon:</span> {{ $order->phone }}</p>
            <p><span class="font-medium">Kézbesítés:</span> {{ $order->deliveryTypeLabel() }}</p>
            @if($order->delivery_type === 'pickup')
                <p><span class="font-medium">Átvételi pont:</span><br>{{ $order->pickup_point_name }}<br>{{ $order->pickup_point_address }}</p>
            @else
                <p><span class="font-medium">Szállítási cím:</span><br>{{ $order->shipping_address }}</p>
            @endif
            <p><span class="font-medium">Szállítási mód:</span> {{ $order->shippingMethodLabel() }}</p>
            @if($order->shipping_fee > 0)
                <p><span class="font-medium">Szállítási díj:</span> {{ number_format($order->shipping_fee) }} Ft</p>
            @endif
            <p><span class="font-medium">Számlázási cím:</span><br>{{ $order->billing_address }}</p>
        </div>
        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 space-y-2">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Rendelés állapota</h2>
            <p>
                <span class="font-medium">Kiküldve:</span>
                @if($order->shipped)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Igen</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nem</span>
                @endif
            </p>
            <p><span class="font-medium">Fizetési mód:</span> {{ $order->paymentMethodLabel() }}</p>
            <p><span class="font-medium">Fizetve:</span> {{ $order->fizetveLabel() }}</p>
            <p><span class="font-medium">Végösszeg:</span> {{ number_format($order->total_price) }} Ft</p>
            @if($order->payment_method === 'barion')
                <p><span class="font-medium">Barion PaymentId:</span> {{ $order->barion_payment_id ?: '—' }}</p>
                <p><span class="font-medium">Fizetés állapota:</span> {{ $order->payment_status ?: '—' }}</p>
            @endif
            @if($order->note)
                <p class="mt-2">
                    <span class="font-medium">Megjegyzés:</span><br>
                    {{ $order->note }}
                </p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-200 p-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Megrendelt termékek</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Termék</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Egységár</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Mennyiség</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Összeg</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($order->items ?? [] as $item)
                        <tr>
                            <td class="px-4 py-2 text-gray-800">{{ $item['title'] ?? '' }}</td>
                            <td class="px-4 py-2 text-right text-gray-700">{{ isset($item['price']) ? number_format($item['price']) . ' Ft' : '' }}</td>
                            <td class="px-4 py-2 text-center text-gray-700">{{ $item['qty'] ?? '' }}</td>
                            <td class="px-4 py-2 text-right text-gray-800 font-semibold">
                                @php
                                    $line = (int)($item['price'] ?? 0) * (int)($item['qty'] ?? 0);
                                @endphp
                                {{ number_format($line) }} Ft
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <button type="button" class="btn-order-transactions inline-flex items-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg" data-order-id="{{ $order->id }}">
            Tranzakciók
        </button>
        <a href="{{ route('admin.orders.edit', $order) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">
            Megrendelés szerkesztése
        </a>
    </div>

    @include('admin.orders._transactions_modal')
@endsection

