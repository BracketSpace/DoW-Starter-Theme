<?php

declare(strict_types=1);

use DoWStarterTheme\Common\View\ViewHelper as v;
?>

<div class="widget-area" id="<?php v::attr('id'); ?>">
    <?php dynamic_sidebar(v::get('id')); ?>
</div>
