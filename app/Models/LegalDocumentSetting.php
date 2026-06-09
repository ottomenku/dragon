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
Fizetési módok: banki átutalás, utánvét, bankkártyás fizetés (ha elérhető).</p>

<p><strong>5. Szállítás</strong><br>
A rendeléseket a Szolgáltató futárszolgálattal, csomagküldő szolgáltatással vagy személyes átvétellel juttatja el a Vásárlóhoz.<br>
Szállítási idő: 5–10 munkanap.<br>
A pontos szállítási költség a rendelés során kerül feltüntetésre.</p>

<p><strong>6. Elállási jog</strong><br>
A Vásárlót a megrendeléstől számított 14 napon belül elállási jog illeti meg.<br>
Elállás esetén a Vásárló köteles a terméket sértetlen állapotban visszajuttatni.<br>
A visszaküldés költsége a Vásárlót terheli.<br>
Az elállási jog nem gyakorolható felbontott termékek, illetve higiéniai okokból nem visszazárható termékek (pl. bontott illóolaj) esetén.</p>

<p><strong>7. Termék visszaküldés</strong><br>
A visszaküldött terméknek sértetlennek, eredeti csomagolásban lévőnek és használatmentesnek kell lennie.</p>

<p><strong>8. Felelősség</strong><br>
A Szolgáltató nem vállal felelősséget a nem rendeltetésszerű használatból eredő károkért és a termékek helytelen alkalmazásáért.</p>

<p><strong>9. Adatkezelés</strong><br>
A személyes adatok kezelése a hatályos adatvédelmi jogszabályoknak megfelelően történik.</p>

<p><strong>10. Jogviták</strong><br>
A felek a vitás kérdéseket elsősorban békés úton rendezik. Ennek hiányában a hatáskörrel rendelkező magyar bíróság illetékes.</p>
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
Nemzeti Adatvédelmi és Információszabadság Hatóság (NAIH)</p>
HTML;
    }
}
