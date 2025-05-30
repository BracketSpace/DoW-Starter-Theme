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
     * @return bool
     */
    protected function filterItem(mixed $item): bool
    {
        return is_string($item) &&
            class_exists($item) &&
            is_subclass_of($item, $this->parentClass);
    }

    /**
     * Initializes item.
     *
     * @param class-string<TItem> $item Item class.
     * @return TItem
     */
    protected function initializeItem(mixed $item): mixed
    {
        // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
        return new $item();
    }
}
