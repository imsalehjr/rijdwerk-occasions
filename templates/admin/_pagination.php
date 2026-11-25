<?php if ($pages > 1): ?>
<nav class="pagination" aria-label="Paginering">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
        <?php if ($p === $page): ?>
            <span aria-current="page"><?= $p ?></span>
        <?php else: ?>
            <a href="<?= e(url($path, $query + ['pagina' => $p > 1 ? $p : null])) ?>"><span class="visually-hidden">Pagina </span><?= $p ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</nav>
<?php endif; ?>
