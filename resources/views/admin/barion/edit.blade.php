@extends('layouts.admin')

@section('title', 'Barion beállítások')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Barion beállítások</h1>
        <p class="text-gray-500 text-sm mt-1">
            Az elfogadóhely jóváhagyásához a Barion Pixel ID megadása kötelező (Alap verzió). A kártyás fizetéshez Payee és POSKey szükséges.
        </p>
    </div>

    <form action="{{ route('admin.barion.update') }}" method="POST" class="max-w-xl space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Kártyás fizetés</h2>
                <p class="text-xs text-gray-500 mt-1">Csak akkor szükséges, ha a Fizetési módok menüben engedélyezve van a Barion kártyás fizetés.</p>
            </div>

            <div>
                <label for="payee" class="block text-sm font-medium text-gray-700 mb-1">Payee (Barion pénztárca e-mail)</label>
                <input
                    type="email"
                    id="payee"
                    name="payee"
                    value="{{ old('payee', $settings->payee) }}"
                    class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                    autocomplete="off"
                >
                @error('payee')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pos_key" class="block text-sm font-medium text-gray-700 mb-1">POSKey</label>
                <input
                    type="password"
                    id="pos_key"
                    name="pos_key"
                    value=""
                    class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 font-mono text-sm"
                    placeholder="{{ $settings->exists && $settings->pos_key ? '•••••• (új kulcs megadásához írjon ide)' : 'A Barion üzleti felületén generált POSKey' }}"
                    autocomplete="new-password"
                >
                @error('pos_key')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Ha már van mentett kulcs, hagyja üresen a mezőt, ha nem szeretné cserélni. A POSKey titkosítva kerül tárolásra.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="use_test" value="0">
                <input
                    type="checkbox"
                    id="use_test"
                    name="use_test"
                    value="1"
                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    {{ old('use_test', $settings->use_test) ? 'checked' : '' }}
                >
                <label for="use_test" class="text-sm font-medium text-gray-700">Teszt (sandbox) környezet</label>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Barion Pixel</h2>
                <p class="text-xs text-gray-500 mt-1">Csalásmegelőzés és eseménykövetés a webshopban. A kártyás fizetéstől független.</p>
            </div>

            <div>
                <label for="pixel_id" class="block text-sm font-medium text-gray-700 mb-1">Barion Pixel ID</label>
                <input
                    type="text"
                    id="pixel_id"
                    name="pixel_id"
                    value="{{ old('pixel_id', $settings->pixel_id) }}"
                    class="w-full rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 font-mono text-sm"
                    placeholder="BP-0000000000-00"
                    autocomplete="off"
                >
                @error('pixel_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    A Barion Wallet → Üzlet → Részletek menüpontban található. Ha meg van adva, a webshop oldalain betöltjük a követő kódot.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="pixel_footer_enabled" value="0">
                <input
                    type="checkbox"
                    id="pixel_footer_enabled"
                    name="pixel_footer_enabled"
                    value="1"
                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    {{ old('pixel_footer_enabled', $settings->pixel_footer_enabled) ? 'checked' : '' }}
                >
                <label for="pixel_footer_enabled" class="text-sm font-medium text-gray-700">Barion logók megjelenítése a láblécben</label>
            </div>
            <p class="text-xs text-gray-500 -mt-3 ml-6">Ha be van jelölve, a főoldal és webshop láblécében megjelennek az elfogadott fizetési mód logók.</p>
        </div>

        <div class="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2 text-sm text-amber-900">
            A visszatérési és értesítő URL-ek automatikusan a webhely címéből épülnek fel (APP_URL / aktuális host). Élesben ellenőrizze, hogy a publikus URL elérhető a Barion számára.
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Vissza az adminhoz</a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">
                Mentés
            </button>
        </div>
    </form>
@endsection
