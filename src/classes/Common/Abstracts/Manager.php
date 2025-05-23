<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Abstracts;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\Illuminate\Support\Collection;

/**
 * Abstract manager class.
 *
 * @template TItem of object
 */
abstract class Manager implements Hookable
{
    /**
     * Parent class name.
     *
     * @var class-string<TItem>
     */
    protected string $parentClass;

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
            ->filter(fn($item) => $this->filterItemCallback($item))
            ->map(fn($item) => $this->initializeItemCallback($item))
            ->all();
    }

    /**
     * Filters items list.
     *
     * @phpstan-assert-if-true class-string<TItem> $item
     * @param mixed $item Item class.
     * @return bool
     */
    protected function filterItemCallback(mixed $item): bool
    {
        return is_string($item) && class_exists($item) && is_subclass_of($item, $this->parentClass);
    }

    /**
     * Initializes item.
     *
     * @param class-string<TItem> $item Item class.
     * @return TItem
     */
    protected function initializeItemCallback(string $item): mixed
    {
        // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
        return new $item();
    }
}
