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
            ->filter(fn($item) => $this->filterItem($item))
            ->map(fn($item) => $this->initializeItem($item))
            ->all();
    }

    /**
     * Filters items list.
     *
     * @phpstan-assert-if-true TConfigItem $item
     * @param mixed $item Item.
     * @return bool
     */
    abstract protected function filterItem(mixed $item): bool;

    /**
     * Initializes item.
     *
     * @param TConfigItem $item Item.
     * @return TItem
     */
    abstract protected function initializeItem(mixed $item): mixed;
}
