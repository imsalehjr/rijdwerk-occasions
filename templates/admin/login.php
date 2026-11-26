<?php $loginError = error_for('login'); $notice = flash('login'); ?>
<div class="auth-card">
    <a class="logo" href="<?= e(url('/')) ?>">
        <?= \App\View::partial('logo-mark') ?>
        <span class="logo__word"><span class="logo__name">Rijdwerk</span><span class="logo__sub">beheer</span></span>
    </a>
    <h1>Inloggen</h1>
    <?php if ($notice): ?><p class="alert alert--info" role="status"><?= e($notice) ?></p><?php endif; ?>
    <?php if (is_demo()): ?>
        <div class="alert alert--info" role="note">
            <strong>Demo-inlog</strong><br>
            E-mailadres: <code><?= e(env('DEMO_ADMIN_EMAIL', '')) ?></code><br>
            Wachtwoord: <code><?= e(env('DEMO_ADMIN_PASSWORD', '')) ?></code>
        </div>
    <?php endif; ?>
    <?php if ($loginError): ?><p class="alert alert--error" role="alert"><?= e($loginError) ?></p><?php endif; ?>
    <form method="post" action="<?= e(url('admin/login')) ?>" class="form-grid">
        <?= csrf_field() ?>
        <div class="field">
            <label for="email">E-mailadres</label>
            <input class="input" type="email" id="email" name="email" value="<?= e(old('email', is_demo() ? (string) env('DEMO_ADMIN_EMAIL', '') : '')) ?>" autocomplete="username" required autofocus>
        </div>
        <div class="field">
            <label for="password">Wachtwoord</label>
            <input class="input" type="password" id="password" name="password" autocomplete="current-password" required maxlength="200">
        </div>
        <button class="btn btn--block" type="submit">Inloggen</button>
    </form>
    <p class="muted small"><a href="<?= e(url('/')) ?>">Terug naar de website</a></p>
</div>
