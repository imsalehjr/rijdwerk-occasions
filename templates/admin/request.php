<?php

use App\Business;
use App\Labels;

$r = $request;
$isTestDrive = $r['type'] === 'test_drive';
$noteError = error_for('body');
$confirmError = error_for('confirm');
?>
<header class="admin-head">
    <div>
        <ol class="breadcrumb"><li><a href="<?= e(url('admin/aanvragen')) ?>">Aanvragen</a></li><li><span aria-current="page"><?= e($r['reference']) ?></span></li></ol>
        <h1><?= e(Labels::REQUEST_TYPE[$r['type']]) ?> van <?= e($r['name']) ?></h1>
        <p class="muted"><span class="num"><?= e($r['reference']) ?></span> · ontvangen <?= e(date_nl($r['created_at'], true)) ?></p>
    </div>
    <span class="req-status req-status--<?= e($r['status']) ?> req-status--lg"><?= e(Labels::REQUEST_STATUS[$r['status']]) ?></span>
</header>

<div class="admin-grid admin-grid--detail">
    <div class="stack">
        <section class="panel">
            <div class="panel__head"><h2>Aanvraag</h2></div>
            <dl class="detail-list">
                <div><dt>Auto</dt><dd>
                    <?= e($r['car_label']) ?>
                    <?php if ($r['car_slug']): ?>
                        <span class="status status--<?= e($r['car_status']) ?>"><?= e(Labels::status($r['car_status'])) ?></span>
                        <a href="<?= e(url('admin/autos/' . $r['car_id'])) ?>">Beheer</a> · <a href="<?= e(url('occasions/' . $r['car_slug'])) ?>" target="_blank" rel="noopener">Website</a>
                    <?php else: ?>
                        <span class="muted">(auto verwijderd)</span>
                    <?php endif; ?>
                </dd></div>
                <?php if ($isTestDrive): ?>
                    <div><dt>Voorkeur</dt><dd class="num"><strong><?= e(date_nl($r['preferred_date'])) ?> om <?= e($r['preferred_time']) ?></strong></dd></div>
                <?php endif; ?>
                <div><dt>Bericht</dt><dd class="message"><?= $r['message'] !== '' ? nl2br(e($r['message'])) : '<span class="muted">Geen bericht</span>' ?></dd></div>
            </dl>
        </section>

        <section class="panel" id="notities">
            <div class="panel__head"><h2>Interne notities</h2><span class="muted small">Alleen zichtbaar voor medewerkers</span></div>
            <form method="post" action="<?= e(url('admin/aanvragen/' . $r['id'] . '/notitie')) ?>" class="note-form">
                <?= csrf_field() ?>
                <label class="visually-hidden" for="note">Nieuwe notitie</label>
                <textarea class="textarea" id="note" name="body" rows="3" maxlength="2000" placeholder="Bijv. gebeld, komt zaterdag om 11:00" required <?= $noteError ? 'aria-invalid="true" aria-describedby="note-error"' : '' ?>><?= e(old('body')) ?></textarea>
                <?php if ($noteError): ?><span class="field__error" id="note-error"><?= e($noteError) ?></span><?php endif; ?>
                <button class="btn btn--small" type="submit">Notitie toevoegen</button>
            </form>
            <?php if ($notes): ?>
                <ol class="timeline">
                    <?php foreach ($notes as $note): ?>
                        <li><p><?= nl2br(e($note['body'])) ?></p><span class="muted small"><?= e($note['author'] ?? 'Onbekend') ?> · <?= e(date_nl($note['created_at'], true)) ?></span></li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </section>
    </div>

    <div class="stack">
        <section class="panel">
            <div class="panel__head"><h2>Klant</h2></div>
            <dl class="detail-list detail-list--compact">
                <div><dt>Naam</dt><dd><?= e($r['name']) ?></dd></div>
                <div><dt>E-mail</dt><dd><a href="mailto:<?= e($r['email']) ?>?subject=<?= e(rawurlencode('Je aanvraag ' . $r['reference'])) ?>"><?= e($r['email']) ?></a></dd></div>
                <div><dt>Telefoon</dt><dd><?= $r['phone'] !== '' ? '<a href="tel:' . e(preg_replace('/[^0-9+]/', '', $r['phone'])) . '">' . e($r['phone']) . '</a>' : '<span class="muted">–</span>' ?></dd></div>
                <div><dt>Toestemming</dt><dd><?= e(date_nl($r['consent_at'], true)) ?></dd></div>
            </dl>
        </section>

        <section class="panel">
            <div class="panel__head"><h2>Status</h2></div>
            <form method="post" action="<?= e(url('admin/aanvragen/' . $r['id'] . '/status')) ?>" class="status-form">
                <?= csrf_field() ?>
                <fieldset class="status-options">
                    <legend class="visually-hidden">Kies een status</legend>
                    <?php foreach (Labels::REQUEST_STATUS as $k => $l): ?>
                        <label><input type="radio" name="status" value="<?= e($k) ?>" <?= $r['status'] === $k ? 'checked' : '' ?>><span class="req-status req-status--<?= e($k) ?>"><?= e($l) ?></span></label>
                    <?php endforeach; ?>
                </fieldset>
                <button class="btn btn--small btn--block" type="submit">Status opslaan</button>
            </form>
            <h3 class="h-xs">Geschiedenis</h3>
            <ol class="timeline timeline--compact">
                <?php foreach ($log as $entry): ?>
                    <li><span><?= $entry['from_status'] ? e(Labels::REQUEST_STATUS[$entry['from_status']] ?? $entry['from_status']) . ' → ' : 'Ontvangen als ' ?><strong><?= e(Labels::REQUEST_STATUS[$entry['to_status']] ?? $entry['to_status']) ?></strong></span><span class="muted small"><?= e($entry['author'] ?? 'Website') ?> · <?= e(date_nl($entry['created_at'], true)) ?></span></li>
                <?php endforeach; ?>
            </ol>
        </section>

        <section class="panel panel--danger" id="verwijderen">
            <div class="panel__head"><h2>Gegevens verwijderen (AVG)</h2></div>
            <p class="muted small">Verwijdert deze aanvraag, de notities en de geschiedenis definitief. Gebruik dit als de klant om verwijdering vraagt.</p>
            <form method="post" action="<?= e(url('admin/aanvragen/' . $r['id'] . '/verwijderen')) ?>" class="form-grid">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="confirm">Typ <strong class="num"><?= e($r['reference']) ?></strong> om te bevestigen</label>
                    <input class="input" id="confirm" name="confirm" autocomplete="off" <?= $confirmError ? 'aria-invalid="true" aria-describedby="confirm-error"' : '' ?>>
                    <?php if ($confirmError): ?><span class="field__error" id="confirm-error"><?= e($confirmError) ?></span><?php endif; ?>
                </div>
                <button class="btn btn--danger btn--small" type="submit"><?= icon('trash') ?>Definitief verwijderen</button>
            </form>
        </section>
    </div>
</div>
