<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Contracts;

/**
 * Service provider contract.
 */
interface ServiceProvider
{
    /**
     * Defines application services.
     *
     * @return array<string, mixed>
     */
    public function provides(): array;
}
