@extends('blog-gallery.layout')

@section('title', $gallery->name)

@section('content')
    <section class="max-w-5xl mx-auto px-4 py-12">
        <h1 class="font-display text-4xl font-semibold text-emerald-900 mb-3">{{ $gallery->name }}</h1>

        @if($gallery->intro)
            <p class="text-lg text-emerald-800 mb-4">{{ $gallery->intro }}</p>
        @endif

        @if($gallery->description)
            <div class="text-gray-700 leading-relaxed mb-10 whitespace-pre-line">{{ $gallery->description }}</div>
        @endif

        @if($gallery->images->isEmpty())
            <p class="text-gray-500">A galéria még üres.</p>
        @elseif($gallery->isSlider())
            @include('blog-gallery.partials.slider', ['images' => $gallery->images])
        @else
            @include('blog-gallery.partials.list', ['images' => $gallery->images])
        @endif
    </section>
@endsection
