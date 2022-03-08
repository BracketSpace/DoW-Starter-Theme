<?php

declare(strict_types=1);

namespace DoWStarterTheme\PostTypes;

/**
 * Example post type class
 */
class ExamplePostType extends PostType
{
	/**
	 * Returns post type args.
	 *
	 * @return array<string, mixed>
	 */
	protected static function getArgs(): array
	{
		return [
			'public' => true,
			'supports' => ['title', 'editor', 'thumbnail'],
			'show_in_rest' => true,
		];
	}
}
