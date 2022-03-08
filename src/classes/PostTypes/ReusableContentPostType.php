<?php

declare(strict_types=1);

namespace DoWStarterTheme\PostTypes;

/**
 * Reusable Content Post Type class
 */
class ReusableContentPostType extends PostType
{
	/**
	 * Returns post type args.
	 *
	 * @return array<string, mixed>
	 */
	protected static function getArgs(): array
	{
		return [
			'show_ui' => true,
			'menu_icon' => 'dashicons-admin-page',
			'show_in_rest' => true,
			'supports' => [ 'title', 'editor' ],
		];
	}
}
