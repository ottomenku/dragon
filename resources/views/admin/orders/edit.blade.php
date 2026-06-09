@extends('layouts.admin')

@section('title', 'Megrendelés szerkesztése #' . $order->id)

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Megrendelés szerkesztése #{{ $order->id }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $order->created_at->format('Y.m.d H:i') }}</p>
    </div>

    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow border border-gray-200 p-4 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800">Vevő adatai</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="name">Név</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $order->name) }}" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $order->phone) }}" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="shipping_address">Szállítási cím</label>
                    <textarea id="shipping_address" name="shipping_address" rows="2" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                    @error('shipping_address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="billing_address">Számlázási cím</label>
                    <textarea id="billing_address" name="billing_address" rows="2" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('billing_address', $order->billing_address) }}</textarea>
                    @error('billing_address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-white rounded-xl shadow border border-gray-200 p-4 space-y-4">
                <h2 class="text-lg font-semibold text-gray-800">Megrendelés adatai</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="total_price">Végösszeg (Ft)</label>
                    <input type="number" id="total_price" name="total_price" value="{{ old('total_price', $order->total_price) }}" min="0" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('total_price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="payment_method">Fizetési mód</label>
                    <select id="payment_method" name="payment_method" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="cod" {{ old('payment_method', $order->payment_method) === 'cod' ? 'selected' : '' }}>Utánvét</option>
                        <option value="otp" {{ old('payment_method', $order->payment_method) === 'otp' ? 'selected' : '' }}>OTP kártya</option>
                        <option value="barion" {{ old('payment_method', $order->payment_method) === 'barion' ? 'selected' : '' }}>Barion kártya</option>
                    </select>
                    @error('payment_method') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="shipping_method">Szállítási mód</label>
                    <select id="shipping_method" name="shipping_method" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="" {{ old('shipping_method', $order->shipping_method) === null || old('shipping_method', $order->shipping_method) === '' ? 'selected' : '' }}>—</option>
                        @foreach(\App\Models\ShippingMethodSetting::METHODS as $value => $label)
                            <option value="{{ $value }}" {{ old('shipping_method', $order->shipping_method) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('shipping_method') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @if($order->payment_method === 'barion')
                    <div class="rounded-lg bg-stone-50 border border-stone-200 p-3 text-sm text-gray-700 space-y-1">
                        <p><span class="font-medium">Barion PaymentId:</span> {{ $order->barion_payment_id ?: '—' }}</p>
                        <p><span class="font-medium">Fizetés állapota:</span> {{ $order->payment_status ?: '—' }}</p>
                        <p>
                            <button type="button" class="btn-order-transactions text-emerald-700 hover:text-emerald-900 font-medium underline" data-order-id="{{ $order->id }}">
                                Tranzakciók megtekintése
                            </button>
                        </p>
                    </div>
                @endif
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-2">Fizetve</span>
                    <div class="space-y-2">
                        @foreach(\App\Models\Order::fizetveOptions() as $value => $label)
                            <label class="flex items-center gap-2 text-sm text-gray-800 cursor-pointer">
                                <input
                                    type="radio"
                                    name="fizetve"
                                    value="{{ $value }}"
                                    class="rounded-full border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                    {{ old('fizetve', $order->fizetve ?? '') === (string) $value ? 'checked' : '' }}
                                >
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    @error('fizetve') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="shipped" name="shipped" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" {{ old('shipped', $order->shipped) ? 'checked' : '' }}>
                    <label for="shipped" class="text-sm font-medium text-gray-700">Kiküldve</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="note">Megjegyzés</label>
                    <textarea id="note" name="note" rows="3" class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('note', $order->note) }}</textarea>
                    @error('note') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Megrendelt termékek (csak megjelenítés)</h2>
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

        <div class="flex justify-between items-center mt-4">
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Vissza a listához</a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">
                Megrendelés mentése
            </button>
        </div>
    </form>

    @include('admin.orders._transactions_modal')
@endsection

