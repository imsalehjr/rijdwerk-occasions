<?php

use App\Auth;
use App\Database;

$newRequests = (int) Database::value("SELECT COUNT(*) FROM requests WHERE status = 'new'");
$success = flash('success');
$nav = [
    ['admin', 'Dashboard', 'dashboard', null],
    ['admin/autos', 'Auto’s', 'car', null],
    ['admin/aanvragen', 'Aanvragen', 'inbox', $newRequests],
];
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?= e(\App\Csrf::token()) ?>">
    <title><?= e($title ?? 'Beheer') ?> | Rijdwerk beheer</title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
    <script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</head>
<body class="admin" data-base="<?= e(rtrim(url('/'), '/')) ?>">
<a class="skip-link" href="#main">Direct naar de inhoud</a>
<?php if (is_demo()): ?>
<aside class="demo-banner" aria-label="Demo-melding">
    <div class="wrap"><strong>Demo:</strong> dit is het beheerpaneel van een demo. Je mag alles aanpassen; alles wordt automatisch teruggezet. Foto’s uploaden staat uit.</div>
</aside>
<?php endif; ?>
<div class="admin-shell">
    <aside class="admin-side">
        <a class="logo" href="<?= e(url('admin')) ?>">
            <?= \App\View::partial('logo-mark') ?>
            <span class="logo__word"><span class="logo__name">Rijdwerk</span><span class="logo__sub">beheer</span></span>
        </a>
        <nav class="admin-nav" aria-label="Beheer">
            <?php foreach ($nav as [$path, $label, $iconName, $badge]):
                $current = \App\App::currentPath();
                $active = $path === 'admin' ? $current === '/admin' : str_starts_with($current, '/' . $path); ?>
                <a href="<?= e(url($path)) ?>" <?= $active ? 'aria-current="page"' : '' ?>>
                    <?= icon($iconName) ?><span><?= e($label) ?></span>
                    <?php if ($badge): ?><span class="pill" aria-label="<?= (int) $badge ?> nieuw"><?= (int) $badge ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-side__foot">
            <a href="<?= e(url('/')) ?>" target="_blank" rel="noopener"><?= icon('external') ?>Website bekijken</a>
            <form method="post" action="<?= e(url('admin/logout')) ?>">
                <?= csrf_field() ?>
                <button type="submit"><?= icon('logout') ?><span>Uitloggen<span class="admin-user"><?= e(Auth::name()) ?></span></span></button>
            </form>
        </div>
    </aside>
    <main id="main" class="admin-main" tabindex="-1">
        <?php if ($success): ?><p class="alert alert--success" role="status" data-flash><?= icon('check') ?><?= e($success) ?></p><?php endif; ?>
        <?= $content ?>
    </main>
</div>
<dialog class="confirm-dialog" data-confirm-dialog>
    <form method="dialog">
        <h2 data-confirm-title>Weet je het zeker?</h2>
        <p data-confirm-text></p>
        <div class="confirm-dialog__actions">
            <button class="btn btn--ghost" value="cancel">Annuleren</button>
            <button class="btn btn--danger" value="ok">Verwijderen</button>
        </div>
    </form>
</dialog>
<div class="toast" role="status" aria-live="polite" data-toast></div>
</body>
</html>
