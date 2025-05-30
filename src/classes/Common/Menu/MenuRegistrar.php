<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Menu;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Common\Config\Config;

/**
 * Menu class
 */
class MenuRegistrar implements Hookable
{
    /**
     * Class constructor.
     *
     * @param Config $config Config instance.
     */
    public function __construct(
        private Config $config,
    ) {
    }

    /**
     * Registers nav menus
     *
     * @action init
     *
     * @return void
     */
    public function registerNavMenus(): void
    {
        $menus = $this->config->get('menus');

        if (! is_array($menus)) {
            return;
        }

        register_nav_menus($menus);
    }
}
