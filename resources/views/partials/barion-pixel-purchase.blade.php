@if(\App\Support\BarionBranding::hasPixel() && isset($order))
    @php
        $contents = collect($order->items ?? [])->map(function (array $item): array {
            $qty = (int) ($item['qty'] ?? 1);
            $price = (int) ($item['price'] ?? 0);

            return [
                'contentType' => 'Product',
                'currency' => 'HUF',
                'id' => (string) ($item['id'] ?? ''),
                'name' => (string) ($item['title'] ?? 'Termék'),
                'quantity' => $qty,
                'totalItemPrice' => $price * $qty,
                'unit' => 'db',
                'unitPrice' => $price,
            ];
        })->values()->all();
    @endphp
    <script>
        (function () {
            if (typeof window.bp !== 'function') {
                return;
            }

            bp('track', 'purchase', {
                contents: @json($contents),
                currency: 'HUF',
                orderNumber: @json((string) $order->id),
                revenue: {{ (int) $order->total_price }},
                step: 1
            });
        })();
    </script>
@endif
