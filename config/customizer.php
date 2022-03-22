<?php
/**
 * @package DoW Starter Theme
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

return [
	// new Panel / new Section / new Field
	'panel:example_panel_1' => [
		'title' => __('Example Panel', 'dow-starter-theme'),
		'description' => __('Example Panel description', 'dow-starter-theme'),
		'priority' => 100,
		'sections' => [
			'section:example_section_1' => [
				'title' => __('Example Section', 'dow-starter-theme'),
				'description' => __('Example Section description', 'dow-starter-theme'),
				'priority' => 100,
				'fields' => [
					'example_field_1' => [
						'type' => 'checkbox',
						'label' => 'Example Field',
					],
				],
			],
		],
	],

	// new Section / new Field
	'section:example_section_2' => [
		'title' => __('Example Section', 'dow-starter-theme'),
		'description' => __('Example Section description', 'dow-starter-theme'),
		'priority' => 100,
		'fields' => [
			'example_field_2' => [
				'type' => 'checkbox',
				'label' => 'Example Field',
			],
		],
	],

	// existing Section / new Field
	'section:title_tagline' => [
		'fields' => [
			'example_field_3' => [
				'type' => 'checkbox',
				'label' => 'Example Field',
			],
		],
	],
];
