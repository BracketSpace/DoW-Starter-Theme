<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Menu;

use DoWStarterTheme\Common\View\ViewFactory;

/**
 * Menu class
 */
class Menu
{
    /**
     * Class constructor.
     *
     * @param ViewFactory $view View factory instance.
     */
    public function __construct(
        private ViewFactory $view,
    ) {
    }

    /**
     * Displays nav menu.
     *
     * @param string         $id       Menu ID (location).
     * @param false|Callable $fallback Fallback to be called if the menu doesn't exist.
     * @return void
     */
    public function display(string $id, $fallback = false): void
    {
        $menu = wp_nav_menu(
            [
                'container' => '',
                'echo' => false,
                'fallback_cb' => $fallback,
                'theme_location' => $id,
            ]
        );

        $this->view->print(
            'partials.nav-menu',
            [
                'class' => "nav-{$id}",
                'menu' => $menu,
            ]
        );
    }
}
