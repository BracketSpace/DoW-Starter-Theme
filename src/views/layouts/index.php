<?php

declare(strict_types=1);

use DoWStarterTheme\Helpers\View as v;

v::partial('header');
?>

<div class="site-inner container-full">
	<main class="content">
		<?php v::raw('content'); ?>
	</main>
</div>

<?php v::partial('footer'); ?>
