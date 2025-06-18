<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Helpers;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\DI\Container;
use DoWStarterTheme\Deps\Illuminate\Support\Collection;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookAnnotations;
use InvalidArgumentException;

/**
 * Factory class.
 */
class Factory
{
    /**
     * Class constructor.
     *
     * @param Container       $container Container instance.
     * @param HookAnnotations $hooks     Hooks registrar instance.
     */
    public function __construct(
        protected Container $container,
        protected HookAnnotations $hooks,
    ) {
    }

    /**
     * Creates object from given class name.
     *
     * @template T of object
     *
     * @param  class-string<T> $class      Class name to create.
     * @param  string          $objectName Object name for the error message.
     * @return T
     */
    public function make(string $class, string $objectName = 'object'): object
    {
        if (! class_exists($class)) {
            throw new \LogicException(
                sprintf('[%s] %s cannot be registered.', $class, $objectName)
            );
        }

        /** @var T */
        $object = $this->container->get($class);

        if ($object instanceof Hookable) {
            $this->hooks->add_hooks($object);
        }

        return $object;
    }

    /**
     * Creates objects from collection of class names.
     *
     * @template T of object
     *
     * @param  Collection<int, class-string<T>> $classes    Collection of class names to create.
     * @param  string                           $objectName Object name for the error message.
     * @return Collection<int, T>
     */
    public function makeFromCollection(
        Collection $classes,
        string $objectName = 'object'
    ): Collection {
        return $classes->map(
            fn (string $class) => $this->make($class, $objectName)
        );
    }

    /**
     * Creates objects from array of class names.
     *
     * @template T of object
     *
     * @param  array<class-string<T>> $classes    Array of class names to create.
     * @param  string                 $objectName Object name for the error message.
     * @return Collection<int, T>
     */
    public function makeFromArray(
        array $classes,
        string $objectName = 'object'
    ): Collection {
        return $this->makeFromCollection(Collection::make($classes), $objectName);
    }

    /**
     * Creates object from given class name, checking if it is a child of given parent class.
     *
     * @template T of object
     *
     * @param  class-string    $class      Class name of the object to create.
     * @param  class-string<T> $parent     Parent class name to check.
     * @param  string          $objectName Object name for the error message.
     * @return T
     *
     * @throws InvalidArgumentException
     */
    public function makeChildOf(
        string $class,
        string $parent,
        string $objectName = 'object'
    ): object {
        if (! is_a($class, $parent, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Class "%s" is not a descendant of "%s".',
                    $class,
                    $parent
                )
            );
        }

        return $this->make($class, $objectName);
    }

    /**
     * Creates objects from collection of class names, ensuring they are children of the given parent class.
     *
     * @template T of object
     *
     * @param  Collection<int, class-string> $classes    Collection of class names to create.
     * @param  class-string<T>               $parent     Parent class name to check.
     * @param  string                        $objectName Object name for the error message.
     * @return Collection<int, T>
     *
     * @throws InvalidArgumentException
     */
    public function makeChildrenOfFromCollection(
        Collection $classes,
        string $parent,
        string $objectName = 'object'
    ): Collection {
        return $classes->map(
            fn (string $class) => $this->makeChildOf($class, $parent, $objectName)
        );
    }

    /**
     * Creates objects from array of class names, ensuring they are children of the given parent class.
     *
     * @template T of object
     *
     * @param  array<class-string> $classes    Array of class names to create.
     * @param  class-string<T>     $parent     Parent class name to check.
     * @param  string              $objectName Object name for the error message.
     * @return Collection<int, T>
     *
     * @throws InvalidArgumentException
     */
    public function makeChildrenOf(
        array $classes,
        string $parent,
        string $objectName = 'object'
    ): Collection {
        return $this->makeChildrenOfFromCollection(
            new Collection($classes),
            $parent,
            $objectName
        );
    }
}
