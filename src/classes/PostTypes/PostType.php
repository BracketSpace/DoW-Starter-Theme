<?php

declare(strict_types=1);

namespace DoWStarterTheme\PostTypes;

use Illuminate\Support\Str;
use Micropackage\DocHooks\HookTrait;

/**
 * Post type abstract class
 */
abstract class PostType
{
	use HookTrait;

	/**
	 * Post type slug
	 */
	protected static string $slug;

	/**
	 * Post type name
	 */
	protected static string $name;

	/**
	 * Post type name in plural form
	 */
	protected static string $pluralName;

	/**
	 * Post type labels
	 *
	 * @var array<string, string>
	 */
	protected static array $labels;

	/**
	 * Featured image name
	 */
	protected static string $featuredImageName;

	/**
	 * Returns post type slug.
	 *
	 * @return string
	 */
	public static function getSlug(): string
	{
		if (!isset(static::$slug)) {
			$name = (string)preg_replace(
				'/PostType$/',
				'',
				static::class
			);

			$i = strrpos($name, '\\');
			static::$slug = Str::kebab($i !== false ? substr($name, $i + 1) : $name);
		}

		return static::$slug;
	}

	/**
	 * Returns post type name.
	 *
	 * @return string
	 */
	public static function getName(): string
	{
		if (!isset(static::$name)) {
			static::$name = Str::singular(Str::title(str_replace('-', ' ', static::getSlug())));
		}

		return static::$name;
	}

	/**
	 * Returns post type name in plural form.
	 *
	 * @return string
	 */
	public static function getPluralName(): string
	{
		if (!isset(static::$pluralName)) {
			static::$pluralName = Str::plural(static::getName());
		}

		return static::$pluralName;
	}

	/**
	 * Returns a list of label templates.
	 *
	 * @return array<string, string>
	 */
	protected static function getLabelTemplates(): array
	{
		return [
			'name' => '{plural}',
			'singular_name' => '{singular}',
			/* translators: %s is a post type name. */
			'add_new_item' => sprintf(__('Add New %s', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'edit_item' => sprintf(__('Edit %s', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'new_item' => sprintf(__('New %s', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'view_item' => sprintf(__('View %s', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'view_items' => sprintf(__('View %s', 'dow-starter-theme'), '{plural}'),
			/* translators: %s is a post type name. */
			'search_items' => sprintf(__('Search %s', 'dow-starter-theme'), '{plural}'),
			/* translators: %s is a post type name. */
			'not_found' => sprintf(__('No %s found.', 'dow-starter-theme'), '{pluralLower}'),
			/* translators: %s is a post type name. */
			'not_found_in_trash' => sprintf(__('No %s found in Trash.', 'dow-starter-theme'), '{pluralLower}'),
			/* translators: %s is a post type name. */
			'parent_item_colon' => sprintf(__('Parent Page:', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'all_items' => sprintf(__('All %s', 'dow-starter-theme'), '{plural}'),
			/* translators: %s is a post type name. */
			'archives' => sprintf(__('%s Archives', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'attributes' => sprintf(__('%s Attributes', 'dow-starter-theme'), '{singular}'),
			/* translators: %s is a post type name. */
			'insert_into_item' => sprintf(__('Insert into %s', 'dow-starter-theme'), '{singularLower}'),
			/* translators: %s is a post type name. */
			'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'dow-starter-theme'), '{singularLower}'),
			/* translators: %s is a post type name. */
			'filter_items_list' => sprintf(__('Filter %s list', 'dow-starter-theme'), '{pluralLower}'),
			/* translators: %s is a post type name. */
			'items_list_navigation' => sprintf(__('%s list navigation', 'dow-starter-theme'), '{plural}'),
			/* translators: %s is a post type name. */
			'items_list' => sprintf(__('%s list', 'dow-starter-theme'), '{plural}'),
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
			'item_link' => sprintf(_x('%s Link', 'navigation link block title', 'dow-starter-theme'), '{singular}'),
			'item_link_description' => sprintf(
				/* translators: %s is a post type name. */
				_x('A link to a %s.', 'navigation link block description', 'dow-starter-theme'),
				'{singularLower}'
			),
		];
	}

	/**
	 * Returns featured image labels.
	 *
	 * @return array<string, string>
	 */
	protected static function getFeaturedImageLabels(): array
	{
		$featuredImageName = static::$featuredImageName ?? __('Featured image', 'dow-starter-theme');

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
		$name = static::getName();
		$pluralName = static::getPluralName();

		$search = [
			'{singular}',
			'{singularLower}',
			'{plural}',
			'{pluralLower}',
		];

		$replace = [
			$name,
			Str::lower($name),
			$pluralName,
			Str::lower($pluralName),
		];

		$labels = array_merge(
			array_map(
				static fn ($item) => str_replace($search, $replace, $item),
				static::getLabelTemplates()
			),
			static::getFeaturedImageLabels()
		);

		if (isset(static::$labels)) {
			return array_merge($labels, static::$labels);
		}

		return $labels;
	}

	/**
	 * Returns post type args.
	 *
	 * @return array<string, mixed>
	 */
	protected static function getArgs(): array
	{
		return [];
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
