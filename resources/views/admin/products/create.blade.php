@extends('layouts.admin')

@section('title', 'Új termék')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">← Termékek</a>
    </div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Új termék</h1>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" data-auto-resize-image="1" class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-4">
        @csrf

        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Kép</label>
            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-emerald-50 file:text-emerald-700">
            <p class="mt-1 text-xs text-gray-500">Bármilyen méretű fotó feltölhető – a rendszer automatikusan átméretezi és optimalizálja.</p>
            @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Cím *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" maxlength="255" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategória *</label>
            <select name="category" id="category" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                <option value="gyogyteak" {{ old('category', 'gyogyteak') === 'gyogyteak' ? 'selected' : '' }}>Gyógyteák</option>
                <option value="illoolajok" {{ old('category') === 'illoolajok' ? 'selected' : '' }}>Illóolajok</option>
                <option value="kozmetikumok" {{ old('category') === 'kozmetikumok' ? 'selected' : '' }}>Kozmetikumok</option>
            </select>
            @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="intro" class="block text-sm font-medium text-gray-700 mb-1">Intro (max 255 karakter) *</label>
            <input type="text" name="intro" id="intro" value="{{ old('intro') }}" maxlength="255" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            @error('intro')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="moreinfo" class="block text-sm font-medium text-gray-700 mb-1">Részletes leírás (richtext)</label>
            <div id="moreinfo-editor" class="min-h-[200px] bg-white border border-gray-300 rounded-lg overflow-hidden"></div>
            <textarea name="moreinfo" id="moreinfo" class="hidden">{{ old('moreinfo') }}</textarea>
            @error('moreinfo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="ar" class="block text-sm font-medium text-gray-700 mb-1">Ár (Ft) *</label>
                <input type="number" name="ar" id="ar" value="{{ old('ar', 0) }}" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                @error('ar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="kedv" class="block text-sm font-medium text-gray-700 mb-1">Kedv (egész) *</label>
                <input type="number" name="kedv" id="kedv" value="{{ old('kedv', 0) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                @error('kedv')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center">
            <input type="hidden" name="public" value="0">
            <input type="checkbox" name="public" id="public" value="1" {{ old('public', true) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            <label for="public" class="ml-2 text-sm text-gray-700">Publikus (látható)</label>
        </div>
        @error('public')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

        <div class="flex items-center">
            <input type="hidden" name="tomain" value="0">
            <input type="checkbox" name="tomain" id="tomain" value="1" {{ old('tomain', false) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            <label for="tomain" class="ml-2 text-sm text-gray-700">Nyitólapra</label>
        </div>
        @error('tomain')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

        <div class="pt-4">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">Mentés</button>
        </div>
    </form>

    @include('admin.products._quill_editor')
    @include('admin.partials._image_resize_script')
@endsection
