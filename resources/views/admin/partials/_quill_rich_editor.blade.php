@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 280px; }
    .ql-editor img { max-width: 100%; height: auto; }
</style>
@endpush
@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var editors = @json($editors ?? []);
    var formSelector = @json($formSelector ?? 'form');
    var imageUploadUrl = @json(route('admin.content-images.store'));
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', function () {
        var quillInstances = [];

        function createImageHandler(quill) {
            return function () {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function () {
                    var file = input.files[0];
                    if (!file) return;

                    var formData = new FormData();
                    formData.append('image', file);

                    fetch(imageUploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        body: formData
                    })
                    .then(function (response) {
                        if (!response.ok) throw new Error('upload failed');
                        return response.json();
                    })
                    .then(function (data) {
                        var range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', data.url);
                        quill.setSelection(range.index + 1);
                    })
                    .catch(function () {
                        alert('A kép feltöltése sikertelen.');
                    });
                };
            };
        }

        editors.forEach(function (config) {
            var editorEl = document.getElementById(config.editorId);
            var textarea = document.getElementById(config.textareaId);
            if (!editorEl || !textarea) return;

            var toolbar = [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link']
            ];

            if (config.enableImages) {
                toolbar.push(['image']);
            }

            toolbar.push(['clean']);

            var quill = new Quill(editorEl, {
                theme: 'snow',
                modules: {
                    toolbar: toolbar
                }
            });

            if (config.enableImages) {
                quill.getModule('toolbar').addHandler('image', createImageHandler(quill));
            }

            if (textarea.value) {
                quill.root.innerHTML = textarea.value;
            }

            quill.on('text-change', function () {
                textarea.value = quill.root.innerHTML;
            });

            quillInstances.push({ quill: quill, textarea: textarea });
        });

        var form = document.querySelector(formSelector);
        if (form) {
            form.addEventListener('submit', function () {
                quillInstances.forEach(function (entry) {
                    entry.textarea.value = entry.quill.root.innerHTML;
                });
            });
        }
    });
})();
</script>
@endpush
