(function (global) {
    var DEFAULTS = {
        maxDimension: 1600,
        quality: 0.85,
        minQuality: 0.55,
        maxBytes: 1800000,
    };

    function readFile(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function (event) { resolve(event.target.result); };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function loadImage(src) {
        return new Promise(function (resolve, reject) {
            var img = new Image();
            img.onload = function () { resolve(img); };
            img.onerror = reject;
            img.src = src;
        });
    }

    function canvasToBlob(canvas, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) { resolve(blob); }, 'image/jpeg', quality);
        });
    }

    function scaledSize(width, height, maxDimension) {
        var max = Math.max(width, height);
        if (max <= maxDimension) {
            return { width: width, height: height };
        }

        var ratio = maxDimension / max;

        return {
            width: Math.round(width * ratio),
            height: Math.round(height * ratio),
        };
    }

    function resizedFileName(originalName) {
        var base = String(originalName || 'kep').replace(/\.[^.]+$/, '');

        return base + '.jpg';
    }

    function resizeImageFile(file, options) {
        var settings = Object.assign({}, DEFAULTS, options || {});

        if (!file || !String(file.type || '').match(/^image\//)) {
            return Promise.resolve(file);
        }

        return readFile(file)
            .then(loadImage)
            .then(function (img) {
                var size = scaledSize(img.width, img.height, settings.maxDimension);
                var canvas = document.createElement('canvas');
                canvas.width = size.width;
                canvas.height = size.height;

                var ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, size.width, size.height);
                ctx.drawImage(img, 0, 0, size.width, size.height);

                var quality = settings.quality;

                function attempt() {
                    return canvasToBlob(canvas, quality).then(function (blob) {
                        if (!blob) {
                            throw new Error('A kép optimalizálása sikertelen.');
                        }

                        if (blob.size <= settings.maxBytes || quality <= settings.minQuality) {
                            return new File(
                                [blob],
                                resizedFileName(file.name),
                                { type: 'image/jpeg', lastModified: Date.now() }
                            );
                        }

                        quality = Math.max(settings.minQuality, quality - 0.05);

                        return attempt();
                    });
                }

                return attempt();
            });
    }

    function replaceInputFile(input, file) {
        var transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
    }

    function bindProductImageForm(form) {
        if (!form || form.dataset.imageResizeBound === '1') {
            return;
        }

        form.dataset.imageResizeBound = '1';

        form.addEventListener('submit', function (event) {
            var input = form.querySelector('input[type="file"][name="image"]');
            if (!input || !input.files || !input.files.length) {
                return;
            }

            event.preventDefault();

            var submitter = event.submitter;
            if (submitter) {
                submitter.disabled = true;
            }

            resizeImageFile(input.files[0])
                .then(function (file) {
                    replaceInputFile(input, file);
                    form.submit();
                })
                .catch(function () {
                    if (submitter) {
                        submitter.disabled = false;
                    }
                    form.submit();
                });
        });
    }

    global.AdminImageResize = {
        resizeImageFile: resizeImageFile,
        replaceInputFile: replaceInputFile,
        bindProductImageForm: bindProductImageForm,
    };
}(window));
