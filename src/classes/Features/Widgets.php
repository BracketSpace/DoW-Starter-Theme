<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Core\Config;
use DoWStarterTheme\View\View;

/**
 * Widgets class
 */
class Widgets
{
	use HookTrait;

	/**
	 * Registers sidebars.
	 *
	 * @action widgets_init
	 *
	 * @return void
	 */
	public function registerSidebars(): void
	{
		$sidebars = Config::get('widget-areas');

		if (!is_array($sidebars)) {
			return;
		}

		foreach ($sidebars as $id => $sidebar) {
			if (!is_array($sidebar)) {
				continue;
			}

			$sidebar['id'] = $id;

			register_sidebar($sidebar);
		}
	}

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

	/**
	 * Displays a widget area.
	 *
	 * @param string $id Widget area id.
	 * @return void
	 */
	public static function display(string $id): void
	{
		if (!is_active_sidebar($id)) {
			return;
		}

		View::print('partials.widget-area', ['id' => $id]);
	}
}
