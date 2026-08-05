@push('scripts')
<script src="{{ asset('js/admin-image-resize.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-auto-resize-image="1"]').forEach(function (form) {
        if (window.AdminImageResize) {
            window.AdminImageResize.bindProductImageForm(form);
        }
    });
});
</script>
@endpush
