<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Menu;

use KepnerTregoe\Common\Contracts\Hookable;
use KepnerTregoe\Common\Helpers\SVG;
use KepnerTregoe\Deps\Illuminate\Support\Collection;
use WP_Post;

/**
 * Menu class
 */
class MenuHooks implements Hookable
{
   /**
     * Class constructor.
     *
     * @param SVG $svg SVG helper instance.
     */
    public function __construct(
        private SVG $svg,
    ) {
    }

    /**
	 * Adds icons to menu items.
	 *
	 * @filter wp_nav_menu_objects
	 *
	 * @param WP_Post[]            $items Menu items.
	 * @param array<string, mixed> $args  object of wp_nav_menu() arguments.
	 * @return WP_Post[] customized menu items with icons.
	 */
	public function addIcons(array $items) {
        return Collection::make($items)
            ->map(function (WP_Post $item) {
                $icon = get_field('icon', $item);

                if (!is_numeric($icon)) {
                    return $item;
                }

                $item->title = sprintf(
                    '%s%s',
                    $this->svg->getAttachment($icon),
                    $item->title
                );

                return $item;
            })
            ->all();
	}
}
