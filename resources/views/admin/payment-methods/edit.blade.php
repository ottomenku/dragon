@extends('layouts.admin')

@section('title', 'Fizetési módok')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Fizetési módok</h1>
        <p class="text-gray-500 text-sm mt-1">
            Csak az itt engedélyezett fizetési módok jelennek meg a webshop kosár felületén a rendelés véglegesítésekor.
        </p>
    </div>

    <form action="{{ route('admin.payment-methods.update') }}" method="POST" class="max-w-xl space-y-5 bg-white rounded-xl shadow border border-gray-200 p-6">
        @csrf
        @method('PUT')

        @foreach(\App\Models\PaymentMethodSetting::METHODS as $key => $label)
            @php
                $column = $key . '_enabled';
                $descriptions = [
                    'cod' => 'Készpénz vagy kártya a futárnál.',
                    'otp' => 'OTP SimplePay kártyás fizetés (banki bekötés után).',
                    'barion' => 'Online kártyás fizetés a Barion rendszerén keresztül.',
                ];
            @endphp
            <div class="flex items-start gap-3">
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
                <strong>egy sem</strong> – a vásárlók nem választhatnak fizetési módot.
            @endif
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Vissza az adminhoz</a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">
                Mentés
            </button>
        </div>
    </form>
@endsection
