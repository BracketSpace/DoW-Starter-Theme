<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Abstracts;

/**
 * Abstract manager class.
 *
 * @template TItem of object
 *
 * @extends Manager<class-string<TItem>, TItem>
 */
abstract class ObjectManager extends Manager
{
    /**
     * Parent class name.
     *
     * @var class-string<TItem>
     */
    protected string $parentClass;

    /**
     * Filters items list.
     *
     * @phpstan-assert-if-true class-string<TItem> $item
     * @param mixed $item Item class.
     * @param mixed $key  Item key.
     * @return bool
     */
    protected function filterItem(mixed $item, mixed $key): bool
    {
        return is_string($item) &&
            class_exists($item) &&
            is_subclass_of($item, $this->parentClass);
    }

    /**
     * Initializes item.
     *
     * @param class-string<TItem> $item Item class.
     * @param mixed               $key  Item key.
     * @return TItem
     */
    protected function initializeItem(mixed $item, mixed $key): mixed
    {
        // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
        return new $item();
    }
}
