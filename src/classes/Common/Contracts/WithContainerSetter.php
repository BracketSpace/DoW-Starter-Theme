<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Contracts;

use DoWStarterTheme\Deps\DI\Container;

/**
 * Container setter contract.
 */
interface WithContainerSetter
{
    /**
     * Sets the container instance.
     *
     * @param  Container $container Container instance.
     * @return void
     */
    public static function setContainer(Container $container): void;
}
