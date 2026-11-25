<?php

use App\Labels;

$maxDay = max(1, max($days));
$maxViews = max(1, (int) ($mostViewed[0]['view_count'] ?? 1));
$weekdays = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];
?>
<header class="admin-head">
    <div>
        <h1><?= e((int) date('H') < 12 ? 'Goedemorgen' : ((int) date('H') < 18 ? 'Goedemiddag' : 'Goedenavond')) ?>, <?= e(explode(' ', \App\Auth::name())[0]) ?></h1>
        <p class="muted"><?= e(ucfirst(date_nl(date('Y-m-d')))) ?></p>
    </div>
    <a class="btn btn--accent" href="<?= e(url('admin/autos/nieuw')) ?>"><?= icon('plus') ?>Auto toevoegen</a>
</header>

<section class="stat-row" aria-label="Kerncijfers">
    <a class="stat" href="<?= e(url('admin/aanvragen', ['status' => 'new'])) ?>">
        <span class="stat__label">Nieuwe aanvragen</span>
        <span class="stat__value"><?= (int) $requestStats['new_total'] ?></span>
        <span class="stat__meta"><?= (int) $requestStats['new_test_drives'] ?> proefritten · <?= (int) $requestStats['open_total'] ?> open in totaal</span>
    </a>
    <?php foreach (Labels::STATUS as $key => $label): ?>
        <a class="stat" href="<?= e(url('admin/autos', ['status' => $key])) ?>">
            <span class="stat__label"><span class="status status--<?= e($key) ?>"><?= e($label) ?></span></span>
            <span class="stat__value"><?= (int) ($statusCounts[$key] ?? 0) ?></span>
            <span class="stat__meta">auto’s</span>
        </a>
    <?php endforeach; ?>
</section>

<div class="admin-grid">
    <section class="panel">
        <div class="panel__head">
            <h2>Aanvragen afgelopen 14 dagen</h2>
            <span class="muted"><?= (int) $requestStats['last7'] ?> in de laatste 7 dagen</span>
        </div>
        <div class="bars" role="img" aria-label="Aantal aanvragen per dag, de afgelopen 14 dagen. Totaal <?= array_sum($days) ?>.">
            <?php foreach ($days as $day => $count): ?>
                <div class="bars__col">
                    <span class="bars__value"><?= $count ?: '' ?></span>
                    <span class="bars__bar" data-height="<?= round($count / $maxDay * 100) ?>"></span>
                    <span class="bars__label"><?= e($weekdays[(int) date('N', strtotime($day)) - 1]) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h2>Komende proefritten</h2>
            <a href="<?= e(url('admin/aanvragen', ['type' => 'test_drive', 'status' => 'open'])) ?>">Alle</a>
        </div>
        <?php if (!$upcoming): ?>
            <p class="muted">Er staan geen proefritten gepland.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($upcoming as $r): ?>
                    <li>
                        <a href="<?= e(url('admin/aanvragen/' . $r['id'])) ?>">
                            <span class="date-chip"><strong><?= e(date('j', strtotime($r['preferred_date']))) ?></strong><?= e(substr(date_nl($r['preferred_date']), strpos(date_nl($r['preferred_date']), ' ') + 1, 3)) ?></span>
                            <span class="list__main"><strong><?= e($r['name']) ?></strong><span class="muted"><?= e($r['car_label']) ?></span></span>
                            <span class="list__side"><span class="num"><?= e($r['preferred_time']) ?></span><span class="req-status req-status--<?= e($r['status']) ?>"><?= e(Labels::REQUEST_STATUS[$r['status']]) ?></span></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel__head"><h2>Meest bekeken</h2><span class="muted">unieke bezoeken</span></div>
        <ol class="ranking">
            <?php foreach ($mostViewed as $car): ?>
                <li>
                    <a href="<?= e(url('admin/autos/' . $car['id'])) ?>">
                        <?php if ($car['photo']): ?><img src="<?= e(upload_url(substr($car['photo'], 0, 32) . '-640.webp')) ?>" width="64" height="48" alt=""><?php endif; ?>
                        <span class="ranking__main">
                            <strong><?= e($car['brand'] . ' ' . $car['model']) ?></strong>
                            <span class="meter"><span data-width="<?= round($car['view_count'] / $maxViews * 100) ?>"></span></span>
                        </span>
                        <span class="num"><?= e(number_nl($car['view_count'])) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>

    <section class="panel">
        <div class="panel__head"><h2>Laatste aanvragen</h2><a href="<?= e(url('admin/aanvragen')) ?>">Alle</a></div>
        <ul class="list">
            <?php foreach ($recent as $r): ?>
                <li>
                    <a href="<?= e(url('admin/aanvragen/' . $r['id'])) ?>">
                        <span class="type-icon type-icon--<?= e($r['type']) ?>"><?= icon($r['type'] === 'test_drive' ? 'calendar' : 'mail') ?></span>
                        <span class="list__main"><strong><?= e($r['name']) ?></strong><span class="muted"><?= e(Labels::REQUEST_TYPE[$r['type']]) ?> · <?= e($r['car_label']) ?></span></span>
                        <span class="list__side"><span class="req-status req-status--<?= e($r['status']) ?>"><?= e(Labels::REQUEST_STATUS[$r['status']]) ?></span><span class="muted small"><?= e(date_nl($r['created_at'])) ?></span></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
