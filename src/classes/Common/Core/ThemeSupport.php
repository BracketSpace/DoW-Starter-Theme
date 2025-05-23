<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Core;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Common\Config\Config;

/**
 * Theme support class.
 */
class ThemeSupport implements Hookable
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
     * Setups image sizes.
     *
     * @action after_setup_theme
     *
     * @return void
     */
    public function setupThemeSupport(): void
    {
        foreach ($this->getThemeSupports() as $feature => $config) {
            if ($config === false) {
                remove_theme_support($feature);
                continue;
            }

            $args = [$feature];

            if ($config !== true) {
                $args[] = $config;
            }

            add_theme_support(...$args);
        }
    }

    /**
     * Gets the list of theme supports.
     *
     * @return  array<string, mixed>
     */
    private function getThemeSupports(): array
    {
        $imageSizes = $this->config->get('theme-support');

        return is_array($imageSizes) ? $imageSizes : [];
    }
}
