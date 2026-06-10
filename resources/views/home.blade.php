<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kezdőlap – Triem Dragonherbs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="antialiased bg-stone-50 min-h-screen">
    <header class="bg-emerald-800 text-white shadow">
        <div class="max-w-4xl mx-auto px-4 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <a href="{{ url('/') }}" class="font-display text-xl font-semibold hover:text-emerald-200">Triem Dragonherbs</a>
            <nav class="flex items-center gap-4">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.products.index') }}" class="text-emerald-200 hover:text-white font-medium">Admin</a>
                @endif
                <span class="text-emerald-200">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-emerald-200 hover:text-white underline">Kijelentkezés</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-12">
        <h1 class="font-display text-3xl font-semibold text-emerald-900 mb-2">Üdvözöljük, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600">Sikeresen bejelentkezett a Triem Dragonherbs rendszerbe.</p>
        <div class="mt-8 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
            <p class="text-emerald-800">Itt később megjelenhetnek a webshop funkciók, rendelések és egyéb bejelentkezett felhasználói tartalmak.</p>
        </div>
    </main>
</body>
</html>
