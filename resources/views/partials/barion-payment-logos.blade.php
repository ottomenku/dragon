@if(\App\Support\BarionBranding::showPaymentLogos())
    <div class="{{ $class ?? '' }}">
        <p class="text-xs {{ $labelClass ?? 'text-gray-500' }} mb-2">{{ $label ?? 'Elfogadott online fizetési módok:' }}</p>
        <img
            src="{{ asset('img/barion/barion-card-strip-intl__medium.png') }}"
            alt="Barion – Visa, Mastercard, Maestro, American Express, Google Pay, Apple Pay"
            class="max-w-full h-auto"
            loading="lazy"
        >
    </div>
@endif
