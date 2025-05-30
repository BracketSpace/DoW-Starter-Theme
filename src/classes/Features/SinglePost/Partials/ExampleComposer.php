<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features\SinglePost\Partials;

use DoWStarterTheme\Common\View\ViewComposer;

/**
 * Test partial composer class
 */
class ExampleComposer extends ViewComposer
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
