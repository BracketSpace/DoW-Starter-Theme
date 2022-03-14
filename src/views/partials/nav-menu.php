<?php

declare(strict_types=1);

use DoWStarterTheme\Helpers\View as v;
?>

<nav class="<?php v::attr('class'); ?>" role="navigation">
	<?php v::raw('menu'); ?>
</nav>
