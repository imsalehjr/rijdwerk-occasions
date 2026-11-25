<?php

use App\Business;
use App\Cars;
use App\Labels;
use App\View;

$budgets = [10000, 15000, 20000, 25000, 30000, 40000, 50000];
$digits = str_split(str_pad((string) $stock, 2, '0', STR_PAD_LEFT));
?>
<section class="hero">
    <div class="wrap hero__grid">
        <div>
            <h1>Occasions die we zelf zouden kopen.</h1>
            <p class="lead">Elke auto gaat eerst door onze eigen werkplaats in <?= e(Business::CITY) ?>. Je krijgt minimaal 12 maanden garantie, een nieuwe APK en een prijs zonder verrassingen.</p>

            <form class="finder" action="<?= e(url('occasions')) ?>" method="get" data-finder>
                <div class="finder__fields">
                    <div class="field">
                        <label for="finder-brand">Merk</label>
                        <select class="select" id="finder-brand" name="merk">
                            <option value="">Alle merken</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= e($brand['slug']) ?>"><?= e($brand['name']) ?> (<?= (int) $brand['total'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="finder-budget">Budget tot</label>
                        <select class="select" id="finder-budget" name="prijs_max">
                            <option value="">Geen max.</option>
                            <?php foreach ($budgets as $b): ?>
                                <option value="<?= $b ?>"><?= e(money($b)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="finder-fuel">Brandstof</label>
                        <select class="select" id="finder-fuel" name="brandstof">
                            <option value="">Alle soorten</option>
                            <?php foreach (Labels::FUEL as $fuel): ?>
                                <option value="<?= e($fuel['slug']) ?>"><?= e($fuel['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button class="btn btn--accent finder__submit" type="submit">
                    <span aria-hidden="true">Toon</span>
                    <span class="odo" data-odo aria-hidden="true">
                        <?php foreach ($digits as $d): ?>
                            <span class="odo__digit"><span class="odo__strip" data-digit="<?= (int) $d ?>"><?php for ($i = 0; $i <= 9; $i++): ?><span><?= $i ?></span><?php endfor; ?></span></span>
                        <?php endforeach; ?>
                    </span>
                    <span aria-hidden="true">auto’s</span>
                    <span class="visually-hidden" data-odo-label>Toon <?= (int) $stock ?> auto’s</span>
                </button>
            </form>
        </div>

        <?php if ($hero && $hero['photo']): ?>
            <figure class="hero__visual">
                <img src="<?= e(upload_url($hero['photo'])) ?>" width="1600" height="1200" alt="<?= e(Cars::title($hero)) ?> in onze showroom" fetchpriority="high">
                <a class="hero__tag" href="<?= e(url('occasions/' . $hero['slug'])) ?>">
                    <span>Net binnen</span>
                    <strong><?= e(Cars::title($hero)) ?></strong>
                    <span class="num"><?= e(money($hero['price'])) ?> · <?= (int) $hero['year'] ?> · <?= e(number_nl($hero['mileage_km'])) ?> km</span>
                </a>
            </figure>
        <?php endif; ?>
    </div>
</section>

<section class="section" aria-labelledby="featured-title">
    <div class="wrap">
        <div class="section-head">
            <div>
                <h2 id="featured-title">Nieuw in de showroom</h2>
                <p>De auto’s die het laatst onze werkplaats uit kwamen. Alles gekeurd, gepoetst en klaar voor een proefrit.</p>
            </div>
            <a class="btn btn--ghost" href="<?= e(url('occasions')) ?>">Alle <?= (int) $stock ?> occasions</a>
        </div>
        <div class="car-grid">
            <?php foreach ($featured as $car): ?>
                <?= View::partial('car-card', ['car' => $car]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" aria-labelledby="recent-title" data-recent hidden>
    <div class="wrap">
        <div class="section-head">
            <h2 id="recent-title">Verder kijken waar je was</h2>
        </div>
        <div class="reveal-strip" data-recent-list></div>
    </div>
</section>

<section class="section band" aria-labelledby="how-title">
    <div class="wrap">
        <div class="section-head">
            <div>
                <h2 id="how-title">Zo koop je een auto bij Rijdwerk</h2>
                <p class="muted">Geen verkooppraatjes. Wel een eerlijk verhaal over elke auto.</p>
            </div>
        </div>
        <ol class="steps">
            <li>
                <h3>Kies online</h3>
                <p class="muted">Filter op wat voor jou telt en zet auto’s naast elkaar. Bij elke auto zie je wat we hebben gecontroleerd en vervangen.</p>
            </li>
            <li>
                <h3>Plan een proefrit</h3>
                <p class="muted">Kies een dag en tijd. Wij zetten de auto klaar, met een volle tank of een volle accu.</p>
            </li>
            <li>
                <h3>Rijd weg met garantie</h3>
                <p class="muted">Inclusief nieuwe APK, onderhoudsbeurt en minimaal 12 maanden garantie. Inruilen of financieren regelen we in één keer.</p>
            </li>
        </ol>
    </div>
</section>

<section class="section" aria-labelledby="visit-title">
    <div class="wrap split">
        <div>
            <h2 id="visit-title">Kom langs aan de Kanaaldijk</h2>
            <p class="lead">Zonder afspraak welkom tijdens openingstijden. Voor een proefrit plan je vooraf een moment, dan staat de auto voor je klaar.</p>
            <dl class="facts">
                <div><dt>Adres</dt><dd><?= e(Business::STREET) ?></dd></div>
                <div><dt>Telefoon</dt><dd><a href="tel:<?= e(Business::PHONE_E164) ?>"><?= e(Business::PHONE) ?></a></dd></div>
            </dl>
            <p class="u-mt"><a class="btn" href="<?= e(url('contact')) ?>">Route en openingstijden</a></p>
        </div>
        <div>
            <?= View::partial('hours') ?>
        </div>
    </div>
</section>
