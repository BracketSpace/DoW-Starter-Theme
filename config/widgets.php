<?php

declare(strict_types=1);

use DoWStarterTheme\Features\Widgets;

return [
    'classes' => [
        Widgets\SocialLinksWidget::class,
    ],

    'areas' => [
        'example-area' => [
            'name' => __('Example Area', 'dow-starter-theme'),
            'description' => __('Description of Example Area.', 'dow-starter-theme'),
        ],
    ],
];
