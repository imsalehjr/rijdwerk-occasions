Nieuwe <?= $type === 'test_drive' ? 'proefrit' : 'vraag' ?>: <?= $reference ?>


Auto: <?= $carLabel ?>

Naam: <?= $name ?>

E-mail: <?= $email ?>

Telefoon: <?= $phone !== '' ? $phone : '-' ?>

<?php if ($type === 'test_drive'): ?>Voorkeur: <?= date_nl($date) ?> om <?= $time ?>

<?php endif; ?>
Bericht:
<?= $message !== '' ? $message : '-' ?>

