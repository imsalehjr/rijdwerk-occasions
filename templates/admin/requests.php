<?php

use App\Labels;

$statusTabs = ['' => 'Alle', 'open' => 'Open'] + Labels::REQUEST_STATUS;
?>
<header class="admin-head">
    <div>
        <h1>Aanvragen</h1>
        <p class="muted"><?= (int) $total ?> <?= $total === 1 ? 'aanvraag' : 'aanvragen' ?></p>
    </div>
</header>

<div class="filter-bar">
    <form method="get" action="<?= e(url('admin/aanvragen')) ?>" class="filter-form">
        <div class="field">
            <label for="r-type">Soort</label>
            <select class="select" id="r-type" name="type" data-autosubmit>
                <option value="">Alle soorten</option>
                <?php foreach (Labels::REQUEST_TYPE as $k => $l): ?><option value="<?= e($k) ?>" <?= $type === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="r-status">Status</label>
            <select class="select" id="r-status" name="status" data-autosubmit>
                <?php foreach ($statusTabs as $k => $l): ?><option value="<?= e($k) ?>" <?= $status === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="field field--grow">
            <label for="r-search">Zoeken</label>
            <div class="search-field">
                <?= icon('search') ?>
                <input class="input" type="search" id="r-search" name="zoek" value="<?= e($search) ?>" placeholder="Naam, e-mail, referentie of auto">
            </div>
        </div>
        <button class="btn btn--ghost" type="submit">Filteren</button>
    </form>
</div>

<?php if (!$requests): ?>
    <div class="empty"><h2>Geen aanvragen gevonden</h2><p class="muted">Pas de filters aan.</p></div>
<?php else: ?>
<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th scope="col">Referentie</th>
                <th scope="col">Klant</th>
                <th scope="col">Auto</th>
                <th scope="col">Voorkeur</th>
                <th scope="col">Status</th>
                <th scope="col">Ontvangen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $r): ?>
                <tr class="<?= $r['status'] === 'new' ? 'is-new' : '' ?>">
                    <td>
                        <a class="row-link" href="<?= e(url('admin/aanvragen/' . $r['id'])) ?>">
                            <span class="type-icon type-icon--<?= e($r['type']) ?>"><?= icon($r['type'] === 'test_drive' ? 'calendar' : 'mail') ?></span>
                            <span><strong class="num"><?= e($r['reference']) ?></strong><span class="muted small"><?= e(Labels::REQUEST_TYPE[$r['type']]) ?></span></span>
                        </a>
                    </td>
                    <td><strong><?= e($r['name']) ?></strong><span class="muted small"><?= e($r['email']) ?></span></td>
                    <td><?= e($r['car_label']) ?></td>
                    <td class="num"><?= $r['preferred_date'] ? e(date_nl($r['preferred_date'])) . '<span class="muted small">' . e($r['preferred_time']) . '</span>' : '<span class="muted">–</span>' ?></td>
                    <td><span class="req-status req-status--<?= e($r['status']) ?>"><?= e(Labels::REQUEST_STATUS[$r['status']]) ?></span><?php if ($r['notes']): ?><span class="muted small"><?= (int) $r['notes'] ?> notitie<?= $r['notes'] > 1 ? 's' : '' ?></span><?php endif; ?></td>
                    <td class="num"><?= e(date_nl($r['created_at'], true)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= \App\View::fetch('admin/_pagination', ['pages' => $pages, 'page' => $page, 'path' => 'admin/aanvragen', 'query' => ['type' => $type, 'status' => $status, 'zoek' => $search]]) ?>
<?php endif; ?>
