<?php

use App\Business;

$today = (int) date('N');
?>
<dl class="hours">
    <?php foreach (Business::HOURS as $n => [$day, $open, $close]): ?>
        <div class="<?= $n === $today ? 'is-today' : '' ?>">
            <dt><?= e($day) ?></dt>
            <dd><?= $open ? e($open . ' – ' . $close) : 'Gesloten' ?></dd>
        </div>
    <?php endforeach; ?>
</dl>
