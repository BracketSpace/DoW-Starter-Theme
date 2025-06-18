<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Abstracts;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\Illuminate\Support\Collection;

/**
 * Abstract manager class.
 *
 * @template TConfigItem
 * @template TItem
 */
abstract class Manager implements Hookable
{
    /**
     * Config key that stores items.
     */
    protected string $configKey;

    /**
     * Class constructor.
     *
     * @param Config $config Config instance.
     */
    public function __construct(
        protected Config $config,
    ) {
    }

    /**
     * Registers items.
     *
     * @return void
     */
    abstract public function register(): void;

    /**
     * Gets the item to be registered.
     *
     * @return  array<TItem>
     */
    protected function getItems(): array
    {
        $items = $this->config->get($this->configKey);

        return Collection::make(is_array($items) ? $items : [])
            ->filter(fn($item, $key) => $this->filterItem($item, $key))
            ->map(fn($item, $key) => $this->initializeItem($item, $key))
            ->all();
    }

    /**
     * Filters items list.
     *
     * @phpstan-assert-if-true TConfigItem $item
     * @param mixed $item Item.
     * @param mixed $key  Item key.
     * @return bool
     */
    abstract protected function filterItem(mixed $item, mixed $key): bool;

    /**
     * Initializes item.
     *
     * @param TConfigItem $item Item.
     * @param mixed       $key  Item key.
     * @return TItem
     */
    abstract protected function initializeItem(mixed $item, mixed $key): mixed;
}
