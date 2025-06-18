<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Traits;

use DoWStarterTheme\Deps\DI\Container;

/**
 * Set container trait.
 */
trait HasContainerSetter
{
    /**
     * Container instance.
     *
     * @var Container|null
     */
    protected static ?Container $container = null;

    /**
     * Sets the container.
     *
     * @param Container $container Container instance.
     * @return void
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }
}
