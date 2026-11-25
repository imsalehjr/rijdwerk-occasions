<?php
$messages = [
    404 => 'Deze pagina bestaat niet (meer). Misschien is de auto verkocht of is de link veranderd.',
    403 => 'Je hebt geen toegang tot deze pagina.',
    500 => 'Er ging aan onze kant iets mis. Probeer het zo opnieuw.',
];
?>
<section class="wrap error-page">
    <p class="code" aria-hidden="true"><?= (int) $code ?></p>
    <h1><?= e($title) ?></h1>
    <p class="lead"><?= e($message ?? ($messages[$code] ?? '')) ?></p>
    <p><a class="btn" href="<?= e(url('occasions')) ?>">Naar het aanbod</a> <a class="btn btn--ghost" href="<?= e(url('/')) ?>">Naar de homepage</a></p>
</section>
