<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fizetés eredménye – Triem Dragonherbs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Source Sans 3', sans-serif; } </style>
</head>
<body class="antialiased bg-stone-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow border border-stone-200 p-8 text-center">
        @if ($state === 'success')
            <div class="text-emerald-600 text-5xl mb-3" aria-hidden="true">✓</div>
            <h1 class="text-xl font-semibold text-gray-900 mb-2">Sikeres fizetés</h1>
            <p class="text-gray-600 text-sm mb-6">Köszönjük a megrendelést. A kártyás fizetés teljesült.</p>
            @if($order)
                <p class="text-gray-800 text-sm">Megrendelés azonosító: <strong>#{{ $order->id }}</strong></p>
            @endif
        @elseif ($state === 'failed')
            <div class="text-red-600 text-5xl mb-3" aria-hidden="true">✕</div>
            <h1 class="text-xl font-semibold text-gray-900 mb-2">Sikertelen fizetés</h1>
            <p class="text-gray-600 text-sm mb-6">A fizetés nem sikerült vagy megszakadt. A megrendelés rögzítve maradt; szükség esetén vegye fel velünk a kapcsolatot.</p>
            @if($order)
                <p class="text-gray-800 text-sm">Megrendelés azonosító: <strong>#{{ $order->id }}</strong></p>
            @endif
        @elseif ($state === 'pending')
            <div class="text-amber-500 text-5xl mb-3" aria-hidden="true">…</div>
            <h1 class="text-xl font-semibold text-gray-900 mb-2">Fizetés feldolgozás alatt</h1>
            <p class="text-gray-600 text-sm mb-6">A tranzakció még nem zárult le véglegesen. Ha a terhelés megjelent a számláján, hamarosan frissül az állapot; egyébként próbálja újra a fizetést.</p>
            @if($order)
                <p class="text-gray-800 text-sm">Megrendelés azonosító: <strong>#{{ $order->id }}</strong></p>
            @endif
        @else
            <h1 class="text-xl font-semibold text-gray-900 mb-2">Ismeretlen állapot</h1>
            <p class="text-gray-600 text-sm">{{ $message ?? 'Nem sikerült azonosítani a fizetést.' }}</p>
        @endif

        <div class="mt-8">
            @if($webshopLinkVisible ?? false)
                <a href="{{ route('webshop') }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-lg w-full">
                    Vissza a webshophoz
                </a>
            @else
                <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-lg w-full">
                    Vissza a főoldalra
                </a>
            @endif
        </div>
    </div>
</body>
</html>
