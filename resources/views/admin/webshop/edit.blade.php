@extends('layouts.admin')

@section('title', 'Webshop beállítások')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Webshop üzemeltetés</h1>
        <p class="text-gray-500 text-sm mt-1">
            Kikapcsoláskor a webshop link nem jelenik meg a főoldalon, a vásárlók nem érhetik el a webshopot és nem adhatnak le rendelést.
            Bejelentkezett adminok továbbra is tesztelhetik.
        </p>
    </div>

    <form action="{{ route('admin.webshop.update') }}" method="POST" class="max-w-xl space-y-5 bg-white rounded-xl shadow border border-gray-200 p-6">
        @csrf
        @method('PUT')

        <div class="flex items-start gap-3">
            <input type="hidden" name="enabled" value="0">
            <input
                type="checkbox"
                id="enabled"
                name="enabled"
                value="1"
                class="mt-1 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                {{ old('enabled', $settings->enabled) ? 'checked' : '' }}
            >
            <label for="enabled" class="text-sm text-gray-800">
                <span class="font-medium block">Webshop üzemben</span>
                <span class="text-gray-500">Ha nincs bejelölve, a nyilvános webshop és rendelésfelvétel leáll.</span>
            </label>
        </div>

        <div class="rounded-lg px-3 py-2 text-sm {{ $settings->enabled ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-amber-50 border border-amber-200 text-amber-900' }}">
            Jelenlegi állapot:
            <strong>{{ $settings->enabled ? 'Nyitva (mindenki számára)' : 'Zárva (csak adminok)' }}</strong>
        </div>

        <div class="flex justify-between items-center pt-2">
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Vissza az adminhoz</a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">
                Mentés
            </button>
        </div>
    </form>
@endsection
