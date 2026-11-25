<section class="page-head wrap">
    <h1>Mijn favorieten</h1>
    <p class="lead">Auto’s die je bewaart, onthoudt deze browser. Je hebt er geen account voor nodig.</p>
</section>
<section class="wrap section" data-favorites-page>
    <div class="car-grid" data-favorites-list aria-live="polite">
        <div class="skeleton skeleton--card"></div><div class="skeleton skeleton--card"></div><div class="skeleton skeleton--card"></div>
    </div>
    <div class="empty" data-favorites-empty hidden>
        <h2>Nog geen favorieten</h2>
        <p class="muted">Tik op het hartje bij een auto om hem hier te bewaren.</p>
        <a class="btn" href="<?= e(url('occasions')) ?>">Bekijk het aanbod</a>
    </div>
</section>
