<div class="modal fade" id="productImageModal" tabindex="-1" aria-labelledby="productImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-stone-900 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-display text-white text-xl" id="productImageModalLabel">Termék képe</h5>
                <div class="d-flex align-items-center gap-2 ms-auto me-2">
                    <button type="button" class="btn btn-sm btn-outline-light" id="productImageZoomOutBtn" aria-label="Kicsinyítés">−</button>
                    <button type="button" class="btn btn-sm btn-outline-light" id="productImageZoomInBtn" aria-label="Nagyítás">+</button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Bezárás"></button>
            </div>
            <div class="modal-body pt-2 pb-3">
                <div id="productImageLightboxViewport" class="product-image-lightbox-viewport rounded-lg">
                    <img id="productImageModalImg" src="" alt="" class="product-image-lightbox-img">
                </div>
                <p id="productImageLightboxHint" class="text-center text-stone-400 text-sm mt-3 mb-0">
                    Kattintson a képre, használja a +/− gombokat, vagy a görgőt · húzással/görgetéssel mozgatható
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .product-image-lightbox-trigger {
        cursor: zoom-in;
        display: block;
        width: 100%;
        padding: 0;
        border: 0;
        background: transparent;
        text-align: inherit;
    }

    .product-image-lightbox-trigger:focus-visible {
        outline: 2px solid #059669;
        outline-offset: 2px;
    }

    .product-image-lightbox-trigger img {
        pointer-events: none;
    }

    .product-image-lightbox-viewport {
        max-height: 78vh;
        overflow: auto;
        background: #1c1917;
        touch-action: pan-x pan-y;
        text-align: center;
    }

    .product-image-lightbox-img {
        display: inline-block;
        margin: 0 auto;
        max-width: 100%;
        max-height: 78vh;
        width: auto;
        height: auto;
        object-fit: contain;
        cursor: zoom-in;
        user-select: none;
        -webkit-user-drag: none;
    }

    .product-image-lightbox-img.is-dragging {
        cursor: grabbing;
    }

    .product-image-lightbox-img.is-zoomed {
        max-width: none;
        max-height: none;
        cursor: grab;
    }
</style>

<script>
    (function () {
        var modal = document.getElementById('productImageModal');
        var viewport = document.getElementById('productImageLightboxViewport');
        var img = document.getElementById('productImageModalImg');
        var hint = document.getElementById('productImageLightboxHint');
        var zoomInBtn = document.getElementById('productImageZoomInBtn');
        var zoomOutBtn = document.getElementById('productImageZoomOutBtn');
        if (!modal || !viewport || !img) return;

        var zoomLevel = 1;
        var minZoom = 1;
        var maxZoom = 4;
        var fitWidth = 0;
        var fitHeight = 0;
        var isDragging = false;
        var dragStartX = 0;
        var dragStartY = 0;
        var scrollStartX = 0;
        var scrollStartY = 0;

        function updateHint() {
            if (!hint) return;
            if (zoomLevel <= 1) {
                hint.textContent = 'Kattintson a képre, használja a +/− gombokat, vagy a görgőt · húzással/görgetéssel mozgatható';
            } else {
                hint.textContent = 'Nagyítás: ' + Math.round(zoomLevel * 100) + '% · kattintás vagy − a visszaállításhoz';
            }
        }

        function measureFitSize() {
            img.style.width = '';
            img.style.height = '';
            img.classList.remove('is-zoomed', 'is-dragging');
            fitWidth = img.getBoundingClientRect().width || img.offsetWidth || 0;
            fitHeight = img.getBoundingClientRect().height || img.offsetHeight || 0;
        }

        function applyZoomLevel() {
            if (fitWidth <= 0 || fitHeight <= 0) {
                measureFitSize();
            }
            if (fitWidth <= 0 || fitHeight <= 0) {
                return;
            }

            zoomLevel = Math.min(maxZoom, Math.max(minZoom, zoomLevel));

            if (zoomLevel <= 1) {
                img.style.width = '';
                img.style.height = '';
                img.classList.remove('is-zoomed');
                viewport.scrollLeft = 0;
                viewport.scrollTop = 0;
            } else {
                img.classList.add('is-zoomed');
                img.style.width = Math.round(fitWidth * zoomLevel) + 'px';
                img.style.height = 'auto';
                window.requestAnimationFrame(function () {
                    viewport.scrollLeft = Math.max(0, (viewport.scrollWidth - viewport.clientWidth) / 2);
                    viewport.scrollTop = Math.max(0, (viewport.scrollHeight - viewport.clientHeight) / 2);
                });
            }

            updateHint();
        }

        function resetZoom() {
            zoomLevel = 1;
            applyZoomLevel();
        }

        function zoomIn() {
            if (fitWidth <= 0) measureFitSize();
            if (zoomLevel <= 1) {
                zoomLevel = 2;
            } else {
                zoomLevel = Math.min(maxZoom, zoomLevel + 0.5);
            }
            applyZoomLevel();
        }

        function zoomOut() {
            if (zoomLevel <= 1) {
                resetZoom();
                return;
            }
            zoomLevel = Math.max(minZoom, zoomLevel - 0.5);
            applyZoomLevel();
        }

        function toggleZoom() {
            if (zoomLevel > 1) {
                resetZoom();
            } else {
                zoomIn();
            }
        }

        modal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var url = trigger.getAttribute('data-image-url') || '';
            var title = trigger.getAttribute('data-image-title') || 'Termék képe';
            var modalTitle = document.getElementById('productImageModalLabel');

            fitWidth = 0;
            fitHeight = 0;
            zoomLevel = 1;
            if (modalTitle) modalTitle.textContent = title;
            img.alt = title;

            function onReady() {
                measureFitSize();
                applyZoomLevel();
            }

            img.onload = onReady;
            img.src = url;
            if (img.complete) {
                onReady();
            }
        });

        modal.addEventListener('hidden.bs.modal', function () {
            img.onload = null;
            img.removeAttribute('src');
            fitWidth = 0;
            fitHeight = 0;
            resetZoom();
        });

        img.addEventListener('click', function (event) {
            if (isDragging) return;
            event.preventDefault();
            toggleZoom();
        });

        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                zoomIn();
            });
        }

        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                zoomOut();
            });
        }

        viewport.addEventListener('wheel', function (event) {
            if (!img.src) return;
            event.preventDefault();
            if (event.deltaY < 0) {
                zoomIn();
            } else {
                zoomOut();
            }
        }, { passive: false });

        img.addEventListener('mousedown', function (event) {
            if (zoomLevel <= 1) return;
            isDragging = false;
            dragStartX = event.clientX;
            dragStartY = event.clientY;
            scrollStartX = viewport.scrollLeft;
            scrollStartY = viewport.scrollTop;
            img.classList.add('is-dragging');

            function onMove(moveEvent) {
                var dx = moveEvent.clientX - dragStartX;
                var dy = moveEvent.clientY - dragStartY;
                if (Math.abs(dx) > 3 || Math.abs(dy) > 3) {
                    isDragging = true;
                }
                viewport.scrollLeft = scrollStartX - dx;
                viewport.scrollTop = scrollStartY - dy;
            }

            function onUp() {
                img.classList.remove('is-dragging');
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                window.setTimeout(function () {
                    isDragging = false;
                }, 0);
            }

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        });
    })();
</script>
