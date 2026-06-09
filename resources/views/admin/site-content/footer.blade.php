@extends('layouts.admin')

@section('title', 'Lábléc')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Lábléc szerkesztése</h1>
        <p class="text-gray-500 text-sm mt-1">
            A főoldal láblécében megjelenő tartalom. Az ÁSZF / szállítási / adatkezelési linkek és a Barion fizetési logók változatlanok maradnak.
        </p>
    </div>

    <form action="{{ route('admin.site-content.footer.update') }}" method="POST" class="max-w-4xl space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-3">
            <label for="footer_content" class="block text-sm font-semibold text-gray-800">Lábléc tartalom</label>
            <div id="footer-editor" class="bg-white"></div>
            <textarea id="footer_content" name="footer_content" class="hidden">{{ old('footer_content', $settings->footer_content) }}</textarea>
            @error('footer_content')
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

@php
    $editors = [
        ['editorId' => 'footer-editor', 'textareaId' => 'footer_content', 'enableImages' => true],
    ];
    $formSelector = 'form[action="' . route('admin.site-content.footer.update') . '"]';
@endphp
@include('admin.partials._quill_rich_editor')
