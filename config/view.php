<?php
/**
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

use DoWStarterTheme\Features\SinglePost;

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
        SinglePost\Partials\ExampleComposer::class,
        SinglePost\SingleComposer::class,
    ],
];
