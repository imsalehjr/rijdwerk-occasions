<?php

use App\CarFilter;
use App\View;

$chips = $filter->activeChips();
$countLabel = $result['total'] === 1 ? '1 occasion' : number_nl($result['total']) . ' occasions';
?>
<div class="toolbar">
    <p class="toolbar__count" aria-live="polite" data-result-count="<?= (int) $result['total'] ?>"><?= e($countLabel) ?></p>
    <button type="button" class="btn btn--ghost btn--small filters-open" data-filters-open aria-controls="filters" aria-expanded="false">
        <?= icon('filter') ?> Filters<?= count($chips) ? ' (' . count($chips) . ')' : '' ?>
    </button>
    <label class="visually-hidden" for="sort">Sorteren</label>
    <select class="select" id="sort" name="sorteer" form="filters" data-autosubmit>
        <?php foreach (CarFilter::SORTS as $key => $sort): ?>
            <option value="<?= e($key) ?>" <?= $filter->sort === $key ? 'selected' : '' ?>><?= e($sort['label']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php if ($chips): ?>
    <ul class="chips-active" aria-label="Actieve filters">
        <?php foreach ($chips as $chip): ?>
            <li><a href="<?= e(url('occasions', $filter->toQuery($chip['remove'] + ['pagina' => null]))) ?>" data-page-link><?= e($chip['label']) ?><?= icon('close') ?><span class="visually-hidden"> (filter verwijderen)</span></a></li>
        <?php endforeach; ?>
        <li><a class="reset" href="<?= e(url('occasions')) ?>" data-page-link>Alles wissen</a></li>
    </ul>
<?php endif; ?>

<?php if (!$result['items']): ?>
    <div class="empty">
        <h2>Geen auto’s die hierbij passen</h2>
        <p class="muted">Haal een filter weg of verruim je budget. Je kunt ons ook vragen om een auto voor je te zoeken.</p>
        <?php if ($chips): ?>
            <a class="btn btn--ghost" href="<?= e(url('occasions', $filter->toQuery(end($chips)['remove'] + ['pagina' => null]))) ?>" data-page-link>Verwijder “<?= e(end($chips)['label']) ?>”</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="car-grid">
        <?php foreach ($result['items'] as $i => $car): ?>
            <?= View::partial('car-card', ['car' => $car, 'headingTag' => 'h2', 'eager' => $i < 3]) ?>
        <?php endforeach; ?>
    </div>
    <?= View::partial('pagination', ['pages' => $result['pages'], 'page' => $result['page'], 'filter' => $filter]) ?>
<?php endif; ?>
