@if(\App\Support\BarionBranding::hasPixel())
    @php($barionPixelId = \App\Support\BarionBranding::pixelId())
    <script>
        window["bp"] = window["bp"] || function () {
            (window["bp"].q = window["bp"].q || []).push(arguments);
        };
        window["bp"].l = 1 * new Date();
        (function () {
            var scriptElement = document.createElement("script");
            var firstScript = document.getElementsByTagName("script")[0];
            scriptElement.async = true;
            scriptElement.src = "https://pixel.barion.com/bp.js";
            if (firstScript && firstScript.parentNode) {
                firstScript.parentNode.insertBefore(scriptElement, firstScript);
            } else {
                document.head.appendChild(scriptElement);
            }
        })();
        bp("init", "addBarionPixelId", @json($barionPixelId));
    </script>
@endif
