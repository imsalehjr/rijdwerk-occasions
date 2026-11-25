<?php

if ($pages <= 1) {
    return;
}
$window = [];
for ($i = 1; $i <= $pages; $i++) {
    if ($i === 1 || $i === $pages || abs($i - $page) <= 1) {
        $window[] = $i;
    }
}
$link = static fn (int $p) => url('occasions', $filter->toQuery(['pagina' => $p > 1 ? $p : null]));
?>
<nav class="pagination" aria-label="Paginering">
    <?php if ($page > 1): ?>
        <a href="<?= e($link($page - 1)) ?>" rel="prev" data-page-link><?= icon('chevron-left') ?><span class="visually-hidden">Vorige pagina</span></a>
    <?php endif; ?>
    <?php $prev = 0; foreach ($window as $p): ?>
        <?php if ($p - $prev > 1): ?><span class="gap" aria-hidden="true">…</span><?php endif; ?>
        <?php if ($p === $page): ?>
            <span aria-current="page"><span class="visually-hidden">Pagina </span><?= $p ?></span>
        <?php else: ?>
            <a href="<?= e($link($p)) ?>" data-page-link><span class="visually-hidden">Pagina </span><?= $p ?></a>
        <?php endif; ?>
    <?php $prev = $p; endforeach; ?>
    <?php if ($page < $pages): ?>
        <a href="<?= e($link($page + 1)) ?>" rel="next" data-page-link><span class="visually-hidden">Volgende pagina</span><?= icon('chevron-right') ?></a>
    <?php endif; ?>
</nav>
