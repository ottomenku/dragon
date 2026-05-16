<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webshop – Triem Dragonherbs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:500,600|source-sans-3:400,500,600&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="antialiased bg-stone-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow border border-emerald-100 p-8 text-center">
        <h1 class="font-display text-3xl text-emerald-900 font-semibold mb-4">Webshop</h1>
        <p class="text-gray-700 text-lg leading-relaxed">
            Sajnáljuk, a webshop jelenleg nem üzemel.
        </p>
        <a href="{{ route('welcome') }}" class="mt-8 inline-flex items-center justify-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-medium rounded-lg w-full">
            Vissza a főoldalra
        </a>
    </div>
</body>
</html>
