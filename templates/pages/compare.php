<?php

use App\Cars;
use App\Labels;

$rows = [
    'Prijs' => ['key' => 'price', 'fmt' => static fn ($c) => money($c['price']), 'best' => 'min'],
    'Bouwjaar' => ['key' => 'year', 'fmt' => static fn ($c) => (string) $c['year'], 'best' => 'max'],
    'Kilometerstand' => ['key' => 'mileage_km', 'fmt' => static fn ($c) => number_nl($c['mileage_km']) . ' km', 'best' => 'min'],
    'Vermogen' => ['key' => 'power_kw', 'fmt' => static fn ($c) => $c['power_kw'] ? round($c['power_kw'] * 1.36) . ' pk' : '–', 'best' => 'max'],
    'Brandstof' => ['fmt' => static fn ($c) => Labels::fuel($c['fuel'])],
    'Transmissie' => ['fmt' => static fn ($c) => Labels::transmission($c['transmission'])],
    'Carrosserie' => ['fmt' => static fn ($c) => Labels::body($c['body_type'])],
    'Actieradius' => ['key' => 'range_km', 'fmt' => static fn ($c) => $c['range_km'] ? number_nl($c['range_km']) . ' km' : '–', 'best' => 'max'],
    'Zitplaatsen' => ['key' => 'seats', 'fmt' => static fn ($c) => $c['seats'] ? (string) $c['seats'] : '–', 'best' => 'max'],
    'Vorige eigenaren' => ['key' => 'owners', 'fmt' => static fn ($c) => $c['owners'] !== null ? (string) $c['owners'] : '–', 'best' => 'min'],
    'Garantie' => ['key' => 'warranty_months', 'fmt' => static fn ($c) => $c['warranty_months'] . ' maanden', 'best' => 'max'],
    'Status' => ['fmt' => static fn ($c) => Labels::status($c['status'])],
];
$bestOf = static function (array $cars, string $key, string $dir): ?float {
    $values = array_filter(array_map(static fn ($c) => $c[$key] === null ? null : (float) $c[$key], $cars), static fn ($v) => $v !== null && $v > 0);
    if (count($values) < 2 || count(array_unique($values)) < 2) {
        return null;
    }

    return $dir === 'min' ? min($values) : max($values);
};
?>
<section class="page-head wrap">
    <h1>Vergelijken</h1>
    <p class="lead">Zet tot drie auto’s naast elkaar. De beste waarde per regel is groen gemarkeerd.</p>
</section>
<section class="section wrap" data-compare-page data-has-ids="<?= $hasIds ? '1' : '0' ?>">
    <?php if (count($cars) < 1): ?>
        <div class="empty">
            <h2>Nog niets om te vergelijken</h2>
            <p class="muted">Tik bij een auto op het vergelijk-icoon. Je kunt maximaal drie auto’s naast elkaar zetten.</p>
            <a class="btn" href="<?= e(url('occasions')) ?>">Kies auto’s uit het aanbod</a>
        </div>
    <?php else: ?>
        <div class="compare-table-wrap">
            <table class="compare-table">
                <caption class="visually-hidden">Vergelijking van <?= count($cars) ?> auto’s</caption>
                <thead>
                    <tr>
                        <td></td>
                        <?php foreach ($cars as $car): ?>
                            <th scope="col">
                                <div class="car-mini">
                                    <?php if ($car['photo']): ?><img src="<?= e(upload_url(substr($car['photo'], 0, 32) . '-640.webp')) ?>" width="640" height="480" alt=""><?php endif; ?>
                                    <a href="<?= e(url('occasions/' . $car['slug'])) ?>"><?= e(Cars::title($car)) ?></a>
                                    <span class="muted"><?= e($car['variant']) ?></span>
                                    <button type="button" data-compare-remove="<?= (int) $car['id'] ?>">Verwijderen</button>
                                </div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $label => $row):
                        $best = isset($row['best']) ? $bestOf($cars, $row['key'], $row['best']) : null; ?>
                        <tr>
                            <th scope="row"><?= e($label) ?></th>
                            <?php foreach ($cars as $car):
                                $isBest = $best !== null && $car[$row['key']] !== null && (float) $car[$row['key']] === $best; ?>
                                <td class="<?= $isBest ? 'is-best' : '' ?>"><?= e($row['fmt']($car)) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($cars) < 3): ?>
            <p class="u-mt"><a class="btn btn--ghost" href="<?= e(url('occasions')) ?>"><?= icon('plus') ?>Auto toevoegen</a></p>
        <?php endif; ?>
    <?php endif; ?>
</section>
