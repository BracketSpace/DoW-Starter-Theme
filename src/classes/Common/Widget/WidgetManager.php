<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Widget;

use DoWStarterTheme\Common\Abstracts\ObjectManager;

/**
 * @extends ObjectManager<Widget>
 */
class WidgetManager extends ObjectManager
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
