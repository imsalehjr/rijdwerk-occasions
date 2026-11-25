<?php

use App\Business;

$pageTitle = isset($title) && $title !== '' ? $title . ' | ' . Business::NAME : Business::NAME . ' – Gecontroleerde occasions in ' . Business::CITY;
$metaDescription = $description ?? 'Gecontroleerde occasions met garantie bij Rijdwerk Occasions in Bremdonk, Noord-Brabant. Bekijk het aanbod, plan een proefrit en bereken je maandbedrag.';
$canonicalUrl = $canonical ?? null;
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <?php if (!empty($metaRobots)): ?><meta name="robots" content="<?= e($metaRobots) ?>"><?php endif; ?>
    <?php if ($canonicalUrl): ?><link rel="canonical" href="<?= e($canonicalUrl) ?>"><?php endif; ?>
    <meta property="og:site_name" content="<?= e(Business::NAME) ?>">
    <meta property="og:title" content="<?= e($title ?? Business::NAME) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:type" content="<?= e($ogType ?? 'website') ?>">
    <meta property="og:locale" content="nl_NL">
    <?php if (!empty($ogImage)): ?><meta property="og:image" content="<?= e($ogImage) ?>"><meta name="twitter:card" content="summary_large_image"><?php endif; ?>
    <meta name="theme-color" content="#edefed">
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preload" href="<?= e(url('assets/fonts/instrument-sans-latin.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
    <?php foreach (($jsonLd ?? []) as $schema): ?>
    <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <?php endforeach; ?>
</head>
<body class="<?= e($bodyClass ?? '') ?>" data-base="<?= e(rtrim(url('/'), '/')) ?>">
<a class="skip-link" href="#main">Direct naar de inhoud</a>
<header class="site-header" data-header>
    <div class="wrap site-header__inner">
        <a class="logo" href="<?= e(url('/')) ?>">
            <?= \App\View::partial('logo-mark') ?>
            <span class="logo__word"><span class="logo__name">Rijdwerk</span><span class="logo__sub">occasions · <?= e(Business::CITY) ?></span></span>
        </a>
        <nav class="nav" id="site-nav" aria-label="Hoofdmenu" data-nav>
            <a href="<?= e(url('occasions')) ?>" <?= is_active('/occasions') ? 'aria-current="page"' : '' ?>>Occasions</a>
            <a href="<?= e(url('vergelijken')) ?>" <?= is_active('/vergelijken') ? 'aria-current="page"' : '' ?>>Vergelijken</a>
            <a href="<?= e(url('over-ons')) ?>" <?= is_active('/over-ons') ? 'aria-current="page"' : '' ?>>Over ons</a>
            <a href="<?= e(url('contact')) ?>" <?= is_active('/contact') ? 'aria-current="page"' : '' ?>>Contact</a>
        </nav>
        <div class="header-tools">
            <a class="icon-btn" href="<?= e(url('favorieten')) ?>" aria-label="Favorieten">
                <?= icon('heart-line') ?>
                <span class="count-badge" data-count="favorites" hidden>0</span>
            </a>
            <a class="btn btn--ghost btn--small header-phone" href="tel:<?= e(Business::PHONE_E164) ?>"><?= icon('phone') ?><span><?= e(Business::PHONE) ?></span></a>
            <button class="icon-btn menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-menu-toggle>
                <?= icon('menu') ?><span class="visually-hidden">Menu</span>
            </button>
        </div>
    </div>
</header>

<main id="main" tabindex="-1">
<?= $content ?>
</main>

<footer class="site-footer">
    <div class="wrap">
        <div class="footer-grid">
            <div>
                <a class="logo" href="<?= e(url('/')) ?>">
                    <?= \App\View::partial('logo-mark') ?>
                    <span class="logo__word"><span class="logo__name">Rijdwerk</span><span class="logo__sub">occasions · <?= e(Business::CITY) ?></span></span>
                </a>
                <p>Gecontroleerde occasions uit eigen werkplaats, met garantie en een eerlijke prijs.</p>
            </div>
            <div>
                <h2>Bezoek ons</h2>
                <ul>
                    <li><?= e(Business::STREET) ?><br><?= e(Business::POSTCODE . ' ' . Business::CITY) ?></li>
                    <li><a href="tel:<?= e(Business::PHONE_E164) ?>"><?= e(Business::PHONE) ?></a></li>
                    <li><a href="mailto:<?= e(Business::EMAIL) ?>"><?= e(Business::EMAIL) ?></a></li>
                </ul>
            </div>
            <div>
                <h2>Openingstijden</h2>
                <?= \App\View::partial('hours') ?>
            </div>
            <div>
                <h2>Snel naar</h2>
                <ul>
                    <li><a href="<?= e(url('occasions')) ?>">Alle occasions</a></li>
                    <li><a href="<?= e(url('occasions', ['brandstof' => 'elektrisch'])) ?>">Elektrische auto’s</a></li>
                    <li><a href="<?= e(url('occasions', ['prijs_max' => 15000])) ?>">Auto’s tot € 15.000</a></li>
                    <li><a href="<?= e(url('favorieten')) ?>">Mijn favorieten</a></li>
                    <li><a href="<?= e(url('privacy')) ?>">Privacyverklaring</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© <?= date('Y') ?> <?= e(Business::NAME) ?> · KvK <?= e(Business::KVK) ?></span>
            <span>Demowebsite: dit bedrijf, de auto’s en de foto’s zijn fictief.</span>
        </div>
    </div>
</footer>

<div class="compare-tray" data-compare-tray aria-live="polite">
    <span data-compare-label>0 auto’s geselecteerd</span>
    <button type="button" class="compare-tray__clear" data-compare-clear>Wissen</button>
    <a class="btn btn--accent btn--small" href="<?= e(url('vergelijken')) ?>" data-compare-link>Vergelijk</a>
</div>
<div class="toast" role="status" aria-live="polite" data-toast></div>
</body>
</html>
