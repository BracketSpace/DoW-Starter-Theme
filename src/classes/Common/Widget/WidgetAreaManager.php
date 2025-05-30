<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Widget;

use DoWStarterTheme\Common\Abstracts\Manager;

/**
 * Widget areas manager class.
 *
 * @phpstan-type TItem array<string, mixed>
 *
 * @extends Manager<TItem, TItem>
 */
class WidgetAreaManager extends Manager
{
    protected string $configKey = 'widgets.areas';

    /**
     * Registers widgets.
     *
     * @action widgets_init
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->getItems() as $widgetArea) {
            register_sidebar($widgetArea);
        }
    }

        /**
     * Filters items list.
     *
     * @phpstan-assert-if-true TItem $item
     * @param mixed $item Item class.
     * @return bool
     */
    protected function filterItem(mixed $item): bool
    {
        return is_array($item);
    }

    /**
     * Initializes item.
     *
     * @param TItem $item Item class.
     * @return TItem
     */
    protected function initializeItem(mixed $item): mixed
    {
        return $item;
    }
}
