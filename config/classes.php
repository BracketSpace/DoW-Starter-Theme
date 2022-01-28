<?php
/**
 * @package DoW Starter Theme
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

return [
    /**
     * General classes instantiated at startup
     */
    'general' => [
        /**
         * Features
         */
        DoWStarterTheme\Features\SVGSupport::class,

        /**
         * Shortcodes
         */
        DoWStarterTheme\Shortcodes\DateShortcode::class,
    ],

    /**
     * Widget classes passed to `register_widget`
     * @see https://developer.wordpress.org/reference/functions/register_widget/
     */
    'widgets' => [
        DoWStarterTheme\Widgets\SocialLinksWidget::class,
    ],
];
