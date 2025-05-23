<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Managers;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\Illuminate\Support\Collection;

/**
 * Widget areas manager class.
 */
class WidgetAreaManager implements Hookable
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
     * Gets the list of widget areas to be registered.
     *
     * @return  array<string, array<string, mixed>>
     */
    protected function getItems(): array
    {
        $items = $this->config->get('widgets.areas');

        return Collection::make(is_array($items) ? $items : [])
            ->filter(static fn($item, $key) => is_string($key) && is_array($item))
            ->map(
                static fn(array $item, string $key) => [
                ...$item,
                'id' => $key,
                ]
            )
            ->all();
    }
}
