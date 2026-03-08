<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Triem Dragonherbs – Gyógynövények a Mátra aljáról</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-display { font-family: 'Cormorant Garamond', serif; }
        .header-bg {
            background-image: url("{{ asset('img/boritokep.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .header-overlay { background: linear-gradient(135deg, rgba(34, 85, 51, 0.75) 0%, rgba(22, 55, 34, 0.85) 100%); }
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
    </style>
</head>
<body class="antialiased text-gray-800 bg-stone-50">

    {{-- Header --}}
    <header class="relative min-h-[70vh] md:min-h-[75vh] flex flex-col header-bg">
        <div class="absolute inset-0 header-overlay"></div>
        <div class="relative z-10 flex-1 flex flex-col justify-between px-4 py-8 md:px-8 md:py-12">
            <nav class="absolute top-4 right-4 md:top-8 md:right-8 flex items-center gap-3">
                @auth
                    <a href="{{ route('home') }}" class="text-white/95 hover:text-white text-sm font-medium">Kezdőlap</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white/95 hover:text-white text-sm font-medium underline">Kijelentkezés</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-white/95 hover:text-white text-sm font-medium">Bejelentkezés</a>
                    <a href="{{ route('register') }}" class="text-white bg-emerald-700/90 hover:bg-emerald-600 px-3 py-1.5 rounded-lg text-sm font-medium">Regisztráció</a>
                @endauth
            </nav>
            <div class="max-w-5xl">
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold text-white drop-shadow-lg">
                    Triem Dragonherbs Webshop
                </h1>
                <p class="mt-3 md:mt-4 font-display text-xl md:text-2xl text-green-100 italic max-w-2xl">
                    Gyökereink a földben, hitünk a minőségben.
                </p>
            </div>
            <div class="flex justify-end mt-6 -mb-16 md:-mb-20 relative z-20">
                <img src="{{ asset('img/logo.jpg') }}" alt="Triem Dragonherbs logo" class="logo-circle">
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

        {{-- Termékek carousel (products táblából, nyilakkal) --}}
        <section class="bg-white border-y border-emerald-100 py-12 md:py-16">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="font-display text-3xl md:text-4xl font-semibold text-emerald-900 mb-8 text-center">Termékeink</h2>
                @if(isset($products) && $products->isNotEmpty())
                    <div class="relative">
                        <div class="carousel-viewport" id="carousel-viewport">
                            <div class="carousel-track flex" id="carousel-track">
                                @foreach($products as $product)
                                    <article class="carousel-slide px-2 sm:px-3">
                                        <div class="bg-stone-50 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow border border-emerald-100 h-full flex flex-col">
                                            @if($product->image)
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover">
                                            @else
                                                <div class="w-full h-48 bg-emerald-100 flex items-center justify-center text-emerald-700 text-sm">Nincs kép</div>
                                            @endif
                                            <div class="p-4 flex-1 flex flex-col">
                                                <h3 class="font-display text-xl font-semibold text-emerald-900">{{ $product->title }}</h3>
                                                <p class="text-sm text-gray-600 mt-1 flex-1">{{ Str::limit($product->intro, 120) }}</p>
                                                <p class="mt-3 font-semibold text-emerald-800">{{ number_format($product->ar) }} Ft</p>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" id="carousel-prev" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-1 sm:translate-x-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/95 shadow-lg border border-emerald-200 text-emerald-800 hover:bg-emerald-50 flex items-center justify-center z-10" aria-label="Előző">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" id="carousel-next" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1 sm:translate-x-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/95 shadow-lg border border-emerald-200 text-emerald-800 hover:bg-emerald-50 flex items-center justify-center z-10" aria-label="Következő">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">Jelenleg nincs megjeleníthető termék.</p>
                @endif
            </div>
        </section>

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
            <p class="mt-8 text-emerald-200/90 text-sm">© {{ date('Y') }} Triem Dragonherbs. Minden jog fenntartva.</p>
        </div>
    </footer>

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

        (function() {
            var viewport = document.getElementById('carousel-viewport');
            var track = document.getElementById('carousel-track');
            var prevBtn = document.getElementById('carousel-prev');
            var nextBtn = document.getElementById('carousel-next');
            if (!viewport || !track || !prevBtn || !nextBtn) return;
            var slides = track.querySelectorAll('.carousel-slide');
            var total = slides.length;
            if (total === 0) return;

            function getVisibleCount() {
                var w = window.innerWidth;
                if (w >= 1024) return 4;
                if (w >= 640) return 2;
                return 1;
            }

            var currentIndex = 0;
            function updateTransform() {
                var visible = getVisibleCount();
                var maxIndex = Math.max(0, total - visible);
                currentIndex = Math.min(currentIndex, maxIndex);
                currentIndex = Math.max(0, currentIndex);
                track.style.width = (total * 100 / visible) + '%';
                var movePercent = (currentIndex * 100) / total;
                track.style.transform = 'translateX(-' + movePercent + '%)';
                prevBtn.style.visibility = currentIndex === 0 ? 'hidden' : 'visible';
                nextBtn.style.visibility = currentIndex >= maxIndex ? 'hidden' : 'visible';
            }

            prevBtn.addEventListener('click', function() {
                currentIndex--;
                updateTransform();
            });
            nextBtn.addEventListener('click', function() {
                currentIndex++;
                updateTransform();
            });
            window.addEventListener('resize', updateTransform);
            updateTransform();
        })();
    </script>
</body>
</html>
