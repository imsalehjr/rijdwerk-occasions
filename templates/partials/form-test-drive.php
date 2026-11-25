<?php

use App\Labels;
use App\SpamGuard;

$fieldError = static fn (string $key) => $errors[$key] ?? null;
$invalid = static fn (string $key) => isset($errors[$key]) ? 'aria-invalid="true" aria-describedby="td-' . $key . '-error"' : '';
$oldTime = old('preferred_time');
?>
<form action="<?= e(url('occasions/' . $car['slug'] . '/proefrit')) ?>" method="post" novalidate data-request-form>
    <?= csrf_field() ?>
    <?= SpamGuard::fields('test_drive') ?>
    <input type="hidden" name="_form" value="test_drive">
    <?php if ($errors): ?>
        <div class="form-errors" role="alert" tabindex="-1" data-form-errors>Controleer de velden die rood zijn gemarkeerd.</div>
    <?php endif; ?>
    <div class="form-grid form-grid--2">
        <div class="field">
            <label for="td-date">Datum</label>
            <input class="input" type="date" id="td-date" name="preferred_date" min="<?= e($minDate) ?>" max="<?= e($maxDate) ?>" value="<?= e(old('preferred_date')) ?>" required <?= $invalid('preferred_date') ?> data-date-input>
            <span class="field__hint">Maandag t/m zaterdag, tot 30 dagen vooruit.</span>
            <?php if ($fieldError('preferred_date')): ?><span class="field__error" id="td-preferred_date-error"><?= e($fieldError('preferred_date')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <fieldset class="slots" data-slots="weekday">
                <legend class="field__label">Tijd (ma–vr)</legend>
                <?php foreach (Labels::TIME_SLOTS as $slot): ?>
                    <label><input type="radio" name="preferred_time" value="<?= e($slot) ?>" <?= $oldTime === $slot ? 'checked' : '' ?>><span><?= e($slot) ?></span></label>
                <?php endforeach; ?>
            </fieldset>
            <fieldset class="slots" data-slots="saturday">
                <legend class="field__label">Tijd (za)</legend>
                <?php foreach (Labels::SATURDAY_SLOTS as $slot): ?>
                    <label><input type="radio" name="preferred_time" value="<?= e($slot) ?>" <?= $oldTime === $slot ? 'checked' : '' ?>><span><?= e($slot) ?></span></label>
                <?php endforeach; ?>
            </fieldset>
            <?php if ($fieldError('preferred_time')): ?><span class="field__error" id="td-preferred_time-error"><?= e($fieldError('preferred_time')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="td-name">Naam</label>
            <input class="input" type="text" id="td-name" name="name" value="<?= e(old('name')) ?>" autocomplete="name" maxlength="100" required <?= $invalid('name') ?>>
            <?php if ($fieldError('name')): ?><span class="field__error" id="td-name-error"><?= e($fieldError('name')) ?></span><?php endif; ?>
        </div>
        <div class="field">
            <label for="td-phone">Telefoon</label>
            <input class="input" type="tel" id="td-phone" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel" maxlength="30" required <?= $invalid('phone') ?>>
            <?php if ($fieldError('phone')): ?><span class="field__error" id="td-phone-error"><?= e($fieldError('phone')) ?></span><?php endif; ?>
        </div>
        <div class="field span-2">
            <label for="td-email">E-mailadres</label>
            <input class="input" type="email" id="td-email" name="email" value="<?= e(old('email')) ?>" autocomplete="email" maxlength="190" required <?= $invalid('email') ?>>
            <?php if ($fieldError('email')): ?><span class="field__error" id="td-email-error"><?= e($fieldError('email')) ?></span><?php endif; ?>
        </div>
        <div class="field span-2">
            <label for="td-message">Opmerking <span class="muted">(optioneel)</span></label>
            <textarea class="textarea" id="td-message" name="message" maxlength="2000" rows="3" <?= $invalid('message') ?>><?= e(old('message')) ?></textarea>
            <?php if ($fieldError('message')): ?><span class="field__error" id="td-message-error"><?= e($fieldError('message')) ?></span><?php endif; ?>
        </div>
        <div class="field span-2">
            <label class="check"><input type="checkbox" name="consent" value="1" required <?= old('consent') === '1' ? 'checked' : '' ?> <?= $invalid('consent') ?>><span>Ik ga akkoord dat Rijdwerk mijn gegevens gebruikt om deze aanvraag af te handelen. Lees de <a href="<?= e(url('privacy')) ?>" target="_blank" rel="noopener">privacyverklaring</a>.</span></label>
            <?php if ($fieldError('consent')): ?><span class="field__error" id="td-consent-error"><?= e($fieldError('consent')) ?></span><?php endif; ?>
        </div>
        <div class="span-2">
            <button class="btn btn--accent btn--block" type="submit" data-submit-label="Proefrit wordt verstuurd…">Proefrit aanvragen</button>
        </div>
    </div>
</form>
