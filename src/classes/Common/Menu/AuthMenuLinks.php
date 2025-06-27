<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Menu;

/**
 * Handles url placeholders for auth links, like login, register, and logout.
 *
 * @phpstan-import-type MenuItem from MenuItemFilter
 */
class AuthMenuLinks extends MenuItemFilter
{
    /**
     * Replaces URL in menu item.
     *
     * @param MenuItem $item Menu item.
     * @return MenuItem customized menu item with icons.
     */
    public function filterItem($item)
    {
        // @phpstan-ignore assign.propertyReadOnly
        $item->url = match ($item->url) {
            '#logout' => wp_logout_url(),
            '#login' => wp_login_url(),
            '#register' => wp_registration_url(),
            default => $item->url,
        };

        return $item;
    }
}
