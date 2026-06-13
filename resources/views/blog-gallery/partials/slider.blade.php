@if($images->count() === 1)
    @php($image = $images->first())
    <div class="mb-6">
        <img src="{{ $image->imageUrl() }}" alt="{{ $image->title ?? 'Galéria kép' }}" class="w-full rounded-xl shadow-md">
        @if($image->title || $image->description)
            <div class="mt-3">
                @if($image->title)
                    <p class="font-semibold text-emerald-900">{{ $image->title }}</p>
                @endif
                @if($image->description)
                    <p class="text-gray-600 text-sm mt-1">{{ $image->description }}</p>
                @endif
            </div>
        @endif
    </div>
@else
    <div id="gallery-carousel" class="carousel slide mb-6" data-bs-ride="false">
        <div class="carousel-inner rounded-xl overflow-hidden shadow-md">
            @foreach($images as $index => $image)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ $image->imageUrl() }}" alt="{{ $image->title ?? 'Galéria kép' }}" class="d-block w-100" style="max-height: 560px; object-fit: contain; background: #f5f5f4;">
                    @if($image->title || $image->description)
                        <div class="carousel-caption d-none d-md-block p-3 rounded" style="background: rgba(0,0,0,.65);">
                            @if($image->title)
                                <h5 class="mb-1">{{ $image->title }}</h5>
                            @endif
                            @if($image->description)
                                <p class="mb-0 small">{{ $image->description }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#gallery-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Előző</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#gallery-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Következő</span>
        </button>
    </div>
@endif
