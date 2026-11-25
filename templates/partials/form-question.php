<?php

use App\SpamGuard;

$fieldError = static fn (string $key) => $errors[$key] ?? null;
$invalid = static fn (string $key) => isset($errors[$key]) ? 'aria-invalid="true" aria-describedby="q-' . $key . '-error"' : '';
?>
<form action="<?= e(url('occasions/' . $car['slug'] . '/vraag')) ?>" method="post" novalidate data-request-form>
    <?= csrf_field() ?>
    <?= SpamGuard::fields('question') ?>
    <input type="hidden" name="_form" value="question">
    <?php if ($errors): ?>
        <div class="form-errors" role="alert" tabindex="-1" data-form-errors>Controleer de velden die rood zijn gemarkeerd.</div>
    <?php endif; ?>
    <div class="form-grid form-grid--2">
        <div class="field span-2">
            <label for="q-message">Je vraag</label>
            <textarea class="textarea" id="q-message" name="message" maxlength="2000" rows="5" required placeholder="Bijvoorbeeld over inruil, onderhoud of bezorging" <?= $invalid('message') ?>><?= e(old('message')) ?></textarea>
            <?php if ($fieldError('message')): ?><span class="field__error" id="q-message-error"><?= e($fieldError('message')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="q-name">Naam</label>
            <input class="input" type="text" id="q-name" name="name" value="<?= e(old('name')) ?>" autocomplete="name" maxlength="100" required <?= $invalid('name') ?>>
            <?php if ($fieldError('name')): ?><span class="field__error" id="q-name-error"><?= e($fieldError('name')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="q-phone">Telefoon <span class="muted">(optioneel)</span></label>
            <input class="input" type="tel" id="q-phone" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel" maxlength="30" <?= $invalid('phone') ?>>
            <?php if ($fieldError('phone')): ?><span class="field__error" id="q-phone-error"><?= e($fieldError('phone')) ?></span><?php endif; ?>
        </div>
        <div class="field span-2">
            <label for="q-email">E-mailadres</label>
            <input class="input" type="email" id="q-email" name="email" value="<?= e(old('email')) ?>" autocomplete="email" maxlength="190" required <?= $invalid('email') ?>>
            <?php if ($fieldError('email')): ?><span class="field__error" id="q-email-error"><?= e($fieldError('email')) ?></span><?php endif; ?>
        </div>
        <div class="field span-2">
            <label class="check"><input type="checkbox" name="consent" value="1" required <?= old('consent') === '1' ? 'checked' : '' ?> <?= $invalid('consent') ?>><span>Ik ga akkoord dat Rijdwerk mijn gegevens gebruikt om mijn vraag te beantwoorden. Lees de <a href="<?= e(url('privacy')) ?>" target="_blank" rel="noopener">privacyverklaring</a>.</span></label>
            <?php if ($fieldError('consent')): ?><span class="field__error" id="q-consent-error"><?= e($fieldError('consent')) ?></span><?php endif; ?>
        </div>
        <div class="span-2">
            <button class="btn btn--block" type="submit" data-submit-label="Vraag wordt verstuurd…">Vraag versturen</button>
        </div>
    </div>
</form>
