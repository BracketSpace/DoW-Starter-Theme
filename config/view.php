<?php
/**
 * @package DoW Starter Theme
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

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
		DoWStarterTheme\View\Composers\Partials\ExampleComposer::class,
		DoWStarterTheme\View\Composers\Single::class,
	],
];
