<div class="grid gap-8 md:grid-cols-2">
    @foreach($images as $image)
        <figure class="bg-white rounded-xl border border-emerald-100 shadow-sm overflow-hidden">
            <img src="{{ $image->imageUrl() }}" alt="{{ $image->title ?? 'Galéria kép' }}" class="w-full object-cover" style="max-height: 360px;">
            @if($image->title || $image->description)
                <figcaption class="p-4">
                    @if($image->title)
                        <p class="font-semibold text-emerald-900">{{ $image->title }}</p>
                    @endif
                    @if($image->description)
                        <p class="text-gray-600 text-sm mt-1">{{ $image->description }}</p>
                    @endif
                </figcaption>
            @endif
        </figure>
    @endforeach
</div>
