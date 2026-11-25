<?php

use App\Business;

require __DIR__ . '/_style.php';
$isTestDrive = $type === 'test_drive';
?>
<!doctype html>
<html lang="nl"><head><meta charset="utf-8"><title><?= e($subject) ?></title></head>
<body style="margin:0;<?= $wrap ?>">
<div style="<?= $card ?>">
    <p style="margin:0 0 4px;font-size:20px;font-weight:700;">Rijdwerk Occasions</p>
    <p style="margin:0 0 24px;color:#56636e;font-size:14px;"><?= e(Business::STREET . ', ' . Business::POSTCODE . ' ' . Business::CITY) ?></p>
    <p>Hoi <?= e($name) ?>,</p>
    <?php if ($isTestDrive): ?>
        <p>Bedankt voor je proefritaanvraag. We bellen je binnen één werkdag om het moment te bevestigen. Tot die tijd is de proefrit nog niet definitief.</p>
    <?php else: ?>
        <p>Bedankt voor je vraag. Je krijgt binnen één werkdag antwoord van ons verkoopteam.</p>
    <?php endif; ?>
    <table style="<?= $table ?>" role="presentation">
        <tr><th style="<?= $th ?>">Referentie</th><td style="<?= $td ?>"><?= e($reference) ?></td></tr>
        <tr><th style="<?= $th ?>">Auto</th><td style="<?= $td ?>"><a href="<?= e($carUrl) ?>" style="color:#7e3a11;"><?= e($carLabel) ?></a></td></tr>
        <?php if ($isTestDrive): ?>
            <tr><th style="<?= $th ?>">Voorkeur</th><td style="<?= $td ?>"><?= e(date_nl($date)) ?> om <?= e($time) ?></td></tr>
        <?php endif; ?>
        <?php if ($message !== ''): ?>
            <tr><th style="<?= $th ?>"><?= $isTestDrive ? 'Opmerking' : 'Je vraag' ?></th><td style="<?= $td ?>font-weight:400;"><?= nl2br(e($message)) ?></td></tr>
        <?php endif; ?>
    </table>
    <p>Wil je iets wijzigen? Antwoord op deze mail of bel <?= e(Business::PHONE) ?>.</p>
    <p style="margin-bottom:0;">Groet,<br>Het team van Rijdwerk</p>
</div>
<p style="max-width:560px;margin:16px auto 0;font-size:12px;color:#56636e;text-align:center;">Je ontvangt deze mail omdat je een aanvraag deed op onze website. Demowebsite: dit bedrijf is fictief.</p>
</body></html>
