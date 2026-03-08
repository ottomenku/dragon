<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Triem Dragonherbs')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="antialiased bg-stone-100 min-h-screen flex flex-col">
    <div class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <a href="{{ url('/') }}" class="block text-center font-display text-2xl text-emerald-800 hover:text-emerald-900 mb-8">Triem Dragonherbs</a>
            <div class="bg-white rounded-2xl shadow-lg border border-emerald-100 p-6 md:p-8">
                @yield('content')
            </div>
            <p class="text-center mt-6 text-sm text-gray-500">
                @yield('footer_link')
            </p>
        </div>
    </div>
</body>
</html>
