<?php

use App\Business;

?>
<section class="page-head wrap">
    <h1>Contact en route</h1>
    <p class="lead">Je bent zonder afspraak welkom in de showroom. Voor een proefrit plan je vooraf een moment, dan staat de auto voor je klaar.</p>
</section>

<section class="section">
    <div class="wrap split split--top">
        <div class="block-stack">
            <div>
                <h2 class="h-sm">Adres</h2>
                <p><?= e(Business::NAME) ?><br><?= e(Business::STREET) ?><br><?= e(Business::POSTCODE . ' ' . Business::CITY) ?></p>
                <p>
                    <a class="btn btn--ghost btn--small" href="tel:<?= e(Business::PHONE_E164) ?>"><?= icon('phone') ?><?= e(Business::PHONE) ?></a>
                    <a class="btn btn--ghost btn--small" href="mailto:<?= e(Business::EMAIL) ?>"><?= icon('mail') ?><?= e(Business::EMAIL) ?></a>
                </p>
            </div>
            <div>
                <h2 class="h-sm">Openingstijden</h2>
                <?= \App\View::partial('hours') ?>
                <p class="muted u-mt-sm"><?= Business::isOpenNow() ? 'Nu geopend.' : 'Nu gesloten. Een bericht via de site kan altijd.' ?></p>
            </div>
            <div>
                <h2 class="h-sm">Zo rijd je naar ons toe</h2>
                <p>Neem op de N279 de afslag Bremdonk-Noord. Na de rotonde rechtsaf de Kanaaldijk op; we zitten na 400 meter aan de linkerkant, naast de oude sluis. Parkeren kan voor de deur.</p>
            </div>
        </div>
        <figure class="map-card">
            <?= \App\View::partial('map') ?>
            <figcaption>Schematische kaart. We gebruiken geen externe kaartdienst, zodat er geen gegevens naar derden gaan.</figcaption>
        </figure>
    </div>
</section>
