<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalDocumentSetting extends Model
{
    protected $fillable = [
        'aszf_content',
        'shipping_terms_content',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'aszf_content' => static::defaultAszfContent(),
            'shipping_terms_content' => static::defaultShippingTermsContent(),
        ]);
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
}
