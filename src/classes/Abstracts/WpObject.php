<?php

declare(strict_types=1);

namespace DoWStarterTheme\Abstracts;

/**
 * WpObject class
 */
abstract class WpObject extends Labelable
{
	/**
	 * Returns object-specific labels.
	 *
	 * @return array<string, string>
	 */
	abstract protected static function getObjectLabelTemplates(): array;

	/**
	 * Returns a list of label templates.
	 *
	 * @return array<string, string>
	 */
	protected static function getLabelTemplates(): array
	{
		return array_merge(
			[
				/* translators: %s is a post type or taxonomy name. */
				'add_new_item' => sprintf(__('Add New %s', 'dow-starter-theme'), '{singular}'),
				/* translators: %s is a post type or taxonomy name. */
				'all_items' => sprintf(__('All %s', 'dow-starter-theme'), '{plural}'),
				/* translators: %s is a post type or taxonomy name. */
				'edit_item' => sprintf(__('Edit %s', 'dow-starter-theme'), '{singular}'),
				/* translators: %s is a post type or taxonomy name. */
				'item_link' => sprintf(_x('%s Link', 'navigation link block title', 'dow-starter-theme'), '{singular}'),
				'item_link_description' => sprintf(
					/* translators: %s is a post type or taxonomy name. */
					_x('A link to a %s.', 'navigation link block description', 'dow-starter-theme'),
					'{singularLower}'
				),
				/* translators: %s is a post type or taxonomy name. */
				'items_list' => sprintf(__('%s list', 'dow-starter-theme'), '{plural}'),
				/* translators: %s is a post type or taxonomy name. */
				'items_list_navigation' => sprintf(__('%s list navigation', 'dow-starter-theme'), '{plural}'),
				'name' => '{plural}',
				/* translators: %s is a post type or taxonomy name. */
				'not_found' => sprintf(__('No %s found.', 'dow-starter-theme'), '{pluralLower}'),
				/* translators: %s is a post type or taxonomy name. */
				'parent_item_colon' => sprintf(__('Parent %s:', 'dow-starter-theme'), '{singular}'),
				/* translators: %s is a post type or taxonomy name. */
				'search_items' => sprintf(__('Search %s', 'dow-starter-theme'), '{plural}'),
				'singular_name' => '{singular}',
				/* translators: %s is a post type or taxonomy name. */
				'view_item' => sprintf(__('View %s', 'dow-starter-theme'), '{singular}'),
			],
			static::getObjectLabelTemplates()
		);
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
}
