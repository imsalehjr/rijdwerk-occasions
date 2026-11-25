<?php

use App\Labels;
use App\Plate;

$tabs = ['' => 'Alle'] + Labels::STATUS;
$allCount = array_sum($counts);
?>
<header class="admin-head">
    <div>
        <h1>Auto’s</h1>
        <p class="muted"><?= (int) $allCount ?> in totaal</p>
    </div>
    <a class="btn btn--accent" href="<?= e(url('admin/autos/nieuw')) ?>"><?= icon('plus') ?>Auto toevoegen</a>
</header>

<div class="filter-bar">
    <nav class="seg-tabs" aria-label="Filter op status">
        <?php foreach ($tabs as $key => $label): ?>
            <a href="<?= e(url('admin/autos', ['status' => $key, 'zoek' => $search])) ?>" <?= $status === $key ? 'aria-current="page"' : '' ?>><?= e($label) ?> <span class="muted"><?= (int) ($key === '' ? $allCount : ($counts[$key] ?? 0)) ?></span></a>
        <?php endforeach; ?>
    </nav>
    <form method="get" action="<?= e(url('admin/autos')) ?>" class="search-field" role="search">
        <?php if ($status): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
        <?= icon('search') ?>
        <label class="visually-hidden" for="car-search">Zoek op merk, model of kenteken</label>
        <input class="input" type="search" id="car-search" name="zoek" value="<?= e($search) ?>" placeholder="Merk, model of kenteken">
    </form>
</div>

<?php if (!$cars): ?>
    <div class="empty"><h2>Geen auto’s gevonden</h2><p class="muted">Pas het filter aan of voeg een nieuwe auto toe.</p></div>
<?php else: ?>
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th scope="col">Auto</th>
                <th scope="col">Kenteken</th>
                <th scope="col" class="num">Prijs</th>
                <th scope="col" class="num">Km-stand</th>
                <th scope="col" class="num">Bekeken</th>
                <th scope="col">Status</th>
                <th scope="col"><span class="visually-hidden">Acties</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cars as $car): ?>
                <tr>
                    <td>
                        <a class="car-cell" href="<?= e(url('admin/autos/' . $car['id'])) ?>">
                            <?php if ($car['photo']): ?>
                                <img src="<?= e(upload_url(substr($car['photo'], 0, 32) . '-640.webp')) ?>" width="72" height="54" alt="">
                            <?php else: ?>
                                <span class="no-photo" title="Geen foto’s"><?= icon('upload') ?></span>
                            <?php endif; ?>
                            <span>
                                <strong><?= e($car['brand'] . ' ' . $car['model']) ?></strong>
                                <span class="muted small"><?= e($car['variant']) ?> · <?= (int) $car['year'] ?></span>
                                <span class="tags">
                                    <?php if ($car['is_featured']): ?><span class="tag tag--accent"><?= icon('star') ?>Uitgelicht</span><?php endif; ?>
                                    <?php if (!$car['is_published']): ?><span class="tag">Verborgen</span><?php endif; ?>
                                    <?php if ((int) $car['photo_count'] === 0): ?><span class="tag tag--warn">Geen foto’s</span><?php endif; ?>
                                </span>
                            </span>
                        </a>
                    </td>
                    <td><?php if ($car['plate']): ?><span class="plate-mini"><?= e(Plate::format($car['plate'])) ?></span><?php else: ?><span class="muted">–</span><?php endif; ?></td>
                    <td class="num"><?= e(money($car['price'])) ?></td>
                    <td class="num"><?= e(number_nl($car['mileage_km'])) ?></td>
                    <td class="num"><?= e(number_nl($car['view_count'])) ?></td>
                    <td>
                        <form method="post" action="<?= e(url('admin/autos/' . $car['id'] . '/status')) ?>" class="quick-status" data-quick-status>
                            <?= csrf_field() ?>
                            <label class="visually-hidden" for="status-<?= (int) $car['id'] ?>">Status van <?= e($car['brand'] . ' ' . $car['model']) ?></label>
                            <select class="select select--status status-select--<?= e($car['status']) ?>" id="status-<?= (int) $car['id'] ?>" name="status">
                                <?php foreach (Labels::STATUS as $key => $label): ?>
                                    <option value="<?= e($key) ?>" <?= $car['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <noscript><button class="btn btn--small" type="submit">Opslaan</button></noscript>
                        </form>
                    </td>
                    <td class="actions">
                        <a class="icon-btn" href="<?= e(url('occasions/' . $car['slug'])) ?>" target="_blank" rel="noopener"><?= icon('eye') ?><span class="visually-hidden">Bekijk op website</span></a>
                        <a class="btn btn--ghost btn--small" href="<?= e(url('admin/autos/' . $car['id'])) ?>">Bewerken</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= \App\View::fetch('admin/_pagination', ['pages' => $pages, 'page' => $page, 'path' => 'admin/autos', 'query' => ['status' => $status, 'zoek' => $search]]) ?>
<?php endif; ?>
