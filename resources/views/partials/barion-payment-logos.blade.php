@if(\App\Support\BarionBranding::shouldShowBranding($context ?? 'footer'))
    @php
        $logoVariant = $variant ?? (($context ?? 'footer') === 'checkout' ? 'light' : 'dark');
        $logoFile = $logoVariant === 'light'
            ? 'barion-payment-banner-medium-light.png'
            : 'barion-payment-banner-medium-dark.png';
    @endphp
    <div class="{{ $class ?? '' }}">
        <p class="text-xs {{ $labelClass ?? 'text-gray-500' }} mb-2">{{ $label ?? 'Elfogadott online fizetési módok:' }}</p>
        <img
            src="{{ asset('img/barion/' . $logoFile) }}"
            alt="Barion – Visa, Mastercard, Apple Pay, Google Pay"
            class="max-w-full h-auto"
            loading="lazy"
        >
    </div>
@endif
