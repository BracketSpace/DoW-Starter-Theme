<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Core\Config;
use DoWStarterTheme\Core\Theme;
use DoWStarterTheme\View\Factory;

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
	 * @param string $id Menu ID (location).
	 * @return void
	 */
	public static function display(string $id): void
	{
		$menu = wp_nav_menu(
			[
				'theme_location' => $id,
				'container' => '',
				'echo' => false,
			]
		);

		$factory = Theme::getService(Factory::class);
		$factory->get(
			'partials.nav-menu',
			[
				'class' => "nav-{$id}",
				'menu' => $menu,
			]
		)->render();
	}
}
