@extends('layouts.admin')

@section('title', 'Szállítási módok')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Szállítási módok</h1>
        <p class="text-gray-500 text-sm mt-1">
            Csak az itt engedélyezett szállítási módok jelennek meg a webshop kosár felületén a rendelés véglegesítésekor.
        </p>
    </div>

    <form action="{{ route('admin.shipping-methods.update') }}" method="POST" class="max-w-xl space-y-5 bg-white rounded-xl shadow border border-gray-200 p-6">
        @csrf
        @method('PUT')

        @foreach(\App\Models\ShippingMethodSetting::METHODS as $key => $label)
            @php
                $column = $key . '_enabled';
                $descriptions = [
                    'mpl' => 'Magyar Posta Logisztika – házhozszállítás vagy postapont.',
                    'foxpost' => 'Automatás csomagátvétel Foxpost hálózaton.',
                    'dhl' => 'Nemzetközi és belföldi futárszolgálat.',
                    'gls' => 'GLS futárszolgálat – házhozszállítás vagy csomagpont.',
                    'packeta' => 'Z-BOX automatás csomagátvétel Packeta hálózaton.',
                ];
            @endphp
            <div class="flex items-start gap-3">
                <input type="hidden" name="{{ $column }}" value="0">
                <input
                    type="checkbox"
                    id="{{ $column }}"
                    name="{{ $column }}"
                    value="1"
                    class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    {{ old($column, $settings->{$column}) ? 'checked' : '' }}
                >
                <label for="{{ $column }}" class="text-sm text-gray-800">
                    <span class="font-medium block">{{ $label }}</span>
                    <span class="text-gray-500">{{ $descriptions[$key] ?? '' }}</span>
                </label>
            </div>
        @endforeach

        <div class="rounded-lg px-3 py-2 text-sm {{ count($settings->enabledMethods()) > 0 ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-amber-50 border border-amber-200 text-amber-900' }}">
            Jelenleg engedélyezve:
            @if(count($settings->enabledMethods()) > 0)
                <strong>{{ implode(', ', $settings->enabledMethods()) }}</strong>
            @else
                <strong>egy sem</strong> – a vásárlók nem választhatnak szállítási módot.
            @endif
        </div>

        <div class="border-t border-gray-200 pt-4 space-y-3">
            <h2 class="text-sm font-semibold text-gray-800">Átvételi pontok (automaták / csomagpontok)</h2>
            <p class="text-sm text-gray-500">
                A vásárlók ezekből a listákból választhatnak átvételi pontot. Az adatok a futárcégek nyilvános forrásaiból frissülnek.
            </p>
            <ul class="text-sm text-gray-700 space-y-1">
                @foreach(\App\Models\ShippingMethodSetting::METHODS as $key => $label)
                    <li>
                        <span class="font-medium">{{ $label }}:</span>
                        {{ number_format($pickupPointCounts[$key] ?? 0) }} pont
                    </li>
                @endforeach
            </ul>
            <form action="{{ route('admin.shipping-methods.sync-pickup-points') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-stone-700 hover:bg-stone-800 text-white font-medium rounded-lg">
                    Átvételi pontok frissítése
                </button>
            </form>
            <p class="text-xs text-gray-500">
                MPL pontokhoz állítsa be a <code class="text-xs">MPL_POSTINFO_URL</code> környezeti változót (PostInfo XML letöltési cím).
            </p>
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Vissza az adminhoz</a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">
                Mentés
            </button>
        </div>
    </form>
@endsection
