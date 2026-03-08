{{-- Quill rich text: betűszín, háttérszín, betűtípus, bold, italic, listák, link --}}
@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor { min-height: 200px; }
    .ql-font-sans-serif { font-family: 'Source Sans 3', sans-serif; }
    .ql-font-serif { font-family: Georgia, 'Times New Roman', serif; }
    .ql-font-monospace { font-family: 'Courier New', monospace; }
    .ql-font-arial { font-family: Arial, sans-serif; }
    .ql-font-georgia { font-family: Georgia, serif; }
    .ql-font-trebuchet { font-family: 'Trebuchet MS', sans-serif; }
    .ql-font-verdana { font-family: Verdana, sans-serif; }
</style>
@endpush
@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function() {
    var allowedFonts = ['sans-serif', 'serif', 'monospace', 'arial', 'georgia', 'trebuchet', 'verdana'];
    var Font = Quill.import('formats/font');
    Font.whitelist = allowedFonts;
    Quill.register(Font, true);

    var colorPalette = [
        '#000000', '#333333', '#666666', '#999999', '#cccccc', '#ffffff',
        '#e60000', '#e66600', '#e6c200', '#00e600', '#00e6e6', '#0066e6', '#c200e6',
        '#2d5016', '#0d4d2d', '#1a4d4d', '#1a3d4d', '#2d2d4d', '#4d2d4d'
    ];
    var bgPalette = [
        '#ffffff', '#f2f2f2', '#e6e6e6', '#ffebeb', '#fff5e6', '#fffde6',
        '#e6ffe6', '#e6ffff', '#e6f2ff', '#ebe6ff', '#fce6ff'
    ];

    document.addEventListener('DOMContentLoaded', function() {
        var editorEl = document.getElementById('moreinfo-editor');
        var textarea = document.getElementById('moreinfo');
        if (!editorEl || !textarea) return;

        var quill = new Quill(editorEl, {
            theme: 'snow',
            placeholder: 'Részletes leírás...',
            modules: {
                toolbar: [
                    [{ 'font': allowedFonts }],
                    ['bold', 'italic', 'underline'],
                    [{ 'color': colorPalette }, { 'background': bgPalette }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        if (textarea.value) quill.root.innerHTML = textarea.value;
        quill.on('text-change', function() { textarea.value = quill.root.innerHTML; });
        var form = editorEl.closest('form');
        if (form) form.addEventListener('submit', function() { textarea.value = quill.root.innerHTML; });
    });
})();
</script>
@endpush
