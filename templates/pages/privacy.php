<?php

use App\Business;

?>
<section class="page-head wrap">
    <h1>Privacyverklaring</h1>
    <p class="lead">Hoe Rijdwerk Occasions omgaat met jouw gegevens. Laatst bijgewerkt op 25 november 2026.</p>
</section>

<section class="section">
    <div class="wrap prose">
        <h2>Wie zijn wij?</h2>
        <p><?= e(Business::NAME) ?>, <?= e(Business::STREET . ', ' . Business::POSTCODE . ' ' . Business::CITY) ?>, KvK <?= e(Business::KVK) ?>, is verantwoordelijk voor de verwerking van persoonsgegevens via deze website. Vragen over privacy stel je via <a href="mailto:<?= e(Business::EMAIL) ?>"><?= e(Business::EMAIL) ?></a>.</p>

        <h2>Welke gegevens we verwerken en waarom</h2>
        <h3>Proefrit aanvragen en vragen over een auto</h3>
        <p>We verwerken je naam, e-mailadres, telefoonnummer, de gekozen auto, je voorkeursdatum en -tijd en je bericht. We gebruiken deze gegevens alleen om je aanvraag te beantwoorden en de proefrit in te plannen. De grondslag is het nemen van stappen op jouw verzoek, voordat je eventueel een koopovereenkomst sluit (artikel 6 lid 1 sub b AVG). Je geeft ook uitdrukkelijk toestemming via het vinkje bij het formulier.</p>
        <h3>Beveiliging en misbruikpreventie</h3>
        <p>Om spam en misbruik te voorkomen, slaan we tijdelijk een versleutelde code van je IP-adres op (een zogenaamde hash). Daaruit is je IP-adres niet terug te halen. Deze codes verwijderen we automatisch na 24 uur. De grondslag is ons gerechtvaardigd belang om de website veilig te houden.</p>

        <h2>Hoe lang we gegevens bewaren</h2>
        <ul>
            <li>Aanvragen die niet tot een koop leiden: maximaal 12 maanden na het laatste contact.</li>
            <li>Gegevens bij een koop: 7 jaar, omdat de fiscale bewaarplicht dat vereist.</li>
            <li>Beveiligingscodes van IP-adressen: 24 uur.</li>
        </ul>

        <h2>Cookies en opslag in je browser</h2>
        <p>We gebruiken geen tracking-, analyse- of advertentiecookies. Er worden geen gegevens gedeeld met Google, Meta of andere partijen. We gebruiken alleen:</p>
        <ul>
            <li>Eén functionele sessiecookie, die nodig is om formulieren veilig te versturen. Die verdwijnt als je je browser sluit.</li>
            <li>Lokale opslag in je browser (localStorage) voor je favorieten, de auto’s die je vergelijkt en recent bekeken auto’s. Deze gegevens blijven op je eigen apparaat en worden niet naar ons verstuurd. Je kunt ze wissen via de instellingen van je browser.</li>
        </ul>
        <p>Lettertypen en kaarten laden we vanaf onze eigen server, dus ook daarbij gaan er geen gegevens naar derden.</p>

        <h2>Met wie we gegevens delen</h2>
        <p>We verkopen je gegevens nooit. Alleen onze hostingpartij en e-maildienst verwerken gegevens in onze opdracht, binnen de EU en met een verwerkersovereenkomst. Als je kiest voor financiering, sturen we je gegevens alleen met jouw toestemming door naar de kredietverstrekker.</p>

        <h2>Beveiliging</h2>
        <p>De website gebruikt een versleutelde verbinding (HTTPS). Formulieren zijn beschermd tegen misbruik, wachtwoorden van medewerkers zijn versleuteld opgeslagen en alleen medewerkers met een eigen account kunnen aanvragen inzien.</p>

        <h2>Jouw rechten</h2>
        <p>Je mag je gegevens inzien, laten aanpassen of laten verwijderen. Je kunt ook bezwaar maken tegen het gebruik ervan of vragen om een kopie. Stuur hiervoor een mail naar <a href="mailto:<?= e(Business::EMAIL) ?>"><?= e(Business::EMAIL) ?></a>. We reageren binnen vier weken. Ben je niet tevreden over hoe we met je gegevens omgaan? Dan kun je een klacht indienen bij de Autoriteit Persoonsgegevens.</p>

        <p class="muted u-mt">Let op: dit is een demowebsite van een fictief bedrijf. Gebruik de formulieren niet met echte gegevens.</p>
    </div>
</section>
