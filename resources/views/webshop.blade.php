<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Webshop – Triem Dragonherbs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
    </style>
    @include('partials.barion-pixel')
</head>
<body class="bg-stone-50 text-gray-800 antialiased">
    <header class="bg-emerald-900 text-white">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="font-display text-2xl">Triem Dragonherbs</a>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 text-white shadow"
                    data-bs-toggle="modal"
                    data-bs-target="#cartModal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l2-7H6.4M7 13L5.4 5M7 13l-2 7h14M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                    </svg>
                    <span id="cart-count-badge" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-emerald-500 text-[10px] font-semibold text-white">0</span>
                </button>
                <a href="{{ route('welcome') }}" class="text-sm text-emerald-100 hover:text-white">Vissza a nyitóoldalra</a>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="font-display text-4xl text-emerald-900 font-semibold mb-8">Webshop</h1>

        @php
            $categoryLabels = [
                'gyogyteak' => 'Gyógyteák',
                'illoolajok' => 'Illóolajok',
                'kozmetikumok' => 'Kozmetikumok',
            ];
        @endphp

        @foreach(['gyogyteak', 'illoolajok', 'kozmetikumok'] as $category)
            <section class="mb-12">
                <h2 class="font-display text-3xl text-emerald-800 mb-5">{{ $categoryLabels[$category] }}</h2>

                @if(($productsByCategory[$category] ?? collect())->isEmpty())
                    <p class="text-gray-500">Ebben a kategóriában még nincs termék.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($productsByCategory[$category] as $product)
                            <article class="bg-white rounded-xl border border-emerald-100 shadow-sm overflow-hidden">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-display text-2xl text-emerald-900 font-semibold">{{ $product->title }}</h3>
                                    <p class="mt-2 text-sm text-gray-600">{{ $product->intro }}</p>
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <p class="text-lg font-semibold text-emerald-800 mb-0">{{ number_format($product->ar) }} Ft</p>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success btn-add-to-cart"
                                            data-product-id="{{ $product->id }}"
                                            data-product-title="{{ $product->title }}"
                                            data-product-price="{{ $product->ar }}"
                                        >
                                            Kosárba
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </main>

    <footer class="bg-emerald-900 text-emerald-50 py-8 mt-10">
        <div class="max-w-6xl mx-auto px-4">
            @include('partials.barion-payment-logos', ['class' => 'max-w-2xl', 'label' => 'Biztonságos online fizetés:', 'labelClass' => 'text-emerald-200'])
            <p class="mt-6 text-emerald-200/90 text-sm">© {{ date('Y') }} Triem Dragonherbs</p>
        </div>
    </footer>

    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-display text-xl" id="cartModalLabel">Kosár</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-empty" class="text-gray-500">A kosár jelenleg üres.</div>
                    <div id="cart-content" class="d-none">
                        <div id="cart-step-items">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Termék</th>
                                        <th class="text-end">Egységár</th>
                                        <th class="text-center">Mennyiség</th>
                                        <th class="text-end">Összeg</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-body"></tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-end ms-auto">
                                    <div class="text-sm text-gray-600">Végösszeg:</div>
                                    <div class="h5 mb-0" id="cart-total">0 Ft</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" id="btn-go-to-checkout" class="btn btn-success">Tovább a fizetési adatokhoz</button>
                            </div>
                        </div>

                        <div id="cart-step-checkout" class="d-none">
                            <form id="cart-order-form" class="row g-3">
                                <div class="col-md-6">
                                    <label for="orderName" class="form-label">Név</label>
                                    <input type="text" class="form-control" id="orderName" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="orderPhone" class="form-label">Telefon</label>
                                    <input type="tel" class="form-control" id="orderPhone" required>
                                </div>

                                <div class="col-12" id="home-shipping-address-wrap">
                                    <label for="orderShippingAddress" class="form-label">Szállítási cím</label>
                                    <textarea class="form-control" id="orderShippingAddress" rows="2" required></textarea>
                                </div>
                                <div class="col-12 form-check">
                                    <input class="form-check-input" type="checkbox" id="sameBillingAddress" checked>
                                    <label class="form-check-label" for="sameBillingAddress">
                                        A számlázási cím megegyezik a szállítási címmel
                                    </label>
                                </div>
                                <div class="col-12" id="billing-address-wrap">
                                    <label for="orderBillingAddress" class="form-label">Számlázási cím</label>
                                    <textarea class="form-control" id="orderBillingAddress" rows="2" required></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label for="orderPaymentMethod" class="form-label mb-1">Fizetési mód</label>
                                    @if(count($enabledPaymentMethods) > 0)
                                        <select id="orderPaymentMethod" class="form-select" required>
                                            @foreach($enabledPaymentMethods as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <p class="text-sm text-amber-700 mb-0">Jelenleg nem érhető el fizetési mód.</p>
                                    @endif

                                    @include('partials.barion-payment-logos', ['context' => 'checkout', 'class' => 'mt-3'])

                                    <label for="orderShippingMethod" class="form-label mb-1 mt-3">Preferált szállítási mód</label>
                                    @if(count($enabledShippingMethods) > 0)
                                        <select id="orderShippingMethod" class="form-select" required>
                                            @foreach($enabledShippingMethods as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <p class="text-sm text-amber-700 mb-0">Jelenleg nem érhető el szállítási mód.</p>
                                    @endif

                                    <div id="delivery-type-wrap" class="mt-3 d-none">
                                        <label class="form-label mb-2">Kézbesítés módja</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deliveryType" id="deliveryTypeHome" value="home" checked>
                                            <label class="form-check-label" for="deliveryTypeHome">Házhozszállítás</label>
                                        </div>
                                        <div class="form-check" id="delivery-type-pickup-wrap">
                                            <input class="form-check-input" type="radio" name="deliveryType" id="deliveryTypePickup" value="pickup">
                                            <label class="form-check-label" for="deliveryTypePickup">Automata / csomagpont</label>
                                        </div>
                                    </div>

                                    <div id="pickup-point-wrap" class="mt-3 d-none">
                                        <label for="pickupPointSearch" class="form-label mb-1">Átvételi pont keresése</label>
                                        <input type="text" class="form-control mb-2" id="pickupPointSearch" placeholder="Irányítószám, város vagy cím...">
                                        <select id="orderPickupPoint" class="form-select" size="6">
                                            <option value="">Először keressen vagy várjon a lista betöltésére...</option>
                                        </select>
                                        <div class="text-xs text-gray-500 mt-1" id="pickup-point-hint"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="text-sm text-gray-600">Termékek összesen:</div>
                                    <div class="mb-1" id="checkout-subtotal">0 Ft</div>
                                    <div class="text-sm text-gray-600">Szállítási díj:</div>
                                    <div class="mb-1" id="checkout-shipping-fee">0 Ft</div>
                                    <div class="text-sm text-gray-600 fw-semibold">Fizetendő végösszeg:</div>
                                    <div class="h5 mb-0" id="checkout-total">0 Ft</div>
                                </div>

                                <div class="col-12 d-flex justify-content-between mt-2">
                                    <button type="button" id="btn-back-to-cart" class="btn btn-outline-secondary">Vissza a kosárhoz</button>
                                    <button type="submit" class="btn btn-success" @if(count($enabledPaymentMethods) === 0 || count($enabledShippingMethods) === 0) disabled @endif>Fizetés és rendelés véglegesítése</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addToCartConfirmModal" tabindex="-1" aria-labelledby="addToCartConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addToCartConfirmModalLabel">Termék kosárba helyezve</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="add-to-cart-message">A terméket a kosárba helyeztük.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Vásárlás folytatása</button>
                    <button type="button" class="btn btn-success" id="btn-go-to-cart-modal">Kosár megnyitása</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="termsAcceptModal" tabindex="-1" aria-labelledby="termsAcceptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-display" id="termsAcceptModalLabel">ÁSZF és szállítási feltételek elfogadása</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                </div>
                <div class="modal-body">
                    <p class="text-gray-600 mb-3">A rendelés leadása előtt kérjük, olvassa el és fogadja el az Általános Szerződési Feltételeket és a szállítási feltételeket.</p>
                    <ul class="nav nav-tabs mb-3" id="termsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="terms-aszf-tab" data-bs-toggle="tab" data-bs-target="#terms-aszf-pane" type="button" role="tab">ÁSZF</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="terms-shipping-tab" data-bs-toggle="tab" data-bs-target="#terms-shipping-pane" type="button" role="tab">Szállítási feltételek</button>
                        </li>
                    </ul>
                    <div class="tab-content border rounded p-3 bg-light" style="max-height: 360px; overflow-y: auto;">
                        <div class="tab-pane fade show active text-gray-700 leading-relaxed" id="terms-aszf-pane" role="tabpanel">
                            {!! $legalDocuments->aszf_content !!}
                        </div>
                        <div class="tab-pane fade text-gray-700 leading-relaxed" id="terms-shipping-pane" role="tabpanel">
                            {!! $legalDocuments->shipping_terms_content !!}
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="acceptAszfCheckbox">
                            <label class="form-check-label" for="acceptAszfCheckbox">Elolvastam és elfogadom az Általános Szerződési Feltételeket (ÁSZF).</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="acceptShippingCheckbox">
                            <label class="form-check-label" for="acceptShippingCheckbox">Elolvastam és elfogadom a szállítási feltételeket.</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Mégse</button>
                    <button type="button" class="btn btn-success" id="btn-confirm-terms">Elfogadom és rendelés leadása</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var CART_STORAGE_KEY = 'triem_webshop_cart';
            var TERMS_STORAGE_KEY = 'triem_legal_accepted';
            var cart = [];
            var persistTimer = null;
            var pickupSearchTimer = null;
            var currencyFormatter = new Intl.NumberFormat('hu-HU');
            var carriersWithPickup = @json($carriersWithPickup);
            var shippingFees = @json($shippingFees);
            var pickupPointsCache = {};
            var pendingOrderSubmit = false;
            var barionPixelEnabled = @json(\App\Support\BarionBranding::hasPixel());

            function barionTrack(eventName, properties) {
                if (!barionPixelEnabled || typeof window.bp !== 'function') {
                    return;
                }
                bp('track', eventName, properties);
            }

            function barionContentsFromCart(items) {
                return items.map(function (item) {
                    return {
                        contentType: 'Product',
                        currency: 'HUF',
                        id: String(item.id),
                        name: item.title,
                        quantity: item.qty,
                        totalItemPrice: item.price * item.qty,
                        unit: 'db',
                        unitPrice: item.price
                    };
                });
            }

            function loadCart() {
                try {
                    var raw = localStorage.getItem(CART_STORAGE_KEY);
                    var parsed = raw ? JSON.parse(raw) : [];
                    cart = Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    cart = [];
                }
            }

            function saveCart() {
                if (persistTimer) {
                    clearTimeout(persistTimer);
                }
                // Kicsit késleltetett mentés, hogy a kattintás ne akadjon
                persistTimer = setTimeout(function () {
                    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
                    persistTimer = null;
                }, 60);
            }

            function formatPrice(value) {
                var num = parseInt(value, 10) || 0;
                return currencyFormatter.format(num) + ' Ft';
            }

            function updateCartBadge() {
                var badge = document.getElementById('cart-count-badge');
                if (!badge) return;
                var count = cart.reduce(function (sum, item) { return sum + item.qty; }, 0);
                badge.textContent = count;
                badge.style.visibility = count > 0 ? 'visible' : 'hidden';
            }

            function getItemsTotal() {
                return cart.reduce(function (sum, item) { return sum + (item.price * item.qty); }, 0);
            }

            function getSelectedShippingFee() {
                var el = document.getElementById('orderShippingMethod');
                if (!el || !el.value) return 0;
                return parseInt(shippingFees[el.value], 10) || 0;
            }

            function getGrandTotal() {
                return getItemsTotal() + getSelectedShippingFee();
            }

            function hasAcceptedTerms() {
                try {
                    return localStorage.getItem(TERMS_STORAGE_KEY) === '1';
                } catch (e) {
                    return false;
                }
            }

            function saveTermsAccepted() {
                try {
                    localStorage.setItem(TERMS_STORAGE_KEY, '1');
                } catch (e) {
                    // ignore
                }
            }

            function updateCheckoutTotals() {
                var subtotalEl = document.getElementById('checkout-subtotal');
                var shippingFeeEl = document.getElementById('checkout-shipping-fee');
                var checkoutTotalEl = document.getElementById('checkout-total');
                var itemsTotal = getItemsTotal();
                var shippingFee = getSelectedShippingFee();

                if (subtotalEl) subtotalEl.textContent = formatPrice(itemsTotal);
                if (shippingFeeEl) shippingFeeEl.textContent = formatPrice(shippingFee);
                if (checkoutTotalEl) checkoutTotalEl.textContent = formatPrice(itemsTotal + shippingFee);
            }

            function renderCart() {
                var emptyEl = document.getElementById('cart-empty');
                var contentEl = document.getElementById('cart-content');
                var tbody = document.getElementById('cart-items-body');
                var totalEl = document.getElementById('cart-total');
                if (!emptyEl || !contentEl || !tbody || !totalEl) return;

                if (cart.length === 0) {
                    emptyEl.classList.remove('d-none');
                    emptyEl.style.display = 'block';
                    contentEl.classList.add('d-none');
                    contentEl.style.display = 'none';
                    return;
                }

                emptyEl.classList.add('d-none');
                emptyEl.style.display = 'none';
                contentEl.classList.remove('d-none');
                contentEl.style.display = 'block';

                var total = 0;
                var rowsHtml = '';
                cart.forEach(function (item, index) {
                    var lineTotal = item.price * item.qty;
                    total += lineTotal;
                    rowsHtml +=
                        '<tr>' +
                        '<td>' + item.title + '</td>' +
                        '<td class="text-end">' + formatPrice(item.price) + '</td>' +
                        '<td class="text-center">' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary me-1 btn-cart-dec" data-index="' + index + '">-</button>' +
                            '<span>' + item.qty + '</span>' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary ms-1 btn-cart-inc" data-index="' + index + '">+</button>' +
                        '</td>' +
                        '<td class="text-end">' + formatPrice(lineTotal) + '</td>' +
                        '<td class="text-end"><button type="button" class="btn btn-sm btn-link text-danger btn-cart-remove" data-index="' + index + '">Eltávolítás</button></td>' +
                        '</tr>';
                });
                tbody.innerHTML = rowsHtml;

                totalEl.textContent = formatPrice(total);
                updateCheckoutTotals();
            }

            function addToCart(product) {
                var existing = cart.find(function (item) { return item.id === product.id; });
                if (existing) {
                    existing.qty += 1;
                } else {
                    cart.push({ id: product.id, title: product.title, price: product.price, qty: 1 });
                }
                barionTrack('addToCart', {
                    contents: barionContentsFromCart([{
                        id: product.id,
                        title: product.title,
                        price: product.price,
                        qty: 1
                    }]),
                    currency: 'HUF',
                    revenue: product.price
                });
                saveCart();
                updateCartBadge();
                // A teljes kosár render csak akkor kell, ha ténylegesen megnyitjuk a kosarat
            }

            document.addEventListener('click', function (event) {
                var target = event.target;
                var addBtn = target.closest('.btn-add-to-cart');
                var incBtn = target.closest('.btn-cart-inc');
                var decBtn = target.closest('.btn-cart-dec');
                var removeBtn = target.closest('.btn-cart-remove');

                if (addBtn) {
                    var productTitle = addBtn.getAttribute('data-product-title') || 'termék';
                    addToCart({
                        id: parseInt(addBtn.getAttribute('data-product-id'), 10),
                        title: productTitle,
                        price: parseInt(addBtn.getAttribute('data-product-price'), 10) || 0
                    });
                    var msgEl = document.getElementById('add-to-cart-message');
                    if (msgEl) {
                        msgEl.textContent = 'A(z) "' + productTitle + '" terméket a kosárba helyeztük.';
                    }
                    var confirmModalEl = document.getElementById('addToCartConfirmModal');
                    if (confirmModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(confirmModalEl).show();
                    }
                }
                if (incBtn) {
                    var incIndex = parseInt(incBtn.getAttribute('data-index'), 10);
                    if (!isNaN(incIndex) && cart[incIndex]) {
                        cart[incIndex].qty += 1;
                        saveCart();
                        updateCartBadge();
                        renderCart();
                    }
                }
                if (decBtn) {
                    var decIndex = parseInt(decBtn.getAttribute('data-index'), 10);
                    if (!isNaN(decIndex) && cart[decIndex]) {
                        cart[decIndex].qty -= 1;
                        if (cart[decIndex].qty <= 0) cart.splice(decIndex, 1);
                        saveCart();
                        updateCartBadge();
                        renderCart();
                    }
                }
                if (removeBtn) {
                    var remIndex = parseInt(removeBtn.getAttribute('data-index'), 10);
                    if (!isNaN(remIndex)) {
                        cart.splice(remIndex, 1);
                        saveCart();
                        updateCartBadge();
                        renderCart();
                    }
                }
            });

            var orderForm = document.getElementById('cart-order-form');
            var cartModal = document.getElementById('cartModal');
            var confirmModal = document.getElementById('addToCartConfirmModal');
            var btnGoToCartModal = document.getElementById('btn-go-to-cart-modal');
            var btnGoToCheckout = document.getElementById('btn-go-to-checkout');
            var btnBackToCart = document.getElementById('btn-back-to-cart');
            var sameBillingAddressCheckbox = document.getElementById('sameBillingAddress');
            var billingAddressWrap = document.getElementById('billing-address-wrap');
            var shippingAddressEl = document.getElementById('orderShippingAddress');
            var billingAddressEl = document.getElementById('orderBillingAddress');
            var shippingMethodEl = document.getElementById('orderShippingMethod');
            var deliveryTypeWrap = document.getElementById('delivery-type-wrap');
            var deliveryTypeHome = document.getElementById('deliveryTypeHome');
            var deliveryTypePickup = document.getElementById('deliveryTypePickup');
            var deliveryTypePickupWrap = document.getElementById('delivery-type-pickup-wrap');
            var pickupPointWrap = document.getElementById('pickup-point-wrap');
            var pickupPointSearch = document.getElementById('pickupPointSearch');
            var pickupPointSelect = document.getElementById('orderPickupPoint');
            var pickupPointHint = document.getElementById('pickup-point-hint');
            var homeShippingAddressWrap = document.getElementById('home-shipping-address-wrap');

            function showCartStep() {
                var stepItems = document.getElementById('cart-step-items');
                var stepCheckout = document.getElementById('cart-step-checkout');
                if (stepItems) stepItems.classList.remove('d-none');
                if (stepCheckout) stepCheckout.classList.add('d-none');
            }

            function showCheckoutStep() {
                var stepItems = document.getElementById('cart-step-items');
                var stepCheckout = document.getElementById('cart-step-checkout');
                if (stepItems) stepItems.classList.add('d-none');
                if (stepCheckout) stepCheckout.classList.remove('d-none');
            }

            function syncBillingVisibility() {
                if (!sameBillingAddressCheckbox || !billingAddressWrap || !billingAddressEl) return;
                if (sameBillingAddressCheckbox.checked) {
                    billingAddressWrap.classList.add('d-none');
                    billingAddressEl.value = shippingAddressEl ? shippingAddressEl.value : '';
                    billingAddressEl.required = false;
                } else {
                    billingAddressWrap.classList.remove('d-none');
                    billingAddressEl.required = true;
                }
            }

            function selectedDeliveryType() {
                if (deliveryTypePickup && deliveryTypePickup.checked) {
                    return 'pickup';
                }
                return 'home';
            }

            function carrierHasPickup(carrier) {
                return carriersWithPickup.indexOf(carrier) !== -1;
            }

            function syncDeliveryVisibility() {
                var carrier = shippingMethodEl ? shippingMethodEl.value : '';
                var hasPickup = carrier !== '' && carrierHasPickup(carrier);
                var deliveryType = selectedDeliveryType();

                if (deliveryTypeWrap) {
                    deliveryTypeWrap.classList.toggle('d-none', carrier === '');
                }
                if (deliveryTypePickupWrap) {
                    deliveryTypePickupWrap.classList.toggle('d-none', !hasPickup);
                }
                if (!hasPickup && deliveryTypeHome) {
                    deliveryTypeHome.checked = true;
                }
                if (pickupPointWrap) {
                    pickupPointWrap.classList.toggle('d-none', deliveryType !== 'pickup' || !hasPickup);
                }
                if (homeShippingAddressWrap && shippingAddressEl) {
                    var showHomeAddress = deliveryType === 'home';
                    homeShippingAddressWrap.classList.toggle('d-none', !showHomeAddress);
                    shippingAddressEl.required = showHomeAddress;
                }
                if (pickupPointSelect) {
                    pickupPointSelect.required = deliveryType === 'pickup' && hasPickup;
                }
                if (sameBillingAddressCheckbox) {
                    var sameBillingWrap = sameBillingAddressCheckbox.closest('.form-check');
                    if (sameBillingWrap) {
                        sameBillingWrap.classList.toggle('d-none', deliveryType === 'pickup');
                    }
                    if (deliveryType === 'pickup') {
                        sameBillingAddressCheckbox.checked = false;
                        if (billingAddressWrap) {
                            billingAddressWrap.classList.remove('d-none');
                        }
                        if (billingAddressEl) {
                            billingAddressEl.required = true;
                        }
                    } else {
                        syncBillingVisibility();
                    }
                }
            }

            function renderPickupPoints(points) {
                if (!pickupPointSelect) return;
                pickupPointSelect.innerHTML = '';
                if (!points.length) {
                    var emptyOption = document.createElement('option');
                    emptyOption.value = '';
                    emptyOption.textContent = 'Nincs találat. Próbáljon másik keresést.';
                    pickupPointSelect.appendChild(emptyOption);
                    return;
                }
                points.forEach(function (point) {
                    var option = document.createElement('option');
                    option.value = point.id;
                    option.textContent = point.label;
                    option.dataset.name = point.name;
                    option.dataset.address = point.address;
                    pickupPointSelect.appendChild(option);
                });
            }

            function loadPickupPoints(searchTerm) {
                var carrier = shippingMethodEl ? shippingMethodEl.value : '';
                if (!carrier || !carrierHasPickup(carrier)) {
                    renderPickupPoints([]);
                    return;
                }

                var cacheKey = carrier + '|' + (searchTerm || '');
                if (pickupPointsCache[cacheKey]) {
                    renderPickupPoints(pickupPointsCache[cacheKey]);
                    if (pickupPointHint) {
                        pickupPointHint.textContent = pickupPointsCache[cacheKey].length + ' találat';
                    }
                    return;
                }

                if (pickupPointHint) {
                    pickupPointHint.textContent = 'Átvételi pontok betöltése...';
                }

                var url = '{{ route('pickup-points.index') }}?carrier=' + encodeURIComponent(carrier);
                if (searchTerm) {
                    url += '&q=' + encodeURIComponent(searchTerm);
                }

                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        var points = data.points || [];
                        pickupPointsCache[cacheKey] = points;
                        renderPickupPoints(points);
                        if (pickupPointHint) {
                            pickupPointHint.textContent = points.length ? (points.length + ' találat') : 'Nincs találat.';
                        }
                    })
                    .catch(function () {
                        renderPickupPoints([]);
                        if (pickupPointHint) {
                            pickupPointHint.textContent = 'Hiba történt az átvételi pontok betöltése közben.';
                        }
                    });
            }

            function cleanupModalArtifacts() {
                // Bootstrap modal maradekok takaritasa (backdrop, body lock)
                document.querySelectorAll('.modal-backdrop').forEach(function (el) {
                    el.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }

            if (cartModal) {
                cartModal.addEventListener('show.bs.modal', function () {
                    showCartStep();
                    syncBillingVisibility();
                    syncDeliveryVisibility();
                    // A megnyitas utan 1 frame-mel renderelunk, hogy ne fogja a modal animaciot
                    requestAnimationFrame(function () {
                        renderCart();
                    });
                });
                cartModal.addEventListener('hidden.bs.modal', function () {
                    cleanupModalArtifacts();
                });
            }

            if (btnGoToCartModal) {
                btnGoToCartModal.addEventListener('click', function () {
                    if (confirmModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var confirmInstance = bootstrap.Modal.getOrCreateInstance(confirmModal);
                        confirmModal.addEventListener('hidden.bs.modal', function onHidden() {
                            confirmModal.removeEventListener('hidden.bs.modal', onHidden);
                            if (cartModal && bootstrap.Modal) {
                                bootstrap.Modal.getOrCreateInstance(cartModal).show();
                            }
                        });
                        confirmInstance.hide();
                    } else if (cartModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(cartModal).show();
                    }
                });
            }

            if (confirmModal) {
                confirmModal.addEventListener('hidden.bs.modal', function () {
                    cleanupModalArtifacts();
                });
            }

            if (btnGoToCheckout) {
                btnGoToCheckout.addEventListener('click', function () {
                    showCheckoutStep();
                    syncBillingVisibility();
                    syncDeliveryVisibility();
                    updateCheckoutTotals();
                    loadPickupPoints('');
                    barionTrack('initiateCheckout', {
                        contents: barionContentsFromCart(cart),
                        currency: 'HUF',
                        revenue: getGrandTotal(),
                        step: 1
                    });
                });
            }

            if (btnBackToCart) {
                btnBackToCart.addEventListener('click', function () {
                    showCartStep();
                });
            }

            if (sameBillingAddressCheckbox) {
                sameBillingAddressCheckbox.addEventListener('change', syncBillingVisibility);
            }

            if (shippingAddressEl) {
                shippingAddressEl.addEventListener('input', function () {
                    if (sameBillingAddressCheckbox && sameBillingAddressCheckbox.checked && billingAddressEl) {
                        billingAddressEl.value = shippingAddressEl.value;
                    }
                });
            }

            if (shippingMethodEl) {
                shippingMethodEl.addEventListener('change', function () {
                    if (pickupPointSearch) {
                        pickupPointSearch.value = '';
                    }
                    pickupPointsCache = {};
                    syncDeliveryVisibility();
                    updateCheckoutTotals();
                    if (selectedDeliveryType() === 'pickup') {
                        loadPickupPoints('');
                    }
                });
            }

            [deliveryTypeHome, deliveryTypePickup].forEach(function (input) {
                if (!input) return;
                input.addEventListener('change', function () {
                    syncDeliveryVisibility();
                    if (selectedDeliveryType() === 'pickup') {
                        loadPickupPoints(pickupPointSearch ? pickupPointSearch.value.trim() : '');
                    }
                });
            });

            if (pickupPointSearch) {
                pickupPointSearch.addEventListener('input', function () {
                    if (pickupSearchTimer) {
                        clearTimeout(pickupSearchTimer);
                    }
                    pickupSearchTimer = setTimeout(function () {
                        loadPickupPoints(pickupPointSearch.value.trim());
                    }, 300);
                });
            }

            function submitOrderRequest() {
                var paymentEl = document.getElementById('orderPaymentMethod');
                var shippingMethodElLocal = document.getElementById('orderShippingMethod');
                var deliveryType = selectedDeliveryType();
                var shippingAddress = '';
                var pickupPointExternalId = null;
                var pickupPointName = null;
                var pickupPointAddress = null;

                if (deliveryType === 'home') {
                    shippingAddress = document.getElementById('orderShippingAddress').value.trim();
                } else {
                    var selectedOption = pickupPointSelect.options[pickupPointSelect.selectedIndex];
                    pickupPointExternalId = pickupPointSelect.value;
                    pickupPointName = selectedOption.dataset.name || selectedOption.textContent;
                    pickupPointAddress = selectedOption.dataset.address || '';
                }

                var billingAddress = (sameBillingAddressCheckbox && sameBillingAddressCheckbox.checked && deliveryType === 'home')
                    ? shippingAddress
                    : document.getElementById('orderBillingAddress').value.trim();

                fetch('{{ route('orders.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: document.getElementById('orderName').value.trim(),
                        phone: document.getElementById('orderPhone').value.trim(),
                        shipping_address: shippingAddress,
                        billing_address: billingAddress,
                        items: cart,
                        total_price: getGrandTotal(),
                        terms_accepted: true,
                        payment_method: paymentEl.value,
                        shipping_method: shippingMethodElLocal.value,
                        delivery_type: deliveryType,
                        pickup_point_external_id: pickupPointExternalId,
                        pickup_point_name: pickupPointName,
                        pickup_point_address: pickupPointAddress
                    })
                }).then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, status: response.status, data: data };
                    });
                }).then(function (result) {
                    if (!result.ok) {
                        var msg = (result.data && result.data.message) ? result.data.message : 'Mentési hiba';
                        if (result.data && result.data.errors) {
                            var firstError = Object.values(result.data.errors)[0];
                            if (Array.isArray(firstError) && firstError[0]) {
                                msg = firstError[0];
                            }
                        }
                        alert(msg);
                        return;
                    }
                    var data = result.data;
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                        return;
                    }
                    barionTrack('purchase', {
                        contents: barionContentsFromCart(cart),
                        currency: 'HUF',
                        orderNumber: String(data.order_id),
                        revenue: getGrandTotal(),
                        step: 1
                    });
                    alert('Rendelés rögzítve. Azonosító: #' + data.order_id);
                    cart = [];
                    saveCart();
                    orderForm.reset();
                    updateCartBadge();
                    renderCart();
                    showCartStep();
                    var cartModalEl = document.getElementById('cartModal');
                    var modalInstance = cartModalEl ? bootstrap.Modal.getInstance(cartModalEl) : null;
                    if (modalInstance) modalInstance.hide();
                }).catch(function () {
                    alert('Hiba történt a rendelés mentése közben.');
                });
            }

            function validateOrderForm() {
                if (cart.length === 0) {
                    alert('A kosár üres.');
                    return false;
                }
                var paymentEl = document.getElementById('orderPaymentMethod');
                if (!paymentEl) {
                    alert('Jelenleg nem érhető el fizetési mód.');
                    return false;
                }
                var shippingMethodElLocal = document.getElementById('orderShippingMethod');
                if (!shippingMethodElLocal) {
                    alert('Jelenleg nem érhető el szállítási mód.');
                    return false;
                }

                var deliveryType = selectedDeliveryType();
                if (deliveryType === 'home') {
                    var shippingAddress = document.getElementById('orderShippingAddress').value.trim();
                    if (!shippingAddress) {
                        alert('Kérjük, adja meg a szállítási címet.');
                        return false;
                    }
                } else if (!pickupPointSelect || !pickupPointSelect.value) {
                    alert('Kérjük, válasszon átvételi pontot.');
                    return false;
                }

                var billingAddress = (sameBillingAddressCheckbox && sameBillingAddressCheckbox.checked && deliveryType === 'home')
                    ? (document.getElementById('orderShippingAddress').value.trim())
                    : document.getElementById('orderBillingAddress').value.trim();
                if (!billingAddress) {
                    alert('Kérjük, adja meg a számlázási címet.');
                    return false;
                }

                return true;
            }

            function showTermsModal() {
                var termsModal = document.getElementById('termsAcceptModal');
                if (termsModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(termsModal).show();
                } else {
                    alert('Kérjük, fogadja el az ÁSZF-et és a szállítási feltételeket a rendelés leadásához.');
                }
            }

            if (orderForm) {
                orderForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (!validateOrderForm()) {
                        return;
                    }
                    if (!hasAcceptedTerms()) {
                        pendingOrderSubmit = true;
                        showTermsModal();
                        return;
                    }
                    submitOrderRequest();
                });
            }

            var btnConfirmTerms = document.getElementById('btn-confirm-terms');
            var acceptAszfCheckbox = document.getElementById('acceptAszfCheckbox');
            var acceptShippingCheckbox = document.getElementById('acceptShippingCheckbox');
            var termsAcceptModal = document.getElementById('termsAcceptModal');

            if (btnConfirmTerms) {
                btnConfirmTerms.addEventListener('click', function () {
                    if (!acceptAszfCheckbox || !acceptAszfCheckbox.checked || !acceptShippingCheckbox || !acceptShippingCheckbox.checked) {
                        alert('Kérjük, jelölje be mindkét elfogadást a rendelés leadásához.');
                        return;
                    }
                    saveTermsAccepted();
                    if (termsAcceptModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(termsAcceptModal).hide();
                    }
                    if (pendingOrderSubmit) {
                        pendingOrderSubmit = false;
                        submitOrderRequest();
                    }
                });
            }

            if (termsAcceptModal) {
                termsAcceptModal.addEventListener('hidden.bs.modal', function () {
                    pendingOrderSubmit = false;
                });
            }

            loadCart();
            updateCartBadge();
            renderCart();
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

