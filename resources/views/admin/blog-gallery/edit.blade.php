@extends('layouts.admin')

@section('title', 'Galéria')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Galéria</h1>
            <p class="text-sm text-gray-500 mt-1">Nyilvános oldal: <a href="{{ route('blog-gallery.index') }}" class="text-emerald-700 underline" target="_blank">Galéria megnyitása</a></p>
        </div>
    </div>

    <form action="{{ route('admin.blog-gallery.update') }}" method="POST" id="gallery-form" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-4 max-w-3xl">
            <h2 class="text-lg font-semibold text-gray-800">Beállítások</h2>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Galéria neve *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $gallery->name) }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="intro" class="block text-sm font-medium text-gray-700 mb-1">Rövid szöveg</label>
                <input type="text" name="intro" id="intro" value="{{ old('intro', $gallery->intro) }}" maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                @error('intro')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Leírás</label>
                <textarea name="description" id="description" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2">{{ old('description', $gallery->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700 mb-2">Megjelenítés *</span>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="display_mode" value="slider" {{ old('display_mode', $gallery->display_mode) !== 'list' ? 'checked' : '' }}>
                        <span>Slider (lapozós)</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="display_mode" value="list" {{ old('display_mode', $gallery->display_mode) === 'list' ? 'checked' : '' }}>
                        <span>Lista (rács)</span>
                    </label>
                </div>
                @error('display_mode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-6 space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Képek</h2>
                <p class="text-sm text-gray-500 mt-1">Egyszerre több képet is kiválaszthat. A cím és leírás nem kötelező.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Képek feltöltése</label>
                <input type="file" id="image-upload-input" accept="image/*" multiple class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-emerald-50 file:text-emerald-700">
                <p id="upload-status" class="text-sm mt-2 hidden"></p>
            </div>

            <div id="images-container" class="space-y-4"></div>

            @error('images')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg">Mentés</button>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Vissza</a>
        </div>
    </form>

    <template id="image-row-template">
        <div class="image-row border border-gray-200 rounded-lg p-4 bg-stone-50 flex flex-col md:flex-row gap-4" data-image-index="">
            <input type="hidden" class="image-id-input" name="" value="">
            <input type="hidden" class="image-path-input" name="" value="">

            <div class="shrink-0">
                <img src="" alt="" class="image-preview w-32 h-32 object-cover rounded border border-gray-200">
            </div>

            <div class="flex-1 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cím</label>
                        <input type="text" class="image-title-input w-full rounded-lg border border-gray-300 px-3 py-2" maxlength="255">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sorrend</label>
                        <input type="number" class="image-sort-input w-full rounded-lg border border-gray-300 px-3 py-2" value="0">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leírás</label>
                    <textarea rows="2" class="image-description-input w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
                </div>
            </div>

            <div class="shrink-0 flex md:flex-col justify-end">
                <button type="button" class="remove-image-btn text-sm text-red-600 hover:underline">Törlés</button>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
<script>
(function () {
    var imagesContainer = document.getElementById('images-container');
    var rowTemplate = document.getElementById('image-row-template');
    var uploadInput = document.getElementById('image-upload-input');
    var uploadStatus = document.getElementById('upload-status');
    var galleryForm = document.getElementById('gallery-form');
    var initialImages = @json($initialImages);
    var uploadUrl = @json(route('admin.blog-gallery-images.store'));
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function setUploadStatus(message, isError) {
        uploadStatus.textContent = message;
        uploadStatus.classList.remove('hidden', 'text-red-600', 'text-emerald-700');
        uploadStatus.classList.add(isError ? 'text-red-600' : 'text-emerald-700');
    }

    function createImageRow(index, data) {
        data = data || {};
        var fragment = rowTemplate.content.cloneNode(true);
        var row = fragment.querySelector('.image-row');
        row.dataset.imageIndex = index;

        row.querySelector('.image-id-input').name = 'images[' + index + '][id]';
        row.querySelector('.image-id-input').value = data.id || '';

        row.querySelector('.image-path-input').name = 'images[' + index + '][stored_image_path]';
        row.querySelector('.image-path-input').value = data.stored_image_path || '';

        row.querySelector('.image-title-input').name = 'images[' + index + '][title]';
        row.querySelector('.image-title-input').value = data.title || '';

        row.querySelector('.image-description-input').name = 'images[' + index + '][description]';
        row.querySelector('.image-description-input').value = data.description || '';

        row.querySelector('.image-sort-input').name = 'images[' + index + '][sort_order]';
        row.querySelector('.image-sort-input').value = data.sort_order ?? index;

        if (data.image_url) {
            row.querySelector('.image-preview').src = data.image_url;
        }

        row.querySelector('.remove-image-btn').addEventListener('click', function () {
            row.remove();
            reindexRows();
        });

        return row;
    }

    function addImageRow(data) {
        var index = imagesContainer.querySelectorAll('.image-row').length;
        imagesContainer.appendChild(createImageRow(index, data));
    }

    function reindexRows() {
        imagesContainer.querySelectorAll('.image-row').forEach(function (row, index) {
            row.dataset.imageIndex = index;
            row.querySelector('.image-id-input').name = 'images[' + index + '][id]';
            row.querySelector('.image-path-input').name = 'images[' + index + '][stored_image_path]';
            row.querySelector('.image-title-input').name = 'images[' + index + '][title]';
            row.querySelector('.image-description-input').name = 'images[' + index + '][description]';
            row.querySelector('.image-sort-input').name = 'images[' + index + '][sort_order]';
        });
    }

    function uploadFiles(files) {
        if (!files.length) {
            return;
        }

        setUploadStatus('Képek feltöltése (' + files.length + ' db)...', false);

        var formData = new FormData();
        Array.prototype.forEach.call(files, function (file) {
            formData.append('images[]', file);
        });

        fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(function (response) {
            return response.text().then(function (text) {
                var data = {};
                try {
                    data = text ? JSON.parse(text) : {};
                } catch (e) {
                    data = { message: text || 'Ismeretlen szerverhiba.' };
                }

                if (!response.ok) {
                    var message = data.message
                        || (data.errors && data.errors.images && data.errors.images[0])
                        || ('HTTP ' + response.status);
                    throw new Error(message);
                }

                return data;
            });
        })
        .then(function (data) {
            var startIndex = imagesContainer.querySelectorAll('.image-row').length;
            (data.images || []).forEach(function (image, offset) {
                addImageRow({
                    stored_image_path: image.path,
                    image_url: image.url,
                    sort_order: startIndex + offset
                });
            });
            setUploadStatus('Feltöltve: ' + (data.images || []).length + ' kép. Ne felejtse el menteni!', false);
            uploadInput.value = '';
        })
        .catch(function (error) {
            setUploadStatus(error.message || 'A feltöltés sikertelen.', true);
        });
    }

    uploadInput.addEventListener('change', function (event) {
        uploadFiles(event.target.files);
    });

    initialImages.forEach(function (imageData) {
        addImageRow(imageData);
    });

    if (galleryForm) {
        galleryForm.addEventListener('submit', function () {
            reindexRows();
        });
    }
})();
</script>
@endpush
