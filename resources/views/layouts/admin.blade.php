<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – Triem Dragonherbs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Source Sans 3', sans-serif; } </style>
    @stack('head')
</head>
<body class="antialiased bg-stone-100 min-h-screen">
    <header class="bg-emerald-800 text-white shadow">
        <div class="max-w-5xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.products.index') }}" class="font-semibold">Admin – Termékek</a>
                <a href="{{ route('admin.orders.index') }}" class="text-emerald-100 hover:text-white text-sm">Megrendelések</a>
                <a href="{{ route('admin.barion.edit') }}" class="text-emerald-100 hover:text-white text-sm">Barion</a>
                <a href="{{ route('admin.webshop.edit') }}" class="text-emerald-100 hover:text-white text-sm">Webshop</a>
                <a href="{{ url('/') }}" class="text-emerald-200 hover:text-white text-sm">Főoldal</a>
            </div>
            <span class="text-emerald-200 text-sm">{{ auth()->user()->name }}</span>
        </div>
    </header>
    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 rounded-lg text-emerald-800">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
