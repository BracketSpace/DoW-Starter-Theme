<?php

declare(strict_types=1);

use DoWStarterTheme\Helpers\View as v;

while (have_posts()) {
	the_post();

	v::partial('content');
}
