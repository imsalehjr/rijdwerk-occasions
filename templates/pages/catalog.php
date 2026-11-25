<?php

use App\CarFilter;
use App\Labels;
use App\View;

$yearNow = (int) date('Y');
$years = range($yearNow, 2010);
$priceSteps = [5000, 7500, 10000, 12500, 15000, 17500, 20000, 25000, 30000, 35000, 40000, 50000, 60000, 75000];
$kmSteps = [10000, 25000, 50000, 75000, 100000, 125000, 150000, 200000];
?>
<div class="catalog">
    <div class="wrap">
        <div class="catalog__head">
            <div>
                <ol class="breadcrumb">
                    <li><a href="<?= e(url('/')) ?>">Home</a></li>
                    <li><span aria-current="page">Occasions</span></li>
                </ol>
                <h1><?= e($title === 'Occasions' ? 'Ons aanbod' : $title) ?></h1>
            </div>
        </div>

        <div class="catalog__layout">
            <form class="filters" id="filters" action="<?= e(url('occasions')) ?>" method="get" data-filters aria-label="Filters">
                <div class="filters__head">
                    <h2>Filters</h2>
                    <button type="button" class="icon-btn" data-filters-close><?= icon('close') ?><span class="visually-hidden">Filters sluiten</span></button>
                </div>

                <div class="filters__group">
                    <label class="filters__title" for="f-search">Zoeken</label>
                    <div class="search-field">
                        <?= icon('search') ?>
                        <input class="input" type="search" id="f-search" name="zoek" value="<?= e($filter->search) ?>" placeholder="Bijv. Golf of hybride" maxlength="60" autocomplete="off">
                    </div>
                </div>

                <div class="filters__group">
                    <label class="filters__title" for="f-brand">Merk en model</label>
                    <select class="select" id="f-brand" name="merk" data-brand-select>
                        <option value="">Alle merken</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= e($brand['slug']) ?>" <?= ($filter->brand['slug'] ?? '') === $brand['slug'] ? 'selected' : '' ?>><?= e($brand['name']) ?> (<?= (int) $brand['total'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <label class="visually-hidden" for="f-model">Model</label>
                    <select class="select" id="f-model" name="model" data-model-select <?= $filter->brand ? '' : 'disabled' ?>>
                        <option value="">Alle modellen</option>
                        <?php foreach (($models[$filter->brand['slug'] ?? ''] ?? []) as $model): ?>
                            <option value="<?= e($model['slug']) ?>" <?= ($filter->model['slug'] ?? '') === $model['slug'] ? 'selected' : '' ?>><?= e($model['name']) ?> (<?= (int) $model['total'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <script type="application/json" data-models-json><?= json_encode($models, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
                </div>

                <fieldset class="filters__group">
                    <legend class="filters__title">Prijs</legend>
                    <div class="filters__pair">
                        <div>
                            <label class="visually-hidden" for="f-pmin">Prijs vanaf</label>
                            <select class="select" id="f-pmin" name="prijs_min">
                                <option value="">Vanaf</option>
                                <?php foreach ($priceSteps as $p): ?><option value="<?= $p ?>" <?= $filter->priceMin === $p ? 'selected' : '' ?>><?= e(money($p)) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="visually-hidden" for="f-pmax">Prijs tot</label>
                            <select class="select" id="f-pmax" name="prijs_max">
                                <option value="">Tot</option>
                                <?php foreach ($priceSteps as $p): ?><option value="<?= $p ?>" <?= $filter->priceMax === $p ? 'selected' : '' ?>><?= e(money($p)) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="filters__group">
                    <legend class="filters__title">Bouwjaar</legend>
                    <div class="filters__pair">
                        <div>
                            <label class="visually-hidden" for="f-ymin">Bouwjaar vanaf</label>
                            <select class="select" id="f-ymin" name="bouwjaar_min">
                                <option value="">Vanaf</option>
                                <?php foreach ($years as $y): ?><option value="<?= $y ?>" <?= $filter->yearMin === $y ? 'selected' : '' ?>><?= $y ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="visually-hidden" for="f-ymax">Bouwjaar tot</label>
                            <select class="select" id="f-ymax" name="bouwjaar_max">
                                <option value="">Tot</option>
                                <?php foreach ($years as $y): ?><option value="<?= $y ?>" <?= $filter->yearMax === $y ? 'selected' : '' ?>><?= $y ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <div class="filters__group">
                    <label class="filters__title" for="f-km">Kilometerstand</label>
                    <select class="select" id="f-km" name="km_max">
                        <option value="">Geen maximum</option>
                        <?php foreach ($kmSteps as $k): ?><option value="<?= $k ?>" <?= $filter->mileageMax === $k ? 'selected' : '' ?>>Tot <?= e(number_nl($k)) ?> km</option><?php endforeach; ?>
                    </select>
                </div>

                <fieldset class="filters__group">
                    <legend class="filters__title">Brandstof</legend>
                    <div class="chip-group">
                        <?php foreach (Labels::FUEL as $key => $fuel): ?>
                            <label class="chip"><input type="checkbox" name="brandstof[]" value="<?= e($fuel['slug']) ?>" <?= in_array($key, $filter->fuels, true) ? 'checked' : '' ?>><span><?= e($fuel['label']) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <fieldset class="filters__group">
                    <legend class="filters__title">Transmissie</legend>
                    <div class="chip-group">
                        <label class="chip"><input type="radio" name="transmissie" value="" <?= $filter->transmission === null ? 'checked' : '' ?>><span>Alle</span></label>
                        <?php foreach (Labels::TRANSMISSION as $key => $t): ?>
                            <label class="chip"><input type="radio" name="transmissie" value="<?= e($t['slug']) ?>" <?= $filter->transmission === $key ? 'checked' : '' ?>><span><?= e($t['label']) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <fieldset class="filters__group">
                    <legend class="filters__title">Carrosserie</legend>
                    <div class="chip-group">
                        <?php foreach (Labels::BODY as $key => $body): ?>
                            <label class="chip"><input type="checkbox" name="carrosserie[]" value="<?= e($body['slug']) ?>" <?= in_array($key, $filter->bodies, true) ? 'checked' : '' ?>><span><?= e($body['label']) ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <noscript><button class="btn btn--block" type="submit">Filters toepassen</button></noscript>
                <div class="filters__foot">
                    <button class="btn btn--accent btn--block" type="submit" data-filters-apply>Toon <span data-apply-count><?= (int) $result['total'] ?></span> resultaten</button>
                </div>
            </form>
            <div class="scrim" data-scrim></div>

            <section class="results" id="results" aria-label="Zoekresultaten" data-results>
                <?= View::partial('results', ['filter' => $filter, 'result' => $result]) ?>
            </section>
        </div>
    </div>
</div>
