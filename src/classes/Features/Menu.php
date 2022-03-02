<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use DoWStarterTheme\Dependencies\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Core\Config;
use DoWStarterTheme\View\View;

/**
 * Menu class
 */
class Menu
{
	use HookTrait;

	/**
	 * Registers nav menus
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function registerNavMenus(): void
	{
		$menus = Config::get('menus');

		if (!is_array($menus)) {
			return;
		}

		register_nav_menus($menus);
	}

	/**
	 * Displays nav menu.
	 *
	 * @param string        $id       Menu ID (location).
	 * @param bool|Callable $fallback Fallback to be called if the menu doesn't exist.
	 * @return void
	 */
	public static function display(string $id, $fallback = false): void
	{
		$menu = wp_nav_menu(
			[
				'container' => '',
				'echo' => false,
				'falback_cb' => $fallback,
				'theme_location' => $id,
			]
		);

		View::print(
			'partials.nav-menu',
			[
				'class' => "nav-{$id}",
				'menu' => $menu,
			]
		);
	}
}
