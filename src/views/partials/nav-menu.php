<?php

declare(strict_types=1);

use DoWStarterTheme\Common\View\ViewHelper as v;
?>

<nav class="<?php v::attr('class'); ?>" role="navigation">
    <?php v::raw('menu'); ?>
</nav>
