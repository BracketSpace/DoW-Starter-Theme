<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Managers;

use DoWStarterTheme\Common\Abstracts\Manager;
use DoWStarterTheme\Common\Abstracts\Shortcode;

/**
 * @extends \DoWStarterTheme\Common\Abstracts\Manager<\DoWStarterTheme\Common\Abstracts\Shortcode<array<string, mixed>>>
 */
class ShortcodeManager extends Manager
{
    protected string $parentClass = Shortcode::class;

    protected string $configKey = 'app.shortcodes';

    /**
     * Registers shortcodes.
     *
     * @action init
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->getItems() as $shortcode) {
            add_shortcode($shortcode->getTag(), [$shortcode, 'renderCallback']);
        }
    }
}
