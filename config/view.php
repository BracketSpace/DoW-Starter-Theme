<?php
/**
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

use DoWStarterTheme\Features\Composers;

return [
    /**
     * Views location
     */
    'location' => 'src/views',

    /**
     * Views file's extension
     */
    'extension' => '.php',

    /**
     * Composers
     */
    'composers' => [
        Composers\Partials\ExampleComposer::class,
        Composers\Single::class,
    ],
];
