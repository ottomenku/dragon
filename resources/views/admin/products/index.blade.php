@extends('layouts.admin')

@section('title', 'Termékek')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Termékek</h1>
        <a href="{{ route('admin.products.create') }}" class="inline-flex justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">Új termék</a>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kép</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cím</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ár</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kedv</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Publikus</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Műveletek</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="" class="w-12 h-12 object-cover rounded">
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $product->title }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ number_format($product->ar) }} Ft</td>
                            <td class="px-4 py-3 text-gray-600">{{ $product->kedv }}</td>
                            <td class="px-4 py-3">
                                @if($product->public)
                                    <span class="text-emerald-600">Igen</span>
                                @else
                                    <span class="text-gray-400">Nem</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-emerald-600 hover:text-emerald-800 font-medium">Szerkesztés</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Törölni szeretnéd ezt a terméket?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Törlés</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Még nincs termék.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
