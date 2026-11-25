<?php

use App\Business;
use App\Cars;
use App\Finance;
use App\Labels;
use App\View;

$name = Cars::title($car);
$price = (int) $car['price'];
$isSold = $car['status'] === 'sold';
$isAvailable = $car['status'] === 'available';
$fromMonthly = Finance::monthly($price, $rate, 72);
$defaultDown = (int) (round($price * 0.15 / 500) * 500);
$defaultTerm = 60;
$defaultMonthly = Finance::monthly($price - $defaultDown, $rate, $defaultTerm);
$maxDown = (int) (floor(($price - Finance::MIN_LOAN) / 500) * 500);
$isElectric = $car['fuel'] === 'electric';
$errors = errors();
$activeForm = old('_form', 'test_drive');
$minDate = (new DateTimeImmutable('tomorrow'))->format('Y-m-d');
$maxDate = (new DateTimeImmutable('+30 days'))->format('Y-m-d');

$specs = array_filter([
    'Merk' => $car['brand'],
    'Model' => $car['model'],
    'Uitvoering' => $car['variant'],
    'Bouwjaar' => (string) $car['year'],
    'Eerste toelating' => $car['first_registration'] ? date_nl($car['first_registration']) : null,
    'Kilometerstand' => number_nl($car['mileage_km']) . ' km',
    'Brandstof' => Labels::fuel($car['fuel']),
    'Transmissie' => Labels::transmission($car['transmission']),
    'Carrosserie' => Labels::body($car['body_type']),
    'Kleur' => $car['color'] ?: null,
    'Vermogen' => $car['power_kw'] ? $car['power_kw'] . ' kW (' . round($car['power_kw'] * 1.36) . ' pk)' : null,
    'Cilinderinhoud' => $car['engine_cc'] ? number_nl($car['engine_cc']) . ' cc' : null,
    'Accucapaciteit' => $car['battery_kwh'] ? str_replace('.', ',', rtrim(rtrim((string) $car['battery_kwh'], '0'), '.')) . ' kWh' : null,
    'Actieradius (WLTP)' => $car['range_km'] ? number_nl($car['range_km']) . ' km' : null,
    'Deuren' => $car['doors'] ? (string) $car['doors'] : null,
    'Zitplaatsen' => $car['seats'] ? (string) $car['seats'] : null,
    'Vorige eigenaren' => $car['owners'] !== null ? (string) $car['owners'] : null,
    'APK tot' => $car['apk_until'] ? date_nl($car['apk_until']) : null,
    'Garantie' => $car['warranty_months'] . ' maanden',
], static fn ($v) => $v !== null && $v !== '');
?>
<div class="detail" data-car-view="<?= (int) $car['id'] ?>">
    <div class="wrap">
        <ol class="breadcrumb">
            <li><a href="<?= e(url('/')) ?>">Home</a></li>
            <li><a href="<?= e(url('occasions')) ?>">Occasions</a></li>
            <li><a href="<?= e(url('occasions', ['merk' => $car['brand_slug']])) ?>"><?= e($car['brand']) ?></a></li>
            <li><span aria-current="page"><?= e($car['model']) ?></span></li>
        </ol>

        <div class="detail__top">
            <section class="gallery" aria-label="Foto’s van de <?= e($name) ?>" data-gallery>
                <div class="gallery__stage">
                    <ul class="gallery__track" data-gallery-track tabindex="0" aria-label="Fotogalerij, gebruik de pijltjestoetsen om te bladeren">
                        <?php foreach ($photos as $i => $photo): $base = substr($photo['filename'], 0, 32); ?>
                            <li class="gallery__slide" aria-label="Foto <?= $i + 1 ?> van <?= count($photos) ?>">
                                <img src="<?= e(upload_url($photo['filename'])) ?>"
                                     srcset="<?= e(upload_url($base . '-640.webp')) ?> 640w, <?= e(upload_url($photo['filename'])) ?> 1600w"
                                     sizes="(min-width: 1240px) 740px, (min-width: 1000px) 60vw, 100vw"
                                     width="<?= (int) $photo['width'] ?>" height="<?= (int) $photo['height'] ?>"
                                     alt="<?= e($name) ?>, foto <?= $i + 1 ?>" <?= $i === 0 ? 'fetchpriority="high" data-vt-photo' : 'loading="lazy"' ?> decoding="async" data-index="<?= $i ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($photos) > 1): ?>
                        <div class="gallery__nav gallery__nav--prev"><button type="button" class="toggle-btn" data-gallery-prev disabled><?= icon('chevron-left') ?><span class="visually-hidden">Vorige foto</span></button></div>
                        <div class="gallery__nav gallery__nav--next"><button type="button" class="toggle-btn" data-gallery-next><?= icon('chevron-right') ?><span class="visually-hidden">Volgende foto</span></button></div>
                    <?php endif; ?>
                    <div class="gallery__meta">
                        <span class="gallery__counter" data-gallery-counter>1 / <?= count($photos) ?></span>
                        <button type="button" class="toggle-btn gallery__zoom" data-gallery-zoom><?= icon('expand') ?><span class="visually-hidden">Foto’s op volledig scherm bekijken</span></button>
                    </div>
                </div>
                <?php if (count($photos) > 1): ?>
                    <div class="gallery__dots" aria-hidden="true"><?php foreach ($photos as $i => $p): ?><span class="<?= $i === 0 ? 'is-active' : '' ?>"></span><?php endforeach; ?></div>
                    <ul class="gallery__thumbs">
                        <?php foreach ($photos as $i => $photo): ?>
                            <li><button type="button" data-gallery-thumb="<?= $i ?>" <?= $i === 0 ? 'aria-current="true"' : '' ?>>
                                <img src="<?= e(upload_url(substr($photo['filename'], 0, 32) . '-640.webp')) ?>" width="640" height="480" alt="" loading="lazy">
                                <span class="visually-hidden">Toon foto <?= $i + 1 ?></span>
                            </button></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <dialog class="lightbox" data-lightbox aria-label="Foto’s op volledig scherm">
                    <div class="lightbox__bar">
                        <span class="num" data-lightbox-counter></span>
                        <button type="button" class="icon-btn" data-lightbox-close><?= icon('close') ?><span class="visually-hidden">Sluiten</span></button>
                    </div>
                    <ul class="lightbox__track" data-lightbox-track>
                        <?php foreach ($photos as $i => $photo): ?>
                            <li><img src="<?= e(upload_url($photo['filename'])) ?>" width="<?= (int) $photo['width'] ?>" height="<?= (int) $photo['height'] ?>" alt="<?= e($name) ?>, foto <?= $i + 1 ?>" loading="lazy"></li>
                        <?php endforeach; ?>
                    </ul>
                </dialog>
            </section>

            <aside class="summary" aria-label="Samenvatting">
                <div>
                    <p class="summary__brand"><?= e($car['brand']) ?> <span class="status status--<?= e($car['status']) ?>"><?= e(Labels::status($car['status'])) ?></span></p>
                    <h1><?= e($car['model']) ?></h1>
                    <p class="summary__variant"><?= e($car['variant']) ?></p>
                </div>
                <div class="summary__price">
                    <strong><?= e(money($price)) ?></strong>
                    <?php if (!$isSold): ?>
                        <span class="summary__monthly">of <a href="#financiering">vanaf <?= e(money($fromMonthly)) ?> p/m</a> (indicatie)</span>
                    <?php endif; ?>
                </div>
                <dl class="keyspecs">
                    <div><dt><?= icon('calendar') ?>Bouwjaar</dt><dd><?= (int) $car['year'] ?></dd></div>
                    <div><dt><?= icon('gauge') ?>Kilometerstand</dt><dd><?= e(number_nl($car['mileage_km'])) ?> km</dd></div>
                    <div><dt><?= icon($isElectric ? 'bolt' : 'fuel') ?>Brandstof</dt><dd><?= e(Labels::fuel($car['fuel'])) ?></dd></div>
                    <div><dt><?= icon('gear') ?>Transmissie</dt><dd><?= e(Labels::transmission($car['transmission'])) ?></dd></div>
                    <?php if ($car['power_kw']): ?><div><dt><?= icon('bolt') ?>Vermogen</dt><dd><?= round($car['power_kw'] * 1.36) ?> pk</dd></div><?php endif; ?>
                    <?php if ($isElectric && $car['range_km']): ?>
                        <div><dt><?= icon('pin') ?>Actieradius</dt><dd><?= e(number_nl($car['range_km'])) ?> km</dd></div>
                    <?php else: ?>
                        <div><dt><?= icon('car') ?>Carrosserie</dt><dd><?= e(Labels::body($car['body_type'])) ?></dd></div>
                    <?php endif; ?>
                </dl>

                <?php if ($isSold): ?>
                    <div class="notice notice--sold"><?= icon('info') ?><div><strong>Deze auto is verkocht.</strong> Hieronder vind je vergelijkbare auto’s die nog wel beschikbaar zijn.</div></div>
                    <a class="btn btn--block" href="<?= e(url('occasions', ['carrosserie' => Labels::BODY[$car['body_type']]['slug']])) ?>">Bekijk vergelijkbare auto’s</a>
                <?php else: ?>
                    <?php if (!$isAvailable): ?>
                        <div class="notice"><?= icon('clock') ?><div><strong>Deze auto is gereserveerd.</strong> Stel een vraag, dan laten we het je weten als hij weer beschikbaar komt.</div></div>
                    <?php endif; ?>
                    <div class="summary__actions">
                        <?php if ($isAvailable): ?>
                            <a class="btn btn--accent btn--block" href="#contact-auto" data-open-form="test_drive"><?= icon('calendar') ?> Plan een proefrit</a>
                        <?php endif; ?>
                        <div class="summary__secondary">
                            <a class="btn btn--ghost" href="#contact-auto" data-open-form="question"><?= icon('mail') ?> Stel een vraag</a>
                            <a class="btn btn--ghost" href="tel:<?= e(Business::PHONE_E164) ?>"><?= icon('phone') ?> Bel</a>
                        </div>
                        <div class="summary__secondary">
                            <button type="button" class="btn btn--ghost btn--small" data-fav="<?= (int) $car['id'] ?>" aria-pressed="false">
                                <?= icon('heart-line', 'icon icon--off') ?><?= icon('heart', 'icon icon--on') ?><span data-label-off>Bewaren</span><span data-label-on hidden>Bewaard</span>
                            </button>
                            <button type="button" class="btn btn--ghost btn--small" data-compare="<?= (int) $car['id'] ?>" aria-pressed="false">
                                <?= icon('compare', 'icon icon--off') ?><?= icon('check', 'icon icon--on') ?><span data-label-off>Vergelijken</span><span data-label-on hidden>In vergelijking</span>
                            </button>
                            <button type="button" class="btn btn--ghost btn--small" data-share data-share-title="<?= e(Cars::fullTitle($car)) ?>"><?= icon('share') ?>Delen</button>
                        </div>
                    </div>
                    <ul class="summary__trust">
                        <li><?= icon('shield') ?><?= (int) $car['warranty_months'] ?> maanden garantie</li>
                        <li><?= icon('wrench') ?>Gekeurd in eigen werkplaats, nieuwe APK</li>
                        <li><?= icon('check') ?>Kilometerstand logisch (NAP)</li>
                    </ul>
                <?php endif; ?>
            </aside>
        </div>

        <div class="detail__body">
            <div class="block-stack">
                <section class="block description" aria-labelledby="about-title">
                    <h2 id="about-title">Over deze auto</h2>
                    <?php foreach (preg_split('/\R{2,}/', trim($car['description'])) as $paragraph): ?>
                        <p><?= nl2br(e($paragraph)) ?></p>
                    <?php endforeach; ?>
                </section>

                <section class="block" aria-labelledby="specs-title">
                    <h2 id="specs-title">Specificaties</h2>
                    <dl class="spec-table">
                        <?php foreach ($specs as $label => $value): ?>
                            <div><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                </section>

                <?php if ($features): ?>
                    <section class="block" aria-labelledby="features-title">
                        <h2 id="features-title">Uitrusting</h2>
                        <div class="feature-groups">
                            <?php foreach (Labels::FEATURE_CATEGORY as $cat => $catLabel): if (empty($features[$cat])) { continue; } ?>
                                <div>
                                    <h3><?= e($catLabel) ?></h3>
                                    <ul>
                                        <?php foreach ($features[$cat] as $feature): ?><li><?= icon('check') ?><?= e($feature) ?></li><?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <?php if (!$isSold && $maxDown >= 0): ?>
            <section class="block" id="financiering" aria-labelledby="calc-title">
                <h2 id="calc-title">Wat kost hij per maand?</h2>
                <form class="calc" data-calc data-price="<?= $price ?>" data-rate="<?= e((string) $rate) ?>" data-min-loan="<?= Finance::MIN_LOAN ?>">
                    <div class="calc__inputs">
                        <div class="field">
                            <div class="range-row"><label for="calc-down">Aanbetaling</label><output for="calc-down" data-calc-down-out><?= e(money($defaultDown)) ?></output></div>
                            <input class="range" type="range" id="calc-down" min="0" max="<?= max(0, $maxDown) ?>" step="500" value="<?= $defaultDown ?>" data-calc-down>
                        </div>
                        <fieldset class="segmented">
                            <legend class="field__label">Looptijd in maanden</legend>
                            <?php foreach (Finance::TERMS as $term): ?>
                                <label><input type="radio" name="term" value="<?= $term ?>" <?= $term === $defaultTerm ? 'checked' : '' ?> data-calc-term><span><?= $term ?></span></label>
                            <?php endforeach; ?>
                        </fieldset>
                    </div>
                    <div class="calc__result" aria-live="polite">
                        <div>
                            <div class="label">Maandbedrag (indicatie)</div>
                            <div class="calc__monthly"><span data-calc-monthly><?= e(money($defaultMonthly)) ?></span> <small>p/m</small></div>
                        </div>
                        <dl class="calc__lines">
                            <div><dt>Leenbedrag</dt><dd data-calc-loan><?= e(money($price - $defaultDown)) ?></dd></div>
                            <div><dt>Jaarlijks kostenpercentage</dt><dd><?= e(number_format($rate, 1, ',', '.')) ?>%</dd></div>
                            <div><dt>Totaal te betalen</dt><dd data-calc-total><?= e(money($defaultMonthly * $defaultTerm)) ?></dd></div>
                        </dl>
                    </div>
                    <div class="afm">
                        <svg viewBox="0 0 46 26" aria-hidden="true"><rect x="1" y="1" width="44" height="24" rx="3" fill="none" stroke="currentColor" stroke-width="2"/><text x="23" y="17.5" font-size="11" font-weight="700" text-anchor="middle" fill="currentColor">€ !</text></svg>
                        <span>Let op! Geld lenen kost geld.</span>
                    </div>
                    <p class="calc__disclaimer">Dit is een indicatie op basis van een vast jaarlijks kostenpercentage van <?= e(number_format($rate, 1, ',', '.')) ?>% en een annuïteitenlening. Het werkelijke bedrag hangt af van je persoonlijke situatie en de beoordeling door de kredietverstrekker. Rijdwerk bemiddelt alleen; aan deze berekening kun je geen rechten ontlenen.</p>
                </form>
            </section>
            <?php endif; ?>

            <?php if (!$isSold): ?>
            <section class="block full" id="contact-auto" aria-labelledby="contact-title">
                <div class="split split--top">
                    <div>
                        <h2 id="contact-title"><?= $isAvailable ? 'Proefrit plannen of iets vragen?' : 'Een vraag over deze auto?' ?></h2>
                        <p class="lead">Je krijgt binnen één werkdag antwoord van Joost of Esra uit ons verkoopteam. De bevestiging staat direct in je mail.</p>
                        <?= View::partial('hours') ?>
                    </div>
                    <div class="contact-forms" data-tabs>
                        <?php if ($isAvailable): ?>
                        <div class="tabs" role="tablist" aria-label="Kies een formulier">
                            <button type="button" role="tab" id="tab-test" aria-controls="panel-test" aria-selected="<?= $activeForm === 'test_drive' ? 'true' : 'false' ?>" data-tab="test_drive"><?= icon('calendar') ?>Proefrit</button>
                            <button type="button" role="tab" id="tab-question" aria-controls="panel-question" aria-selected="<?= $activeForm === 'question' ? 'true' : 'false' ?>" data-tab="question"><?= icon('mail') ?>Vraag stellen</button>
                        </div>
                        <div role="tabpanel" id="panel-test" aria-labelledby="tab-test" <?= $activeForm === 'test_drive' ? '' : 'hidden' ?>>
                            <?= View::partial('form-test-drive', ['car' => $car, 'errors' => $activeForm === 'test_drive' ? $errors : [], 'minDate' => $minDate, 'maxDate' => $maxDate]) ?>
                        </div>
                        <div role="tabpanel" id="panel-question" aria-labelledby="tab-question" <?= $activeForm === 'question' ? '' : 'hidden' ?>>
                            <?= View::partial('form-question', ['car' => $car, 'errors' => $activeForm === 'question' ? $errors : []]) ?>
                        </div>
                        <?php else: ?>
                            <?= View::partial('form-question', ['car' => $car, 'errors' => $errors]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($similar): ?>
            <section class="block full" aria-labelledby="similar-title">
                <h2 id="similar-title">Misschien ook iets voor jou</h2>
                <div class="car-grid">
                    <?php foreach ($similar as $s): ?><?= View::partial('car-card', ['car' => $s]) ?><?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!$isSold): ?>
<div class="mobile-bar" data-mobile-bar>
    <div class="mobile-bar__price"><strong><?= e(money($price)) ?></strong><span><?= e($name) ?></span></div>
    <a class="icon-btn" href="tel:<?= e(Business::PHONE_E164) ?>"><?= icon('phone') ?><span class="visually-hidden">Bel ons</span></a>
    <?php if ($isAvailable): ?>
        <a class="btn btn--accent" href="#contact-auto" data-open-form="test_drive">Proefrit</a>
    <?php else: ?>
        <a class="btn" href="#contact-auto" data-open-form="question">Vraag stellen</a>
    <?php endif; ?>
</div>
<?php endif; ?>
