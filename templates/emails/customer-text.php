<?php

use App\Business;

$isTestDrive = $type === 'test_drive';
?>
Hoi <?= $name ?>,

<?= $isTestDrive ? "Bedankt voor je proefritaanvraag. We bellen je binnen één werkdag om het moment te bevestigen." : "Bedankt voor je vraag. Je krijgt binnen één werkdag antwoord van ons verkoopteam." ?>


Referentie: <?= $reference ?>

Auto: <?= $carLabel ?>

<?php if ($isTestDrive): ?>Voorkeur: <?= date_nl($date) ?> om <?= $time ?>

<?php endif; ?>
<?php if ($message !== ''): ?>
<?= $isTestDrive ? 'Opmerking' : 'Je vraag' ?>:
<?= $message ?>

<?php endif; ?>

Wil je iets wijzigen? Antwoord op deze mail of bel <?= Business::PHONE ?>.

Groet,
Het team van Rijdwerk
