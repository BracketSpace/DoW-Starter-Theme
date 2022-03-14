<?php
/**
 * @package DoW Starter Theme
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

use DoWStarterTheme\Customizer\Panels;
use DoWStarterTheme\Customizer\Sections;

return [
	Panels\ExamplePanel::class => [
		Sections\ExampleSection::class => [
			'example-field' => [
				'type' => 'checkbox',
				'label' => 'Example Field',
			],
		],
	],
];
