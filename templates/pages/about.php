<?php

use App\Business;

?>
<section class="page-head wrap">
    <h1>Een garage die ook verkoopt</h1>
    <p class="lead">Rijdwerk begon in 2009 als werkplaats aan de Kanaaldijk in <?= e(Business::CITY) ?>. Klanten vroegen steeds vaker of we een goede occasion voor ze wisten. Inmiddels staan er zo’n twintig auto’s in de showroom, en elke auto komt eerst bij ons op de brug.</p>
</section>

<section class="section">
    <div class="wrap split">
        <?php if ($photo): ?>
            <img src="<?= e(upload_url($photo)) ?>" width="1600" height="1200" alt="Een van onze occasions in de showroom" loading="lazy">
        <?php endif; ?>
        <div class="prose">
            <h2>Wat we bij elke auto doen</h2>
            <p>Voordat een auto online komt, gaat hij door dezelfde controle. We schrijven op wat we vinden en wat we vervangen. Dat lees je terug bij de auto.</p>
            <ul>
                <li>Controle van meer dan 80 punten, van remmen tot airco</li>
                <li>Onderhoudsbeurt met nieuwe olie en filters</li>
                <li>Nieuwe APK bij aflevering</li>
                <li>Bij elektrische auto’s een accutest met rapport</li>
                <li>Kilometerstand gecontroleerd via het NAP</li>
            </ul>
        </div>
    </div>
</section>

<section class="section band">
    <div class="wrap">
        <div class="section-head"><h2>Waar je bij ons op kunt rekenen</h2></div>
        <dl class="facts facts--wide">
            <div><dt>garantie op elke auto, bij jongere auto’s 24 maanden</dt><dd>12 maanden</dd></div>
            <div><dt>vaste prijs, zonder afleverkosten achteraf</dt><dd>Alles inbegrepen</dd></div>
            <div><dt>binnen 50 km van Bremdonk</dt><dd>Gratis bezorgd</dd></div>
            <div><dt>ook als je bij ons geen auto koopt</dt><dd>Eerlijk inruilbod</dd></div>
        </dl>
    </div>
</section>

<section class="section">
    <div class="wrap split">
        <div class="prose">
            <h2>Het team</h2>
            <p>Joost en Esra helpen je in de showroom. Monteurs Bas en Ilse staan in de werkplaats en weten van elke auto precies wat eraan gedaan is. Heb je een technische vraag over een occasion? Dan loopt een van hen even mee.</p>
            <p><a class="btn" href="<?= e(url('occasions')) ?>">Bekijk het aanbod</a></p>
        </div>
        <div>
            <h2 class="visually-hidden">Openingstijden</h2>
            <?= \App\View::partial('hours') ?>
        </div>
    </div>
</section>
