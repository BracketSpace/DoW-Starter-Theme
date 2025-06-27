<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Menu;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\Illuminate\Support\Collection;

/**
 * Handles url placeholders for auth links, like login, register, and logout.
 *
 * @phpstan-type MenuItem \WP_Post & object{
 *    attr_title: string,
 *    classes: array<string>,
 *    db_id: int,
 *    description: string,
 *    menu_item_parent: int,
 *    object: string,
 *    object_id: int,
 *    post_parent: int,
 *    target: string,
 *    title: string,
 *    type: string,
 *    type_label: string,
 *    url: string,
 *    xfn: string,
 *    _invalid: string,
 * }
 */
abstract class MenuItemFilter implements Hookable
{
    /**
     * Replaces URLs in menu items.
     *
     * @filter wp_nav_menu_objects
     *
     * @param array<MenuItem> $items Menu items.
     * @return array<MenuItem> customized menu items with icons.
     */
    public function filter(array $items) {
        return Collection::make($items)
            ->map(
                fn ($item) => $this->filterItem($item)
            )
            ->all();
    }

    /**
     * Filter a single menu item.
     *
     * @param MenuItem $item The menu item to filter.
     * @return MenuItem
     */
    abstract public function filterItem($item);
}
