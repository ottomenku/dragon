<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDocumentSetting extends Model
{
    protected $fillable = [
        'aszf_content',
        'shipping_terms_content',
        'gdpr_content',
    ];

    public static function current(): self
    {
        $settings = static::query()->firstOrCreate([], [
            'aszf_content' => static::defaultAszfContent(),
            'shipping_terms_content' => static::defaultShippingTermsContent(),
            'gdpr_content' => static::defaultGdprContent(),
        ]);

        if ($settings->gdpr_content === null || $settings->gdpr_content === '') {
            $settings->gdpr_content = static::defaultGdprContent();
            $settings->save();
        }

        return $settings;
    }

    public static function defaultAszfContent(): string
    {
        return <<<'HTML'
<p><strong>Általános Szerződési Feltételek (ÁSZF)</strong><br>
Hatályos: a webshop használatának időpontjától<br>
Webáruház: https://triemdragonherbs.hu</p>

<p><strong>1. A Szolgáltató adatai (üzemeltető)</strong><br>
Kereskedő neve: Triem Dragonherbs<br>
Jogi forma / üzemeltető: családi gazdaság<br>
Székhely: 3214 Nagyréde, Kossuth L. utca 17.<br>
Levelezési cím: 3214 Nagyréde, Kossuth L. utca 17.<br>
E-mail: medmo1973@gmail.com<br>
Telefon: +36 30 968 9532<br>
Adószám: [kérjük, töltse ki a tényleges adószámot az admin felületen]<br>
A webáruház üzemeltetője (Szolgáltató) a fenti adatok szerint.</p>

<p><strong>2. A szerződés tárgya</strong><br>
Jelen ÁSZF a Triem Dragonherbs webáruházban (https://triemdragonherbs.hu) történő termékvásárlás feltételeit szabályozza. A webáruház használatával és a rendelés leadásával a Vásárló (fogyasztó) kijelenti, hogy ismeri és elfogadja jelen ÁSZF rendelkezéseit. A szerződés a Polgári Törvénykönyvről szóló 2013. évi V. törvény (Ptk.) szerint távollevők között jön létre.</p>

<p><strong>3. Termékek</strong><br>
A webshopban gyógynövények, szárítmányok, illatvizek és illóolajok értékesítése történik. A termékleírások tájékoztató jellegűek, nem minősülnek gyógyhatásra vonatkozó állításnak, és nem helyettesítik az orvosi tanácsadást.</p>

<p><strong>4. Megrendelés, szerződéskötés</strong><br>
A vásárlás a webáruház felületén történik. A rendelés leadása előtt a Vásárló köteles elolvasni és elfogadni jelen ÁSZF-et és a szállítási feltételeket. A rendelés leadása ajánlattételnek minősül. A szerződés a Szolgáltató visszaigazolásával jön létre.</p>

<p><strong>5. Árak és fizetés</strong><br>
Az árak forintban (HUF) értendők, tartalmazzák az áfát. Elérhető fizetési módok: banki átutalás, utánvét, online bankkártyás fizetés.</p>

<p><strong>6. Online bankkártyás fizetés – Barion</strong><br>
A webáruházban az online bankkártyás fizetést a Barion Payment Zrt. (székhely: 1117 Budapest, Infopark sétány 1. I. épület 5. emelet; cégjegyzékszám: 01-10-048552; adószám: 25353192-2-43; web: https://www.barion.com/hu/) biztosítja.<br>
A bankkártyás fizetés során a Vásárlót a Barion biztonságos fizetőfelületére irányítjuk. A Szolgáltató bankkártya-adatokat nem kezel és nem tárol; a fizetéshez szükséges adatokat a Barion kezeli saját adatvédelmi szabályzata szerint.<br>
A sikeres fizetésről a Barion visszajelzést ad; a rendelés teljesítése a visszaigazolt fizetés után indul.<br>
A Barion fizetési szolgáltatás igénybevételére a Barion Payment Zrt. vonatkozó szerződési feltételei is érvényesek.</p>

<p><strong>7. Szállítás</strong><br>
A rendeléseket futárszolgálattal, csomagküldő szolgáltatással vagy egyedi megállapodás szerint személyes átvétellel teljesítjük. A szállítási feltételek külön dokumentumban, a rendelés során elfogadott szállítási feltételekben találhatók. A szállítási díj a rendeléskor külön tételként jelenik meg.</p>

<p><strong>8. Számlázás</strong><br>
A számlát a megrendelés teljesítését követően, jellemzően 8 napon belül állítjuk ki, és elektronikus úton megküldjük a Vásárló részére.</p>

<p><strong>9. Elállási jog</strong><br>
A fogyasztót a termék átvételétől számított 14 napon belül elállási jog illeti meg. Elállás esetén a terméket sértetlen, eredeti csomagolásban kell visszaküldeni; a visszaküldés költsége a Vásárlót terheli, kivéve, ha a Szolgáltató vállalja. Az elállási jog nem gyakorolható felbontott, illetve higiéniai okokból visszazárhatatlan termékek (pl. bontott illóolaj) esetén.</p>

<p><strong>10. Jótállás, szavatosság</strong><br>
A Vásárlót a hatályos fogyasztóvédelmi jogszabályok szerinti jogok illetik meg.</p>

<p><strong>11. Panaszkezelés, vitarendezés, békéltető testület</strong><br>
Panasz esetén kérjük, forduljon hozzánk: medmo1973@gmail.com, +36 30 968 9532.<br>
A fogyasztó békéltető testülethez fordulhat: Heves Megyei Kereskedelmi és Iparkamara Békéltető Testülete (3300 Eger, Bajcsy-Zsilinszky utca 17.; e-mail: bekeltetes@heveskim.hu; web: https://bekeltetes.hu).<br>
Online vitarendezés: az Európai Bizottság ODR platformja: https://ec.europa.eu/consumers/odr/<br>
A fogyasztó jogosult bírósághoz is fordulni. Illetékes bíróság: a fogyasztó lakóhelye szerinti törvényszék, illetve a hatályos jogszabályok szerint.</p>

<p><strong>12. Adatkezelés</strong><br>
Személyes adatok kezeléséről az Adatkezelési tájékoztató rendelkezik, amely a weboldalon elérhető.</p>

<p><strong>13. Záró rendelkezések</strong><br>
A Szolgáltató jogosult jelen ÁSZF-et egyoldalúan módosítani; a módosítás a weboldalon való közzététellel lép hatályba.</p>
HTML;
    }

    public static function defaultShippingTermsContent(): string
    {
        return <<<'HTML'
<p><strong>1. Szállítási terület</strong><br>
A webshopból leadott rendeléseket elsősorban Magyarország területére szállítjuk. Egyedi megállapodás esetén külföldi szállítás is lehetséges.</p>

<p><strong>2. Szállítási módok</strong><br>
A rendelés során választható szállítási módok: futárszolgálat (MPL, GLS, DHL, Foxpost, Packeta stb.), házhozszállítás vagy csomagpont / automata átvétel – az aktuálisan elérhető opciók szerint.</p>

<p><strong>3. Szállítási díj</strong><br>
A szállítási díj a rendelés véglegesítése előtt, a kosárban és a fizetési lépésben külön tételként jelenik meg. A díj a választott szállítási módtól függ.</p>

<p><strong>4. Szállítási idő</strong><br>
A csomagok feldolgozása általában 1–3 munkanapon belül megtörténik. A kiszállítás várható ideje 5–10 munkanap a rendelés visszaigazolásától számítva.</p>

<p><strong>5. Átvétel</strong><br>
Házhozszállítás esetén a futár a megadott címre kézbesít. Csomagpont / automata esetén a vásárló a kiválasztott ponton veheti át a csomagot az értesítést követően.</p>

<p><strong>6. Sérült csomag</strong><br>
Sérült vagy hiányos csomag átvételekor kérjük, jelezze azonnal a futárnál és írjon nekünk a medmo1973@gmail.com címre a rendelés azonosítójával.</p>

<p><em>Ez egy ideiglenes szállítási feltétel-szöveg. Az admin felületen szerkeszthető.</em></p>
HTML;
    }

    public static function defaultGdprContent(): string
    {
        return <<<'HTML'
<p><strong>Adatkezelési tájékoztató</strong><br>
Hatályos: a webshop használatának időpontjától<br>
Webáruház: https://triemdragonherbs.hu</p>

<p><strong>1. Az adatkezelő adatai</strong><br>
Név: Triem Dragonherbs (családi gazdaság)<br>
Székhely: 3214 Nagyréde, Kossuth L. utca 17.<br>
E-mail: medmo1973@gmail.com<br>
Telefon: +36 30 968 9532<br>
Adószám: [kérjük, töltse ki a tényleges adószámot az admin felületen]</p>

<p><strong>2. Az adatkezelés célja</strong><br>
Megrendelések teljesítése, kapcsolattartás, számlázás, szállítás megszervezése, jogszabályi kötelezettségek teljesítése, a webshop biztonságos működtetése és csalásmegelőzés (Barion Pixel).</p>

<p><strong>3. Kezelt személyes adatok köre</strong><br>
Név, e-mail cím, telefonszám, szállítási és számlázási cím, rendelési adatok, fizetéssel kapcsolatos tranzakciós azonosítók.</p>

<p><strong>4. Az adatkezelés jogalapja</strong><br>
Szerződés teljesítése (GDPR 6. cikk (1) b)), jogi kötelezettség (c), jogos érdek / csalásmegelőzés (f), illetve hozzájárulás (a), ahol szükséges.</p>

<p><strong>5. Online fizetés – Barion Payment Zrt.</strong><br>
Bankkártyás fizetés esetén a fizetési adatokat a Barion Payment Zrt. (1117 Budapest, Infopark sétány 1.; https://www.barion.com/hu/) kezeli adatkezelőként / adatfeldolgozóként. A Szolgáltató bankkártya-adatokat nem tárol. A Barion adatkezeléséről tájékoztatás: https://www.barion.com/hu/adatvedelem/</p>

<p><strong>6. Barion Pixel (csalásmegelőzés)</strong><br>
A weboldalon a Barion Payment Zrt. Alap (Base) Barion Pixel szolgáltatása fut csalásmegelőzési célból. A Pixel cookie-kat és eszköz-/használati adatokat gyűjthet (pl. IP-cím, böngésző, oldalmegtekintés, kosár- és fizetési események). Az adatkezelő e tekintetben a Barion Payment Zrt., az adatkezelés célja a csalások megelőzése és az online fizetés biztonsága. További információ: https://www.barion.com/hu/adatvedelem/ és https://docs.barion.com/</p>

<p><strong>7. Adattovábbítás</strong><br>
Adatok továbbítása történhet futárszolgálatoknak (szállítás), könyveléshez (számlázás), valamint a Barion Payment Zrt.-nek (online fizetés, csalásmegelőzés). Marketing célú adatértékesítés nem történik.</p>

<p><strong>8. Tárolás időtartama</strong><br>
A megrendelés teljesítéséig, illetve a számviteli és adózási jogszabályokban előírt ideig (jellemzően 8 év).</p>

<p><strong>9. Az érintettek jogai</strong><br>
Hozzáférés, helyesbítés, törlés, adatkezelés korlátozása, tiltakozás, adathordozhatóság – kérésre: medmo1973@gmail.com</p>

<p><strong>10. Jogorvoslat</strong><br>
Panasz tehető a Nemzeti Adatvédelmi és Információszabadság Hatóságnál (NAIH): https://naih.hu</p>
HTML;
    }
}
