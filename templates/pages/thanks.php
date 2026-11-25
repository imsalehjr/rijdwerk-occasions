<?php $isTestDrive = $thanks['type'] === 'test_drive'; ?>
<section class="wrap">
    <div class="thanks">
        <span class="thanks__check"><?= icon('check') ?></span>
        <h1><?= $isTestDrive ? 'Je proefrit is aangevraagd' : 'Je vraag is verstuurd' ?></h1>
        <p class="lead">
            <?php if ($isTestDrive && !empty($thanks['date'])): ?>
                We bellen je binnen één werkdag om <?= e(date_nl($thanks['date'])) ?> om <?= e($thanks['time']) ?> te bevestigen voor de <?= e($thanks['car']) ?>.
            <?php elseif ($isTestDrive): ?>
                We bellen je binnen één werkdag om je proefrit in de <?= e($thanks['car']) ?> te bevestigen.
            <?php else: ?>
                Je krijgt binnen één werkdag antwoord over de <?= e($thanks['car']) ?>.
            <?php endif; ?>
            Een bevestiging staat in je mailbox.
        </p>
        <?php if (!empty($thanks['reference'])): ?>
            <p class="reference" aria-label="Referentienummer <?= e($thanks['reference']) ?>"><?= icon('inbox') ?><?= e($thanks['reference']) ?></p>
        <?php endif; ?>
        <p><a class="btn" href="<?= e(url('occasions')) ?>">Verder kijken in het aanbod</a></p>
    </div>
</section>
