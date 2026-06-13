<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Galéria') – Triem Dragonherbs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #047857;
            border-radius: 9999px;
            background-size: 60% 60%;
        }
        .carousel-control-prev,
        .carousel-control-next { width: 3rem; }
    </style>
    @include('partials.barion-pixel')
</head>
<body class="antialiased text-gray-800 bg-stone-50 min-h-screen flex flex-col">
    <header class="bg-emerald-900 text-white shadow">
        <div class="max-w-5xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('welcome') }}" class="font-display text-2xl font-semibold hover:text-emerald-200">Triem Dragonherbs</a>
            <a href="{{ route('welcome') }}" class="text-sm text-emerald-200 hover:text-white underline">Nyitólap</a>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-emerald-900 text-emerald-50 py-8 mt-10">
        <div class="max-w-5xl mx-auto px-4 text-sm text-emerald-200/90">
            © {{ date('Y') }} Triem Dragonherbs
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
