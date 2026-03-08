@extends('layouts.auth')

@section('title', 'Bejelentkezés – Triem Dragonherbs')

@section('content')
    <h1 class="font-display text-2xl font-semibold text-emerald-900 mb-6">Bejelentkezés</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Jelszó</label>
            <input type="password" name="password" id="password" required autocomplete="current-password"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('password') border-red-500 @enderror">
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            <label for="remember" class="ml-2 text-sm text-gray-600">Emlékezz rám</label>
        </div>
        <button type="submit" class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg transition-colors">
            Bejelentkezés
        </button>
    </form>
@endsection

@section('footer_link')
    Még nincs fiókja? <a href="{{ route('register') }}" class="text-emerald-700 hover:text-emerald-900 font-medium">Regisztráció</a>
@endsection
