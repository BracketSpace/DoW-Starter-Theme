<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Managers;

use DoWStarterTheme\Common\Abstracts\Manager;
use DoWStarterTheme\Common\Abstracts\Widget;

/**
 * @extends \DoWStarterTheme\Common\Abstracts\Manager<\DoWStarterTheme\Common\Abstracts\Widget>
 */
class WidgetManager extends Manager
{
    protected string $parentClass = Widget::class;

    protected string $configKey = 'widgets.classes';

    /**
     * Registers widgets.
     *
     * @action init
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->getItems() as $widget) {
            register_widget($widget);
        }
    }
}
