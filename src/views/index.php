<?php

declare(strict_types=1);

use DoWStarterTheme\Common\View\ViewHelper as v;

while (have_posts()) {
    the_post();

    v::print('partials.content');
}
