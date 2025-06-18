<?php

declare(strict_types=1);

use DoWStarterTheme\Common\View\ViewHelper as v;

v::print('partials.header');
?>

<div class="site-inner container-full">
    <main class="content">
        <?php v::raw('content'); ?>
    </main>
</div>

<?php v::print('partials.footer'); ?>
