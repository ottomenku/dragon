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
            <div class="flex items-center gap-4 flex-wrap">
                <a href="{{ route('admin.products.index') }}" class="font-semibold">Admin – Termékek</a>
                @include('partials.admin-menu-links')
                <a href="{{ url('/') }}" class="text-emerald-200 hover:text-white text-sm">Főoldal</a>
            </div>
            <span class="text-emerald-200 text-sm">{{ auth()->user()->name }}</span>
        </div>
    </header>
    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('success'))
            <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 rounded-lg text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="mb-4 p-3 bg-amber-100 border border-amber-300 rounded-lg text-amber-900">{{ session('warning') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 rounded-lg text-red-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
