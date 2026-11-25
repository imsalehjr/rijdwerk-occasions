<?php

use App\Cars;
use App\Labels;

$photoBase = $car['photo'] ? substr($car['photo'], 0, 32) : null;
$headingTag = $headingTag ?? 'h3';
$href = url('occasions/' . $car['slug']);
?>
<article class="car-card <?= $car['status'] === 'sold' ? 'car-card__sold' : '' ?>" data-car-id="<?= (int) $car['id'] ?>">
    <div class="car-card__media">
        <?php if ($photoBase): ?>
            <img src="<?= e(upload_url($photoBase . '-640.webp')) ?>"
                 srcset="<?= e(upload_url($photoBase . '-640.webp')) ?> 640w, <?= e(upload_url($photoBase . '.webp')) ?> 1600w"
                 sizes="(min-width: 1240px) 300px, (min-width: 640px) 45vw, 92vw"
                 width="640" height="480" alt="" loading="<?= !empty($eager) ? 'eager' : 'lazy' ?>" decoding="async" data-vt-photo>
        <?php endif; ?>
        <?php if ($car['status'] !== 'available'): ?>
            <span class="status status--<?= e($car['status']) ?>"><?= e(Labels::status($car['status'])) ?></span>
        <?php endif; ?>
    </div>
    <div class="car-card__actions">
        <button type="button" class="toggle-btn" data-fav="<?= (int) $car['id'] ?>" aria-pressed="false" aria-label="Bewaar <?= e(Cars::title($car)) ?> als favoriet">
            <?= icon('heart-line', 'icon icon--off') ?><?= icon('heart', 'icon icon--on') ?>
        </button>
        <?php if ($car['status'] !== 'sold'): ?>
        <button type="button" class="toggle-btn" data-compare="<?= (int) $car['id'] ?>" aria-pressed="false" aria-label="Vergelijk <?= e(Cars::title($car)) ?>">
            <?= icon('compare', 'icon icon--off') ?><?= icon('check', 'icon icon--on') ?>
        </button>
        <?php endif; ?>
    </div>
    <div class="car-card__body">
        <<?= $headingTag ?> class="car-card__title"><a href="<?= e($href) ?>"><?= e(Cars::title($car)) ?></a></<?= $headingTag ?>>
        <p class="car-card__variant"><?= e($car['variant']) ?></p>
        <div class="car-card__row">
            <p class="car-card__price"><?= e(money($car['price'])) ?></p>
        </div>
        <ul class="car-card__specs">
            <li><?= icon('calendar') ?><span class="visually-hidden">Bouwjaar </span><?= (int) $car['year'] ?></li>
            <li><?= icon('gauge') ?><?= e(number_nl($car['mileage_km'])) ?> km</li>
            <li><?= icon($car['fuel'] === 'electric' ? 'bolt' : 'fuel') ?><?= e(Labels::fuel($car['fuel'])) ?></li>
            <li><?= icon('gear') ?><?= e(Labels::transmission($car['transmission'])) ?></li>
        </ul>
    </div>
</article>
