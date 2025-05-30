<?php

declare(strict_types=1);

use DoWStarterTheme\Feature\SocialLinks\SocialLinksWidget;

return [
    'classes' => [
        SocialLinksWidget::class,
    ],

    'areas' => [
        'example-area' => [
            'name' => __('Example Area', 'dow-starter-theme'),
            'description' => __('Description of Example Area.', 'dow-starter-theme'),
        ],
    ],
];
