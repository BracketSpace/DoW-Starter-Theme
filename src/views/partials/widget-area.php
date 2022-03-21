<?php

declare(strict_types=1);

use DoWStarterTheme\Helpers\View as v;
?>

<div class="widget-area" id="<?php v::attr('id'); ?>">
	<?php dynamic_sidebar(v::get('id')); ?>
</div>
