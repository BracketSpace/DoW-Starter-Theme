<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Core;

use DoWStarterTheme\Common\Contracts\Hookable;

/**
 * Internationalization class.
 */
class I18n implements Hookable
{
    /**
     * Loads theme textdomain.
     *
     * @action after_setup_theme
     *
     * @return void
     */
    public function loadTexdomain(): void
    {
        load_theme_textdomain('dow-starter-theme', get_stylesheet_directory() . '/languages');
    }
}
