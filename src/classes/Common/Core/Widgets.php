<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Core;

use DoWStarterTheme\Common\Contracts\Hookable;

/**
 * Widgets integration class
 */
class Widgets implements Hookable
{
    /**
     * Filters sidebar default options.
     *
     * @filter register_sidebar_defaults
     *
     * @param  array<string, mixed> $defaults Default sidebar optons.
     * @return array<string, mixed>
     */
    public function sidebarDefaults(array $defaults): array
    {
        return array_merge(
            $defaults,
            [
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget' => "</div>\n",
                'before_title' => '<h3 class="widget-title">',
                'after_title' => "</h3>\n",
            ]
        );
    }
}
