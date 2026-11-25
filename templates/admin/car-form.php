<?php

use App\ImageUpload;
use App\Labels;
use App\Plate;

$isEdit = $car !== null;
$fromOld = old('_submitted') === '1';
$val = static function (string $key, string $default = '') use ($fromOld, $car): string {
    if ($fromOld) {
        return old($key, $default);
    }
    $value = $car[$key] ?? null;

    return $value === null ? $default : (string) $value;
};
$checked = static fn (string $key, bool $default) => $fromOld ? old($key) === '1' : ($car ? (bool) $car[$key] : $default);
$selectedFeatures = $fromOld ? array_map('intval', array_filter(explode(',', old('features')))) : $selectedFeatures;
$errs = errors();
$err = static fn (string $key) => isset($errs[$key]) ? '<span class="field__error" id="err-' . e($key) . '">' . e($errs[$key]) . '</span>' : '';
$inv = static fn (string $key) => isset($errs[$key]) ? 'aria-invalid="true" aria-describedby="err-' . e($key) . '"' : '';
$action = $isEdit ? url('admin/autos/' . $car['id']) : url('admin/autos/nieuw');
$plateValue = $fromOld ? old('plate') : Plate::format($car['plate'] ?? null);
?>
<header class="admin-head">
    <div>
        <ol class="breadcrumb"><li><a href="<?= e(url('admin/autos')) ?>">Auto’s</a></li><li><span aria-current="page"><?= $isEdit ? 'Bewerken' : 'Nieuw' ?></span></li></ol>
        <h1><?= e($isEdit ? $car['brand'] . ' ' . $car['model'] : 'Nieuwe auto') ?></h1>
        <?php if ($isEdit): ?><p class="muted"><?= e($car['variant']) ?> · <?= (int) $car['view_count'] ?> keer bekeken</p><?php endif; ?>
    </div>
    <?php if ($isEdit): ?>
        <a class="btn btn--ghost" href="<?= e(url('occasions/' . $car['slug'])) ?>" target="_blank" rel="noopener"><?= icon('eye') ?>Bekijk op website</a>
    <?php endif; ?>
</header>

<?php if ($errs): ?>
    <p class="alert alert--error" role="alert" tabindex="-1" data-focus-me><?= icon('info') ?>De auto is nog niet opgeslagen. Controleer <?= count($errs) === 1 ? 'het gemarkeerde veld' : 'de ' . count($errs) . ' gemarkeerde velden' ?>.</p>
<?php endif; ?>

<form method="post" action="<?= e($action) ?>" class="car-form" id="car-form" novalidate>
    <?= csrf_field() ?>

    <section class="panel plate-lookup" aria-labelledby="lookup-title" data-plate-lookup data-endpoint="<?= e(url('admin/kenteken')) ?>">
        <div class="panel__head">
            <div>
                <h2 id="lookup-title">Kenteken opzoeken bij de RDW</h2>
                <p class="muted small">Vult merk, model, bouwjaar, brandstof, kleur en meer automatisch in. Controleer de gegevens daarna altijd zelf.</p>
            </div>
        </div>
        <div class="plate-lookup__row">
            <label class="plate-input">
                <span class="plate-input__eu" aria-hidden="true">NL</span>
                <span class="visually-hidden">Kenteken</span>
                <input type="text" name="plate" id="plate" value="<?= e($plateValue) ?>" maxlength="10" autocomplete="off" spellcheck="false" placeholder="K-482-TB" <?= $inv('plate') ?> data-plate-input>
            </label>
            <button type="button" class="btn" data-plate-button><?= icon('search') ?><span>Gegevens ophalen</span></button>
        </div>
        <?= $err('plate') ?>
        <div class="lookup-result" aria-live="polite" data-plate-result></div>
    </section>

    <section class="panel" aria-labelledby="basic-title">
        <div class="panel__head"><h2 id="basic-title">Basisgegevens</h2></div>
        <div class="form-cols">
            <div class="field">
                <label for="brand">Merk</label>
                <input class="input" id="brand" name="brand" list="brand-list" value="<?= e($fromOld ? old('brand') : ($car['brand'] ?? '')) ?>" required maxlength="60" <?= $inv('brand') ?> data-rdw="brand">
                <datalist id="brand-list"><?php foreach ($brands as $b): ?><option value="<?= e($b) ?>"><?php endforeach; ?></datalist>
                <?= $err('brand') ?>
            </div>
            <div class="field">
                <label for="model">Model</label>
                <input class="input" id="model" name="model" list="model-list" value="<?= e($fromOld ? old('model') : ($car['model'] ?? '')) ?>" required maxlength="80" <?= $inv('model') ?> data-rdw="model">
                <datalist id="model-list"></datalist>
                <script type="application/json" data-models-json><?= json_encode($models, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
                <?= $err('model') ?>
            </div>
            <div class="field span-2">
                <label for="variant">Uitvoering</label>
                <input class="input" id="variant" name="variant" value="<?= e($val('variant')) ?>" maxlength="120" placeholder="Bijv. 1.5 TSI Style" <?= $inv('variant') ?>>
                <?= $err('variant') ?>
            </div>
            <div class="field">
                <label for="price">Prijs (€)</label>
                <input class="input num" id="price" name="price" inputmode="numeric" value="<?= e($val('price')) ?>" required <?= $inv('price') ?>>
                <?= $err('price') ?>
            </div>
            <div class="field">
                <label for="mileage_km">Kilometerstand</label>
                <input class="input num" id="mileage_km" name="mileage_km" inputmode="numeric" value="<?= e($val('mileage_km')) ?>" required <?= $inv('mileage_km') ?>>
                <?= $err('mileage_km') ?>
            </div>
            <div class="field">
                <label for="year">Bouwjaar</label>
                <input class="input num" id="year" name="year" inputmode="numeric" maxlength="4" value="<?= e($val('year')) ?>" required <?= $inv('year') ?> data-rdw="year">
                <?= $err('year') ?>
            </div>
            <div class="field">
                <label for="first_registration">Eerste toelating</label>
                <input class="input" type="date" id="first_registration" name="first_registration" value="<?= e($val('first_registration')) ?>" <?= $inv('first_registration') ?> data-rdw="first_registration">
                <?= $err('first_registration') ?>
            </div>
            <div class="field">
                <label for="status">Status</label>
                <select class="select" id="status" name="status" <?= $inv('status') ?>>
                    <?php foreach (Labels::STATUS as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $val('status', 'available') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= $err('status') ?>
            </div>
            <div class="field field--toggles">
                <span class="field__label">Zichtbaarheid</span>
                <label class="switch"><input type="checkbox" name="is_published" value="1" <?= $checked('is_published', true) ? 'checked' : '' ?>><span>Tonen op de website</span></label>
                <label class="switch"><input type="checkbox" name="is_featured" value="1" <?= $checked('is_featured', false) ? 'checked' : '' ?>><span>Uitlichten op de homepage</span></label>
            </div>
        </div>
    </section>

    <section class="panel" aria-labelledby="tech-title">
        <div class="panel__head"><h2 id="tech-title">Techniek</h2></div>
        <div class="form-cols form-cols--3">
            <?php foreach (['fuel' => ['Brandstof', Labels::FUEL], 'transmission' => ['Transmissie', Labels::TRANSMISSION], 'body_type' => ['Carrosserie', Labels::BODY]] as $key => [$label, $map]): ?>
                <div class="field">
                    <label for="<?= e($key) ?>"><?= e($label) ?></label>
                    <select class="select" id="<?= e($key) ?>" name="<?= e($key) ?>" <?= $inv($key) ?> data-rdw="<?= e($key) ?>">
                        <option value="">Kies…</option>
                        <?php foreach ($map as $k => $item): ?>
                            <option value="<?= e($k) ?>" <?= $val($key) === $k ? 'selected' : '' ?>><?= e($item['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= $err($key) ?>
                </div>
            <?php endforeach; ?>
            <?php
            $numbers = [
                'color' => ['Kleur', 'text', ''],
                'power_kw' => ['Vermogen (kW)', 'numeric', ''],
                'engine_cc' => ['Cilinderinhoud (cc)', 'numeric', ''],
                'battery_kwh' => ['Accu (kWh)', 'decimal', 'Alleen bij elektrisch of plug-in'],
                'range_km' => ['Actieradius WLTP (km)', 'numeric', 'Alleen bij elektrisch'],
                'doors' => ['Deuren', 'numeric', ''],
                'seats' => ['Zitplaatsen', 'numeric', ''],
                'owners' => ['Vorige eigenaren', 'numeric', ''],
                'warranty_months' => ['Garantie (maanden)', 'numeric', ''],
            ];
            foreach ($numbers as $key => [$label, $mode, $hint]): ?>
                <div class="field">
                    <label for="<?= e($key) ?>"><?= e($label) ?></label>
                    <input class="input <?= $mode !== 'text' ? 'num' : '' ?>" id="<?= e($key) ?>" name="<?= e($key) ?>" <?= $mode !== 'text' ? 'inputmode="' . e($mode) . '"' : 'maxlength="40"' ?> value="<?= e($val($key, $key === 'warranty_months' ? '12' : '')) ?>" <?= $inv($key) ?> data-rdw="<?= e($key) ?>">
                    <?php if ($hint): ?><span class="field__hint"><?= e($hint) ?></span><?php endif; ?>
                    <?= $err($key) ?>
                </div>
            <?php endforeach; ?>
            <div class="field">
                <label for="apk_until">APK geldig tot</label>
                <input class="input" type="date" id="apk_until" name="apk_until" value="<?= e($val('apk_until')) ?>" <?= $inv('apk_until') ?> data-rdw="apk_until">
                <?= $err('apk_until') ?>
            </div>
        </div>
    </section>

    <section class="panel" aria-labelledby="desc-title">
        <div class="panel__head"><h2 id="desc-title">Omschrijving</h2><span class="muted small" data-char-count="description">0 / 5000</span></div>
        <div class="field">
            <label class="visually-hidden" for="description">Omschrijving</label>
            <textarea class="textarea" id="description" name="description" rows="7" maxlength="5000" required <?= $inv('description') ?>><?= e($val('description')) ?></textarea>
            <span class="field__hint">Laat een witregel tussen alinea’s. Vertel wat je hebt gecontroleerd en vervangen.</span>
            <?= $err('description') ?>
        </div>
    </section>

    <section class="panel" aria-labelledby="features-title">
        <div class="panel__head"><h2 id="features-title">Uitrusting</h2><span class="muted small" data-feature-count></span></div>
        <div class="feature-picker">
            <?php foreach (Labels::FEATURE_CATEGORY as $cat => $catLabel): if (empty($features[$cat])) { continue; } ?>
                <fieldset>
                    <legend><?= e($catLabel) ?></legend>
                    <?php foreach ($features[$cat] as $f): ?>
                        <label class="check"><input type="checkbox" name="features[]" value="<?= (int) $f['id'] ?>" <?= in_array((int) $f['id'], $selectedFeatures, true) ? 'checked' : '' ?>><span><?= e($f['name']) ?></span></label>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="save-bar">
        <span class="muted small" data-dirty-note><?= $isEdit ? 'Laatst gewijzigd ' . e(date_nl($car['updated_at'], true)) : 'Na het opslaan kun je foto’s toevoegen.' ?></span>
        <a class="btn btn--ghost" href="<?= e(url('admin/autos')) ?>">Annuleren</a>
        <button class="btn btn--accent" type="submit"><?= icon('check') ?><?= $isEdit ? 'Wijzigingen opslaan' : 'Auto opslaan' ?></button>
    </div>
</form>

<?php if ($isEdit): ?>
<section class="panel" id="fotos" aria-labelledby="photos-title"
         data-photos data-upload="<?= e(url('admin/autos/' . $car['id'] . '/fotos')) ?>"
         data-reorder="<?= e(url('admin/autos/' . $car['id'] . '/fotos/volgorde')) ?>"
         data-delete-base="<?= e(url('admin/autos/' . $car['id'] . '/fotos')) ?>"
         data-max="<?= ImageUpload::MAX_PER_CAR ?>">
    <div class="panel__head">
        <div>
            <h2 id="photos-title">Foto’s</h2>
            <p class="muted small">Sleep om de volgorde te wijzigen. De eerste foto is de hoofdfoto. JPG, PNG of WebP, max. 8 MB per foto en <?= ImageUpload::MAX_PER_CAR ?> foto’s per auto.</p>
        </div>
        <span class="muted small" data-photo-count><?= count($photos) ?> / <?= ImageUpload::MAX_PER_CAR ?></span>
    </div>
    <label class="dropzone" data-dropzone>
        <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple data-file-input>
        <?= icon('upload') ?>
        <span><strong>Sleep foto’s hierheen</strong> of klik om te kiezen</span>
        <span class="dropzone__progress" data-progress hidden><span></span></span>
    </label>
    <div class="upload-errors" role="alert" data-upload-errors></div>
    <ol class="photo-grid" data-photo-grid aria-label="Foto’s in volgorde">
        <?php foreach ($photos as $i => $photo): ?>
            <li class="photo-item" draggable="true" data-photo-id="<?= (int) $photo['id'] ?>">
                <img src="<?= e(upload_url(substr($photo['filename'], 0, 32) . '-640.webp')) ?>" width="640" height="480" alt="Foto <?= $i + 1 ?>">
                <span class="photo-item__badge">Hoofdfoto</span>
                <span class="photo-item__grip" aria-hidden="true"><?= icon('grip') ?></span>
                <div class="photo-item__actions">
                    <button type="button" class="icon-btn" data-move="-1"><?= icon('chevron-left') ?><span class="visually-hidden">Eerder zetten</span></button>
                    <button type="button" class="btn btn--small btn--ghost" data-make-main>Maak hoofdfoto</button>
                    <button type="button" class="icon-btn" data-move="1"><?= icon('chevron-right') ?><span class="visually-hidden">Later zetten</span></button>
                    <button type="button" class="icon-btn icon-btn--danger" data-delete-photo><?= icon('trash') ?><span class="visually-hidden">Foto verwijderen</span></button>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
    <template data-photo-template>
        <li class="photo-item" draggable="true">
            <img src="" width="640" height="480" alt="">
            <span class="photo-item__badge">Hoofdfoto</span>
            <span class="photo-item__grip" aria-hidden="true"><?= icon('grip') ?></span>
            <div class="photo-item__actions">
                <button type="button" class="icon-btn" data-move="-1"><?= icon('chevron-left') ?><span class="visually-hidden">Eerder zetten</span></button>
                <button type="button" class="btn btn--small btn--ghost" data-make-main>Maak hoofdfoto</button>
                <button type="button" class="icon-btn" data-move="1"><?= icon('chevron-right') ?><span class="visually-hidden">Later zetten</span></button>
                <button type="button" class="icon-btn icon-btn--danger" data-delete-photo><?= icon('trash') ?><span class="visually-hidden">Foto verwijderen</span></button>
            </div>
        </li>
    </template>
    <p class="muted" data-photo-empty <?= $photos ? 'hidden' : '' ?>>Nog geen foto’s. Auto’s zonder foto’s zien er op de website leeg uit.</p>
</section>

<section class="panel panel--danger" aria-labelledby="danger-title">
    <div class="panel__head"><h2 id="danger-title">Auto verwijderen</h2></div>
    <p class="muted">Verwijdert de auto en alle foto’s definitief. Aanvragen blijven bewaard. Verkochte auto’s kun je beter op “Verkocht” zetten, zodat de pagina blijft bestaan.</p>
    <form method="post" action="<?= e(url('admin/autos/' . $car['id'] . '/verwijderen')) ?>" data-confirm="Weet je zeker dat je de <?= e($car['brand'] . ' ' . $car['model']) ?> en alle foto’s wilt verwijderen? Dit kan niet ongedaan worden gemaakt.">
        <?= csrf_field() ?>
        <button class="btn btn--danger" type="submit"><?= icon('trash') ?>Auto verwijderen</button>
    </form>
</section>

<?php endif; ?>
