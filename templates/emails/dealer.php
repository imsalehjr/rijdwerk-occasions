<?php
require __DIR__ . '/_style.php';
$isTestDrive = $type === 'test_drive';
?>
<!doctype html>
<html lang="nl"><head><meta charset="utf-8"><title><?= e($subject) ?></title></head>
<body style="margin:0;<?= $wrap ?>">
<div style="<?= $card ?>">
    <p style="margin:0 0 16px;font-size:20px;font-weight:700;">Nieuwe <?= $isTestDrive ? 'proefrit' : 'vraag' ?>: <?= e($reference) ?></p>
    <table style="<?= $table ?>" role="presentation">
        <tr><th style="<?= $th ?>">Auto</th><td style="<?= $td ?>"><?= e($carLabel) ?><br><span style="font-weight:400;color:#56636e;">Status: <?= e(\App\Labels::status($car['status'])) ?></span></td></tr>
        <tr><th style="<?= $th ?>">Naam</th><td style="<?= $td ?>"><?= e($name) ?></td></tr>
        <tr><th style="<?= $th ?>">E-mail</th><td style="<?= $td ?>"><?= e($email) ?></td></tr>
        <tr><th style="<?= $th ?>">Telefoon</th><td style="<?= $td ?>"><?= e($phone !== '' ? $phone : '–') ?></td></tr>
        <?php if ($isTestDrive): ?>
            <tr><th style="<?= $th ?>">Voorkeur</th><td style="<?= $td ?>"><?= e(date_nl($date)) ?> om <?= e($time) ?></td></tr>
        <?php endif; ?>
        <tr><th style="<?= $th ?>">Bericht</th><td style="<?= $td ?>font-weight:400;"><?= $message !== '' ? nl2br(e($message)) : '–' ?></td></tr>
    </table>
    <p style="margin-bottom:0;">Behandel de aanvraag in de beheeromgeving onder Aanvragen.</p>
</div>
</body></html>
