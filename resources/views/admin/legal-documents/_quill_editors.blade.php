@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 280px; }
</style>
@endpush
@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var editors = [
        { editorId: 'aszf-editor', textareaId: 'aszf_content' },
        { editorId: 'shipping-terms-editor', textareaId: 'shipping_terms_content' }
    ];

    document.addEventListener('DOMContentLoaded', function () {
        var quillInstances = [];

        editors.forEach(function (config) {
            var editorEl = document.getElementById(config.editorId);
            var textarea = document.getElementById(config.textareaId);
            if (!editorEl || !textarea) return;

            var quill = new Quill(editorEl, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            if (textarea.value) {
                quill.root.innerHTML = textarea.value;
            }

            quill.on('text-change', function () {
                textarea.value = quill.root.innerHTML;
            });

            quillInstances.push({ quill: quill, textarea: textarea });
        });

        var form = document.querySelector('form[action="{{ route('admin.legal-documents.update') }}"]');
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
