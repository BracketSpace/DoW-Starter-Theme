<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Menu;

use DoWStarterTheme\Common\Helpers\SVG;

/**
 * Adds icons support to menu items.
 *
 * @phpstan-import-type MenuItem from MenuItemFilter
 */
class MenuIcons extends MenuItemFilter
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
     * Adds icon to menu item.
     *
     * @param MenuItem $item Menu item.
     * @return MenuItem customized menu item with icon.
     */
    public function filterItem($item) {
        $icon = get_field('icon', $item);

        if (! is_numeric($icon)) {
            return $item;
        }

        // @phpstan-ignore assign.propertyReadOnly
        $item->title = sprintf(
            '%s%s',
            $this->svg->getAttachment((int)$icon),
            $item->title
        );

        return $item;
    }
}
