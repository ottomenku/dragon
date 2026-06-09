@extends('layouts.admin')

@section('title', 'ÁSZF, szállítás és adatkezelés')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">ÁSZF, szállítási feltételek és adatkezelés</h1>
        <p class="text-gray-500 text-sm mt-1">
            Ezek a szövegek jelennek meg a webshop rendelési felugró ablakában és a nyitóoldalon. A vásárlónak a rendelés leadása előtt el kell fogadnia az ÁSZF-et és a szállítási feltételeket.
        </p>
    </div>

    <form action="{{ route('admin.legal-documents.update') }}" method="POST" class="max-w-4xl space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-3">
            <label for="aszf_content" class="block text-sm font-semibold text-gray-800">Általános Szerződési Feltételek (ÁSZF)</label>
            <div id="aszf-editor" class="bg-white"></div>
            <textarea id="aszf_content" name="aszf_content" class="hidden">{{ old('aszf_content', $settings->aszf_content) }}</textarea>
            @error('aszf_content')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-3">
            <label for="shipping_terms_content" class="block text-sm font-semibold text-gray-800">Szállítási feltételek</label>
            <div id="shipping-terms-editor" class="bg-white"></div>
            <textarea id="shipping_terms_content" name="shipping_terms_content" class="hidden">{{ old('shipping_terms_content', $settings->shipping_terms_content) }}</textarea>
            @error('shipping_terms_content')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-3">
            <label for="gdpr_content" class="block text-sm font-semibold text-gray-800">Adatkezelési tájékoztató (GDPR)</label>
            <div id="gdpr-editor" class="bg-white"></div>
            <textarea id="gdpr_content" name="gdpr_content" class="hidden">{{ old('gdpr_content', $settings->gdpr_content) }}</textarea>
            @error('gdpr_content')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Vissza az adminhoz</a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">
                Mentés
            </button>
        </div>
    </form>
@endsection

@include('admin.legal-documents._quill_editors')
