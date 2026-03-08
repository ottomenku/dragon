@extends('layouts.auth')

@section('title', 'Regisztráció – Triem Dragonherbs')

@section('content')
    <h1 class="font-display text-2xl font-semibold text-emerald-900 mb-6">Regisztráció</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Név</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Jelszó</label>
            <input type="password" name="password" id="password" required autocomplete="new-password"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('password') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-500">Legalább 8 karakter</p>
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Jelszó megerősítése</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
        </div>
        <button type="submit" class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg transition-colors">
            Regisztráció
        </button>
    </form>
@endsection

@section('footer_link')
    Már van fiókja? <a href="{{ route('login') }}" class="text-emerald-700 hover:text-emerald-900 font-medium">Bejelentkezés</a>
@endsection
