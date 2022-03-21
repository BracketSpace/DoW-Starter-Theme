<?php

declare(strict_types=1);

namespace DoWStarterTheme\PostTypes;

use DoWStarterTheme\Abstracts\WpObject;
use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Post type abstract class
 */
abstract class PostType extends WpObject
{
	/**
	 * Object class name suffix
	 */
	protected static string $suffix = 'PostType';

	/**
	 * Featured image name
	 */
	protected static string $featuredImageName;

	/**
	 * Returns a list of label templates.
	 *
	 * @return array<string, string>
	 */
	protected static function getObjectLabelTemplates(): array
	{
		return [
			/* translators: %s is a post type name. */
			'archives' => sprintf(__('%s Archives', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'attributes' => sprintf(__('%s Attributes', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'filter_items_list' => sprintf(__('Filter %s list', 'dow-starter-theme'), '{pluralLower}'),
			/* translators: %s is a post type name. */
			'insert_into_item' => sprintf(__('Insert into %s', 'dow-starter-theme'), '{singularLower}'),
			/* translators: %s is a post type name. */
			'item_published' => sprintf(__('%s published.', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'item_published_privately' => sprintf(__('%s published privately.', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'item_reverted_to_draft' => sprintf(__('%s reverted to draft.', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'item_scheduled' => sprintf(__('%s scheduled.', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'item_updated' => sprintf(__('%s updated.', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'new_item' => sprintf(__('New %s', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'not_found_in_trash' => sprintf(__('No %s found in Trash.', 'dow-starter-theme'), '{pluralLower}'),
			/* translators: %s is a post type name. */
			'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'dow-starter-theme'), '{singularLower}'),
			/* translators: %s is a post type name. */
			'view_items' => sprintf(__('View %s', 'dow-starter-theme'), '{plural}'),
		];
	}

	/**
	 * Returns featured image labels.
	 *
	 * @return array<string, string>
	 */
	protected static function getFeaturedImageLabels(): array
	{
		$featuredImageName = static::$featuredImageName ?? __('featured image', 'dow-starter-theme');

		return array_map(
			static fn ($value) => Str::ucfirst(sprintf($value, $featuredImageName)),
			[
				'featured_image' => '%s',
				/* translators: %s is a featured image name. */
				'set_featured_image' => __('Set %s', 'dow-starter-theme'),
				/* translators: %s is a featured image name. */
				'remove_featured_image' => __('Remove %s', 'dow-starter-theme'),
				/* translators: %s is a featured image name. */
				'use_featured_image' => __('Use as %s', 'dow-starter-theme'),
			]
		);
	}

	/**
	 * Returns prepared post type labels.
	 *
	 * @return array<string, string>
	 */
	protected static function getLabels(): array
	{
		return array_merge(
			parent::getLabels(),
			static::getFeaturedImageLabels()
		);
	}

	/**
	 * Registers a post type.
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function register(): void
	{
		// phpcs:ignore WordPress.NamingConventions.ValidPostTypeSlug.NotStringLiteral
		register_post_type(
			static::getSlug(),
			array_merge(
				static::getArgs(),
				['labels' => static::getLabels()]
			)
		);
	}
}
