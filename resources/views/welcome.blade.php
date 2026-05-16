<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Triem Dragonherbs – Gyógynövények a Mátra aljáról</title>
    {{-- Facebook / Open Graph megosztási adatok --}}
    <meta property="og:url" content="https://trimedragonherbs.hu">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Triem Dragonherbs Webshop">
    <meta property="og:description" content="Gyökereink a földben, hitünk a minőségben.">
    <meta property="og:image" content="{{ asset('img/boritokep.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
        .header-bg {
            background-image: url("{{ asset('img/boritokep2.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .header-overlay { background: linear-gradient(135deg, rgba(0, 0, 0, 0.25) 0%, rgba(0, 0, 0, 0.55) 100%); }
        .logo-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.9);
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        }
        @media (min-width: 768px) {
            .logo-circle { width: 140px; height: 140px; }
        }
        .carousel-viewport { overflow: hidden; }
        .carousel-track { display: flex; transition: transform 0.35s ease-out; }
        .carousel-slide { flex: 0 0 100%; min-width: 100%; }
        @media (min-width: 640px) { .carousel-slide { flex: 0 0 50%; min-width: 50%; } }
        @media (min-width: 1024px) { .carousel-slide { flex: 0 0 25%; min-width: 25%; } }
        .intro-more { max-height: 0; overflow: hidden; transition: max-height 0.5s ease-out; }
        .intro-more.open { max-height: 2000px; transition: max-height 0.6s ease-in; }

        /* Bootstrap carousel nyilak témához igazítva */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #047857; /* emerald-700 */
            border-radius: 9999px;
            background-size: 60% 60%;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 3rem;
        }
    </style>
</head>
<body class="antialiased text-gray-800 bg-stone-50">

    {{-- Header --}}
    <header class="relative min-h-[60vh] md:min-h-[65vh] flex flex-col header-bg">
        <div class="absolute inset-0 header-overlay"></div>
        <div class="relative z-10 flex-1 flex flex-col justify-center px-4 py-8 md:px-8 md:py-12">
            <nav class="absolute top-4 right-4 md:top-8 md:right-8 flex items-center gap-3">
                {{-- Kosár ikon --}}
                <button
                    type="button"
                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 text-white shadow"
                    data-bs-toggle="modal"
                    data-bs-target="#cartModal" style="display: none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l2-7H6.4M7 13L5.4 5M7 13l-2 7h14M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                    </svg>
                    <span id="cart-count-badge" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-emerald-500 text-[10px] font-semibold text-white">
                        0
                    </span>
                </button>

                @auth
                    @if($webshopLinkVisible ?? false)
                        <a href="{{ route('webshop') }}" class="text-white/95 hover:text-white text-sm font-medium">Webshop</a>
                    @endif
                    <a href="{{ route('home') }}" class="text-white/95 hover:text-white text-sm font-medium">Kezdőlap</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white/95 hover:text-white text-sm font-medium underline">Kijelentkezés</button>
                    </form>
                @else
                    @if($webshopLinkVisible ?? false)
                        <a href="{{ route('webshop') }}" class="text-white/95 hover:text-white text-sm font-medium">Webshop</a>
                    @endif
                    <a href="{{ route('login') }}" class="text-white/95 hover:text-white text-sm font-medium">Bejelentkezés</a>
                    <a href="{{ route('register') }}" class="text-white bg-emerald-700/90 hover:bg-emerald-600 px-3 py-1.5 rounded-lg text-sm font-medium">Regisztráció</a>
                @endauth
            </nav>
            <div class="max-w-5xl mx-auto text-center">
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold text-white drop-shadow-lg">
                    Triem Dragonherbs Webshop
                </h1>
                <p class="mt-3 md:mt-4 font-display text-xl md:text-2xl text-green-100 italic max-w-2xl mx-auto">
                    Gyökereink a földben, hitünk a minőségben.
                </p>
            </div>
            <div class="flex justify-end mt-6 -mb-24 md:-mb-32 relative z-20">
                <img src="{{ asset('img/logo.jpg') }}" alt="Triem Dragonherbs logo" class="logo-circle">
            </div>
            <div class="absolute left-4 bottom-4 flex gap-2" >
                <a href="https://www.facebook.com/trimedragonherbs" target="_blank" style="display: none" rel="noopener" class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-700/90 hover:bg-emerald-600 text-white text-xs font-medium shadow">
                    👍 Tetszik
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode('https://trimedragonherbs.hu') }}" target="_blank" rel="noopener" class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-700/90 hover:bg-emerald-600 text-white text-xs font-medium shadow">
                    Megosztás
                </a>
            </div>
        </div>
    </header>

    <main class="relative">
        {{-- Bemutatkozás --}}
        <section class="max-w-4xl mx-auto px-4 py-12 md:py-16">
            <h2 class="font-display text-3xl md:text-4xl font-semibold text-emerald-900 mb-6">Bemutatkozás</h2>
            <div class="space-y-4 text-gray-700 leading-relaxed">
                <p class="text-lg font-medium text-emerald-800">Üdvözöljük a Triem Dragonherbs világában</p>
                <p>A természet ereje karnyújtásnyira van – mi pedig azért dolgozunk, hogy ezt az erőt tiszta, gondosan válogatott formában juttassuk el Önhöz.</p>
                <p>A Triem Dragonherbs termékei a Mátra aljában működő saját családi gazdaságunkból származnak. Hiszünk a hagyományos értékekben, a föld szeretetében és a felelősségteljes gazdálkodásban.</p>
                <p>Gyógynövényeinket odafigyeléssel termesztjük, kézzel gondozzuk, és a lehető legnagyobb körültekintéssel dolgozzuk fel, hogy megőrizzék természetes hatóanyagaikat.</p>
                <p>Számunkra a minőség nem csupán ígéret, hanem alapelv. Minden egyes termékünk mögött személyes elkötelezettség, szakértelem és a természet iránti tisztelet áll.</p>
                <p>Fedezze fel kínálatunkat, és tapasztalja meg a Mátra tiszta, érintetlen erejét – közvetlenül a családi gazdaságunkból az Ön otthonába.</p>
            </div>
            <div id="intro-more" class="intro-more">
                <div class="mt-6 pt-6 border-t border-emerald-200 space-y-4 text-gray-700 leading-relaxed">
                      <p class="text-lg font-medium text-emerald-800">Rólunk</p>
                    <p>A mi történetünk a Mátra aljában Nagyrédén kezdődött.</p>
                    <p>2015-ben három fővel, családi összefogással indítottuk el a gazdaságunkat szőlőtermesztéssel. A közös munka, a föld szeretete és az egymásba vetett bizalom már akkor meghatározta mindennapjainkat. Nem csupán gazdálkodni szerettünk volna, hanem értéket teremteni.</p>
                    <p>Az évek során egyre inkább a gyógynövények világa felé fordultam. A természetgyógyító ereje, a növények tudatos termesztése és feldolgozása hívatássá vált számomra. 2025-ben Gyógy- és fűszernövény technológusként végeztem a MATE Róbert Károly Campusán, ahol szakmai tudásomat tudományos alapokra helyeztem.</p>
                    <p>Ugyanebben az évben egy új fejezet kezdődött: kialakítottuk 1,8 hektáron fekvő gyógynövénykertünket a Mátra aljában Nagyrédén. Ez a terület ma már nem csupán ültetvény, hanem gondosan ápolt, élő rendszer, ahol minden növény figyelmet és szakértelmet kap.</p>
                    <p>A Triem Dragonherbs márkanév alatt olyan termékeket kínálunk, amelyek saját termesztésből származnak, és amelyek mögött személyes felelősségvállalás áll. A vetéstől a betakarításon át a feldolgozásig jelen vagyunk minden lépésnél, hogy a lehető legtisztább és legértékesebb formában juttassuk el önhöz a természet ajándékait.</p>
                    <p>Számunkra a minőség nem marketingfogás, hanem mindennapi gyakorlat.</p>
                    <p>A föld iránti tisztelet, a szakmai tudás és a családi összetartozás együtt adja a Triem Dragonherbs valódi erejét.</p>
                    <p>Köszönjük, hogy bizalmával támogatja családi gazdaságunk munkáját, és velünk együtt a természetes megoldásokat választja.</p>
                </div>
            </div>
            <button type="button" id="btn-bovebben" class="mt-6 px-6 py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                Bővebben
            </button>
        </section>

        {{-- Termékek carousel (Bootstrap, több elem asztalin) --}}
        <section class="bg-white border-y border-emerald-100 py-12 md:py-16">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="font-display text-3xl md:text-4xl font-semibold text-emerald-900 mb-8 text-center">Termékeink</h2>
                @if(isset($products) && $products->isNotEmpty())
                    <div id="productsCarousel" class="carousel slide" data-bs-ride="false">
                        <div class="carousel-inner">
                            @foreach($products->chunk(3) as $chunkIndex => $chunk)
                                <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                    <div class="row g-4 justify-content-center">
                                        @foreach($chunk as $product)
                                            <div class="col-12 col-md-6 col-lg-4 d-flex justify-content-center">
                                                <div class="bg-stone-50 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow border border-emerald-100 flex flex-col w-100" style="max-width: 22rem;">
                                                    @if($product->image)
                                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->title }}" class="d-block w-100 img-fluid" style="max-height: 260px; object-fit: contain;">
                                                    @else
                                                        <div class="w-full h-48 bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm">Nincs kép</div>
                                                    @endif
                                                    <div class="p-4 d-flex flex-column">
                                                        <h3 class="font-display text-xl font-semibold text-emerald-900">{{ $product->title }}</h3>
                                                        <p class="text-sm text-gray-600 mt-1 flex-1">{{ Str::limit($product->intro, 120) }}</p>
                                                        @if(!empty($product->moreinfo))
                                                            <div id="product-moreinfo-{{ $product->id }}" class="d-none">
                                                                {!! $product->moreinfo !!}
                                                            </div>
                                                        @endif
                                                        <div class="mt-3 d-flex justify-content-between align-items-center gap-2">
                                                            <p class="mb-0 font-semibold text-emerald-800">{{ number_format($product->ar) }} Ft</p>
                                                            <div class="d-flex gap-1">
                                                                <button
                                                                    type="button" style="display: none"
                                                                    class="btn btn-sm btn-success btn-add-to-cart"
                                                                    data-product-id="{{ $product->id }}"
                                                                    data-product-title="{{ $product->title }}"
                                                                    data-product-price="{{ $product->ar }}"
                                                                >
                                                                    Kosárba
                                                                </button>
                                                                @if(!empty($product->moreinfo))
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-sm btn-outline-success"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#productMoreInfoModal"
                                                                        data-product-title="{{ $product->title }}"
                                                                        data-product-moreinfo-id="{{ $product->id }}"
                                                                        data-product-image="{{ $product->image ? Storage::url($product->image) : '' }}"
                                                                    >
                                                                        Bővebben
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productsCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Előző</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productsCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Következő</span>
                        </button>
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">Jelenleg nincs megjeleníthető termék.</p>
                @endif
            </div>
        </section>

        {{-- Termékek "Bővebben" modal --}}
        <div class="modal fade" id="productMoreInfoModal" tabindex="-1" aria-labelledby="productMoreInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-display text-xl" id="productMoreInfoModalLabel">Termék részletei</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                    </div>
                    <div class="modal-body">
                        <div id="productMoreInfoContent" class="text-gray-700 clearfix"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kosár modal (utánvételes rendelés) --}}
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
                                <div>
                                    <label for="orderPaymentMethod" class="form-label mb-1">Fizetési mód</label>
                                    <select id="orderPaymentMethod" class="form-select form-select-sm">
                                        <option value="cod">Utánvét</option>
                                        <option value="otp">OTP kártya</option>
                                    </select>
                                    <div class="text-xs text-gray-500 mt-1">OTP fizetés előkészítve, banki éles adatok bekötése után aktiválható.</div>
                                </div>
                                <div class="text-end">
                                    <div class="text-sm text-gray-600">Végösszeg:</div>
                                    <div class="h5 mb-0" id="cart-total">0 Ft</div>
                                </div>
                            </div>
                            <hr class="my-4" />
                            <h6 class="mb-3">Szállítási adatok</h6>
                            <form id="cart-order-form" class="row g-3">
                                <div class="col-md-6">
                                    <label for="orderName" class="form-label">Név</label>
                                    <input type="text" class="form-control" id="orderName" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="orderPhone" class="form-label">Telefon</label>
                                    <input type="tel" class="form-control" id="orderPhone" required>
                                </div>
                                <div class="col-12">
                                    <label for="orderShippingAddress" class="form-label">Szállítási cím</label>
                                    <textarea class="form-control" id="orderShippingAddress" rows="2" required></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="orderBillingAddress" class="form-label">Számlázási cím</label>
                                    <textarea class="form-control" id="orderBillingAddress" rows="2" required></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="orderNote" class="form-label">Megjegyzés (opcionális)</label>
                                    <textarea class="form-control" id="orderNote" rows="2"></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-emerald btn-success">
                                        Rendelés elküldése (utánvétel)
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Minőség és Termesztés --}}
        <section class="max-w-4xl mx-auto px-4 py-12 md:py-16">
            <h2 class="font-display text-3xl md:text-4xl font-semibold text-emerald-900 mb-8">Minőség és Termesztés</h2>
            <div class="prose prose-emerald max-w-none text-gray-700 space-y-6">
                <p>A Triem Dragonherbs számára a minőség nem egy lépés a folyamatban, hanem maga a folyamat.</p>
                <h3 class="font-display text-xl font-semibold text-emerald-800 mt-6">Saját Termesztés, teljes kontroll</h3>
                <p>Gyógynövényeink a Mátra aljában Nagyrédén található, 1,8 hektáros saját gyógynövénykertünkben nevelkednek. A termesztés minden szakaszát a talaj előkészítéstől a vetésen és gondozáson át a betakarításig személyesen felügyeljük.</p>
                <p>Hiszünk abban, hogy a kiváló minőség alapja az egészséges talaj, a megfelelő fajtaválasztás és az odafigyelés. Növényeink fejlődését folyamatosan figyelemmel kísérjük és a természet ritmusához igazodva dolgozunk.</p>
                <h3 class="font-display text-xl font-semibold text-emerald-800 mt-6">Szakmai tudás és felelősség</h3>
                <p>Gyógy- és fűszernövény technológusi végzettségem biztos alapot ad ahhoz, hogy a termesztést és feldolgozást tudatos, szakmailag megalapozott döntések vezéreljék. Számunkra fontos, hogy a hagyományos tapasztalat és a korszerű szakmai ismeret egymást erősítse.</p>
                <p>Betakarítást mindig az optimális hatóanyag-tartalom időszakában végezzük, hogy a növényeink természetes értékei a lehető legteljesebb formában maradjanak meg.</p>
                <h3 class="font-display text-xl font-semibold text-emerald-800 mt-6">Gondos feldolgozás</h3>
                <p>A betakarított növényeket kíméletesen dolgozzuk fel. A szárítás, válogatás és csomagolás során elsődleges szempont a tisztaság, az állandó minőség és a természetes hatóanyagok megőrzése.</p>
                <p>Nem tömegtermelésben gondolkodunk, hanem átlátható, felelős gazdálkodásban. Minden egyes termék mögött saját munkánk és nevünk áll.</p>
                <h3 class="font-display text-xl font-semibold text-emerald-800 mt-6">Átláthatóság és bizalom</h3>
                <p>Fontos számunkra, hogy vásárlóink pontosan tudják, honnan érkeznek a termékek. A Triem Dragonherbs kínálata saját termesztésből származik, Magyar földből, családi gazdaságból.</p>
                <p><strong>Bízza magát a természetre, szakértő kezeken keresztül.</strong></p>
                <p>Kínálatunk folyamatosan bővül, termékeink átvétele történhet személyesen telefonos egyeztetést követően, illetve postai úton.</p>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-emerald-900 text-emerald-50 py-12 md:py-16">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="font-display text-2xl md:text-3xl font-semibold mb-6">Kapcsolat</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <p class="font-semibold">Medveczkiné Magos Mónika</p>
                    <p><a href="mailto:medmo1973@gmail.com" class="hover:text-emerald-200 underline">medmo1973@gmail.com</a></p>
                    <p><a href="tel:+36309689532" class="hover:text-emerald-200">+36 30/968 9532</a></p>
                </div>
                <div>
                    <p class="font-semibold">Medveczki István</p>
                    <p><a href="tel:+36303037196" class="hover:text-emerald-200">+36 30/303 7196</a></p>
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-4 text-sm">
                <button type="button" class="underline hover:text-emerald-200" data-bs-toggle="modal" data-bs-target="#aszfModal">
                    ÁSZF
                </button>
                <button type="button" class="underline hover:text-emerald-200" data-bs-toggle="modal" data-bs-target="#adatkezelesModal">
                    Adatkezelési tájékoztató
                </button>
            </div>
            <p class="mt-8 text-emerald-200/90 text-sm">© {{ date('Y') }} Triem Dragonherbs. Minden jog fenntartva.</p>
        </div>
    </footer>

    <div class="modal fade" id="aszfModal" tabindex="-1" aria-labelledby="aszfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-display text-2xl" id="aszfModalLabel">Általános Szerződési Feltételek (ÁSZF)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                </div>
                <div class="modal-body text-gray-700 leading-relaxed space-y-4">
                    <p><strong>1. Szolgáltató adatai</strong><br>
                    Cég neve: Triem Dragonherbs<br>
                    Székhely: 3214 Nagyréde, Kossuth L. utca 17.<br>
                    Üzemeltető: Családi gazdaság<br>
                    E-mail: medmo1973@gmail.com<br>
                    Telefonszám: +36 30 968 9532<br>
                    A webáruház használatával a vásárló elfogadja a jelen Általános Szerződési Feltételeket.</p>

                    <p><strong>2. Termékek és szolgáltatások</strong><br>
                    A webshopban gyógynövények, szárítmányok, illatvizek és illóolajok kerülnek értékesítésre.<br>
                    A termékek leírása tájékoztató jellegű, nem minősülnek gyógyhatásra vonatkozó állításnak, és nem helyettesítik az orvosi tanácsadást.</p>

                    <p><strong>3. Megrendelés menete</strong><br>
                    A vásárlás a webáruház felületén történik.<br>
                    A rendelés leadása után a Vásárló automatikus visszaigazoló e-mailt kap.<br>
                    A szerződés a megrendelés visszaigazolásával jön létre.</p>

                    <p><strong>4. Árak és fizetés</strong><br>
                    A feltüntetett árak forintban értendők és tartalmazzák az áfát.<br>
                    Fizetési módok:<br>
                    banki átutalás<br>
                    utánvét<br>
                    bankkártyás fizetés (ha elérhető)</p>

                    <p><strong>5. Szállítás</strong><br>
                    A rendeléseket a Szolgáltató:<br>
                    futárszolgálattal<br>
                    csomagküldő szolgáltatással<br>
                    vagy személyes átvétellel<br>
                    juttatja el a Vásárlóhoz.<br>
                    Szállítási idő:<br>
                    👉 5–10 munkanap<br>
                    A pontos szállítási költség a rendelés során kerül feltüntetésre.</p>

                    <p><strong>6. Elállási jog</strong><br>
                    A Vásárlót a megrendeléstől számított 14 napon belül elállási jog illeti meg.<br>
                    Elállás esetén a Vásárló köteles a terméket sértetlen állapotban visszajuttatni.<br>
                    A visszaküldés költsége a Vásárlót terheli.<br>
                    ❗ Kivétel az elállás alól<br>
                    Az elállási jog nem gyakorolható:<br>
                    felbontott termékek esetén<br>
                    higiéniai okokból nem visszazárható termékeknél<br>
                    (pl. bontott illóolaj, felbontott illatvíz stb.)</p>

                    <p><strong>7. Termék visszaküldés</strong><br>
                    A visszaküldött terméknek:<br>
                    sértetlennek<br>
                    eredeti csomagolásban lévőnek<br>
                    használatmentesnek kell lennie</p>

                    <p><strong>8. Felelősség</strong><br>
                    A Szolgáltató nem vállal felelősséget:<br>
                    a nem rendeltetésszerű használatból eredő károkért<br>
                    a termékek helytelen alkalmazásáért<br>
                    A termékek használata a Vásárló saját felelősségére történik.</p>

                    <p><strong>9. Adatkezelés</strong><br>
                    A személyes adatok kezelése a hatályos adatvédelmi jogszabályoknak megfelelően történik.<br>
                    Részletes adatkezelési tájékoztató külön dokumentumban érhető el.</p>

                    <p><strong>10. Jogviták</strong><br>
                    A felek a vitás kérdéseket elsősorban békés úton rendezik.<br>
                    Amennyiben ez nem vezet eredményre, a hatáskörrel rendelkező magyar bíróság jogosult eljárni.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adatkezelesModal" tabindex="-1" aria-labelledby="adatkezelesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-display text-2xl" id="adatkezelesModalLabel">Adatkezelési Tájékoztató</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                </div>
                <div class="modal-body text-gray-700 leading-relaxed space-y-4">
                    <p><strong>1. Az adatkezelő adatai</strong><br>
                    Név: Triem Dragonherbs<br>
                    Székhely: 3214 Nagyréde, Kossuth L. utca 17.<br>
                    Üzemeltető: Családi gazdaság<br>
                    E-mail: medmo1973@gmail.com<br>
                    Telefonszám: +36 30 968 9532</p>

                    <p><strong>2. Az adatkezelés célja</strong><br>
                    Az adatkezelés célja:<br>
                    a megrendelések teljesítése<br>
                    kapcsolattartás a vásárlókkal<br>
                    számlázás<br>
                    jogszabályi kötelezettségek teljesítése</p>

                    <p><strong>3. Kezelt személyes adatok köre</strong><br>
                    A webshop használata során az alábbi adatok kerülhetnek rögzítésre:<br>
                    név<br>
                    e-mail cím<br>
                    telefonszám<br>
                    szállítási cím<br>
                    számlázási adatok<br>
                    rendelési adatok</p>

                    <p><strong>4. Az adatkezelés jogalapja</strong><br>
                    Az adatkezelés jogalapja:<br>
                    a szerződés teljesítése<br>
                    jogi kötelezettség teljesítése<br>
                    a vásárló hozzájárulása</p>

                    <p><strong>5. Az adatok tárolásának időtartama</strong><br>
                    A személyes adatokat:<br>
                    a megrendelés teljesítéséig<br>
                    vagy a jogszabályban előírt ideig (pl. számlázás miatt)<br>
                    őrizzük meg.</p>

                    <p><strong>6. Adattovábbítás</strong><br>
                    A személyes adatok csak az alábbi esetekben kerülnek továbbításra:<br>
                    futárszolgálat részére (szállítás céljából)<br>
                    könyvelő részére (számlázás miatt)<br>
                    Az adatok harmadik fél számára marketing célra nem kerülnek átadásra.</p>

                    <p><strong>7. Adatbiztonság</strong><br>
                    Az adatokat:<br>
                    biztonságosan tároljuk<br>
                    illetéktelen hozzáféréstől védjük<br>
                    csak az arra jogosult személyek férnek hozzá</p>

                    <p><strong>8. Az érintettek jogai</strong><br>
                    A vásárlót az alábbi jogok illetik meg:<br>
                    hozzáférés a saját adataihoz<br>
                    adatok helyesbítése<br>
                    adatok törlése<br>
                    adatkezelés korlátozása<br>
                    tiltakozás az adatkezelés ellen</p>

                    <p><strong>9. Jogorvoslati lehetőség</strong><br>
                    Amennyiben a Vásárló úgy érzi, hogy személyes adatai kezelésével kapcsolatban jogsérelem érte, panaszt tehet a következő hatóságnál:<br>
                    👉 Nemzeti Adatvédelmi és Információszabadság Hatóság (NAIH)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var more = document.getElementById('intro-more');
            var btn = document.getElementById('btn-bovebben');
            if (more && btn) {
                btn.addEventListener('click', function() {
                    more.classList.toggle('open');
                    btn.textContent = more.classList.contains('open') ? 'Kevesebb' : 'Bővebben';
                });
            }
        })();

        (function () {
            var modal = document.getElementById('productMoreInfoModal');
            if (!modal) return;
            modal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) return;
                var title = button.getAttribute('data-product-title') || '';
                var moreinfoId = button.getAttribute('data-product-moreinfo-id') || '';
                var imageUrl = button.getAttribute('data-product-image') || '';
                var modalTitle = modal.querySelector('.modal-title');
                var modalBody = document.getElementById('productMoreInfoContent');
                if (modalTitle) modalTitle.textContent = title;
                if (modalBody) {
                    var source = moreinfoId ? document.getElementById('product-moreinfo-' + moreinfoId) : null;
                    var html = '';
                    if (imageUrl) {
                        var safeTitle = title.replace(/"/g, '&quot;');
                        html += '<img src="' + imageUrl + '" alt="' + safeTitle + '" class="img-fluid float-start me-3 mb-2" style="max-width: 140px; height: auto;">';
                    }
                    if (source) {
                        html += source.innerHTML;
                    }
                    modalBody.innerHTML = html;
                }
            });
        })();

        // Egyszerű kosárkezelés (utánvét, rendelés mentése adatbázisba)
        (function () {
            var cart = [];

            function formatPrice(value) {
                var num = parseInt(value, 10) || 0;
                return num.toLocaleString('hu-HU') + ' Ft';
            }

            function updateCartBadge() {
                var badge = document.getElementById('cart-count-badge');
                if (!badge) return;
                var count = cart.reduce(function (sum, item) { return sum + item.qty; }, 0);
                badge.textContent = count;
                badge.style.visibility = count > 0 ? 'visible' : 'hidden';
            }

            function renderCart() {
                var emptyEl = document.getElementById('cart-empty');
                var contentEl = document.getElementById('cart-content');
                var tbody = document.getElementById('cart-items-body');
                var totalEl = document.getElementById('cart-total');
                if (!emptyEl || !contentEl || !tbody || !totalEl) return;

                if (cart.length === 0) {
                    emptyEl.classList.remove('d-none');
                    contentEl.classList.add('d-none');
                    return;
                }

                emptyEl.classList.add('d-none');
                contentEl.classList.remove('d-none');

                tbody.innerHTML = '';
                var total = 0;
                cart.forEach(function (item, index) {
                    var lineTotal = item.price * item.qty;
                    total += lineTotal;
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td>' + item.title + '</td>' +
                        '<td class="text-end">' + formatPrice(item.price) + '</td>' +
                        '<td class="text-center">' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary me-1 btn-cart-dec" data-index="' + index + '">-</button>' +
                            '<span>' + item.qty + '</span>' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary ms-1 btn-cart-inc" data-index="' + index + '">+</button>' +
                        '</td>' +
                        '<td class="text-end">' + formatPrice(lineTotal) + '</td>' +
                        '<td class="text-end">' +
                            '<button type="button" class="btn btn-sm btn-link text-danger btn-cart-remove" data-index="' + index + '">Eltávolítás</button>' +
                        '</td>';
                    tbody.appendChild(tr);
                });

                totalEl.textContent = formatPrice(total);
            }

            function addToCart(product) {
                var existing = cart.find(function (item) { return item.id === product.id; });
                if (existing) {
                    existing.qty += 1;
                } else {
                    cart.push({ id: product.id, title: product.title, price: product.price, qty: 1 });
                }
                updateCartBadge();
                renderCart();
            }

            document.addEventListener('click', function (event) {
                var target = event.target;

                // Kosárba gomb
                if (target.classList.contains('btn-add-to-cart')) {
                    var id = parseInt(target.getAttribute('data-product-id'), 10);
                    var title = target.getAttribute('data-product-title') || '';
                    var price = parseInt(target.getAttribute('data-product-price'), 10) || 0;
                    addToCart({ id: id, title: title, price: price });

                    // Kosár popup automatikus megnyitása
                    var cartModalEl = document.getElementById('cartModal');
                    if (cartModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modalInstance = bootstrap.Modal.getOrCreateInstance(cartModalEl);
                        modalInstance.show();
                    }
                }

                // Mennyiség növelése/csökkentése, eltávolítás
                if (target.classList.contains('btn-cart-inc')) {
                    var incIndex = parseInt(target.getAttribute('data-index'), 10);
                    if (!isNaN(incIndex) && cart[incIndex]) {
                        cart[incIndex].qty += 1;
                        updateCartBadge();
                        renderCart();
                    }
                }
                if (target.classList.contains('btn-cart-dec')) {
                    var decIndex = parseInt(target.getAttribute('data-index'), 10);
                    if (!isNaN(decIndex) && cart[decIndex]) {
                        cart[decIndex].qty -= 1;
                        if (cart[decIndex].qty <= 0) {
                            cart.splice(decIndex, 1);
                        }
                        updateCartBadge();
                        renderCart();
                    }
                }
                if (target.classList.contains('btn-cart-remove')) {
                    var remIndex = parseInt(target.getAttribute('data-index'), 10);
                    if (!isNaN(remIndex)) {
                        cart.splice(remIndex, 1);
                        updateCartBadge();
                        renderCart();
                    }
                }
            });

            // Rendelés elküldése (utánvétel) – mentés az adatbázisba
            var orderForm = document.getElementById('cart-order-form');
            if (orderForm) {
                orderForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (cart.length === 0) {
                        alert('A kosár üres, nem tudsz rendelést leadni.');
                        return;
                    }

                    var nameEl = document.getElementById('orderName');
                    var phoneEl = document.getElementById('orderPhone');
                    var shippingEl = document.getElementById('orderShippingAddress');
                    var billingEl = document.getElementById('orderBillingAddress');
                    var noteEl = document.getElementById('orderNote');
                    var paymentEl = document.getElementById('orderPaymentMethod');

                    var name = nameEl ? nameEl.value.trim() : '';
                    var phone = phoneEl ? phoneEl.value.trim() : '';
                    var shippingAddress = shippingEl ? shippingEl.value.trim() : '';
                    var billingAddress = billingEl ? billingEl.value.trim() : '';
                    var note = noteEl ? noteEl.value.trim() : '';
                    var paymentMethod = paymentEl ? paymentEl.value : 'cod';

                    if (!name || !phone || !shippingAddress || !billingAddress) {
                        alert('Kérjük, töltsd ki a kötelező mezőket (név, telefon, szállítási és számlázási cím).');
                        return;
                    }

                    var total = cart.reduce(function (sum, item) {
                        return sum + (item.price * item.qty);
                    }, 0);

                    var payload = {
                        name: name,
                        phone: phone,
                        shipping_address: shippingAddress,
                        billing_address: billingAddress,
                        items: cart,
                        total_price: total,
                        payment_method: paymentMethod,
                        note: note
                    };

                    fetch('{{ route('orders.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    }).then(function (response) {
                        if (!response.ok) {
                            throw new Error('Hiba történt a rendelés mentése közben.');
                        }
                        return response.json();
                    }).then(function (data) {
                        if (!data.success) {
                            throw new Error('Nem sikerült rögzíteni a rendelést.');
                        }
                        var paymentText = paymentMethod === 'otp' ? 'OTP kártyás' : 'utánvétes';
                        alert('Köszönjük! Rendelésedet ' + paymentText + ' fizetéssel rögzítettük. (Rendelés azonosító: #' + data.order_id + ')');
                        cart = [];
                        orderForm.reset();
                        updateCartBadge();
                        renderCart();
                        var cartModalEl = document.getElementById('cartModal');
                        if (cartModalEl) {
                            var modalInstance = bootstrap.Modal.getInstance(cartModalEl);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }
                    }).catch(function (error) {
                        console.error(error);
                        alert('Sajnos hiba történt a rendelés mentése közben. Kérjük, próbáld meg később újra, vagy vedd fel velünk a kapcsolatot telefonon.');
                    });
                });
            }

            // Induláskor elrejtjük a jelvényt, ha üres
            updateCartBadge();
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
