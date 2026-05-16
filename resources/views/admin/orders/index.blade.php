@extends('layouts.admin')

@section('title', 'Megrendelések')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Megrendelések</h1>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Név</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Összeg</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fizetés</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fizetve</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kiküldve</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dátum</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Műveletek</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">#{{ $order->id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $order->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $order->phone }}</td>
                            <td class="px-4 py-3 text-gray-800 font-semibold">{{ number_format($order->total_price) }} Ft</td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $order->paymentMethodLabel() }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 text-sm">
                                {{ $order->fizetveLabel() }}
                            </td>
                            <td class="px-4 py-3">
                                @if($order->shipped)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Igen</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nem</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-sm">{{ $order->created_at->format('Y.m.d H:i') }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <button type="button" class="btn-order-transactions text-violet-600 hover:text-violet-800 text-sm font-medium" data-order-id="{{ $order->id }}">Tranzakció</button>
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Megnyitás</a>
                                <a href="{{ route('admin.orders.edit', $order) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Szerkesztés</a>
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Biztosan törlöd ezt a megrendelést?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Törlés</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">Még nincs megrendelés.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    @include('admin.orders._transactions_modal')
@endsection

