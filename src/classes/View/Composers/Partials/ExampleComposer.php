<?php
/**
 * phpcs:disable NeutronStandard.MagicMethods.RiskyMagicMethod.RiskyMagicMethod
 */

declare(strict_types=1);

namespace DoWStarterTheme\View\Composers\Partials;

use DoWStarterTheme\View\Composer;

/**
 * Test partial composer class
 */
class ExampleComposer extends Composer
{
    /**
     * Returns an array of data for the view.
     *
     * @return array<string, mixed> Data.
     */
    protected function with(): array
    {
        return [
            'example-var' => 'Example value from Composer...',
        ];
    }
}
