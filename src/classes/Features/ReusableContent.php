<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use DoWStarterTheme\Core\Config;
use DoWStarterTheme\Core\Theme;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;

/**
 * Reusable Content feature class
 */
class ReusableContent
{
	use HookTrait;

	/**
	 * Displays content for given slot.
	 *
	 * @param string      $slot Slot name.
	 * @param bool|string $wrap Whether to wrap the content in a div element. If a string is passed, it will be used as
	 *                          a wrapper element's class name.
	 * @return void
	 */
	public static function display(string $slot, $wrap = true): void
	{
		echo static::get($slot, $wrap);
	}

	/**
	 * Gets content for given slot.
	 *
	 * @param string      $slot Slot name.
	 * @param bool|string $wrap Whether to wrap the content in a div element. If a string is passed, it will be used as
	 *                          a wrapper element's class name.
	 * @return string|null
	 */
	public static function get(string $slot, $wrap = true): ?string
	{
		$content = static::getRawContent($slot);

		if (!is_string($content)) {
			return null;
		}

		if ($wrap !== false) {
			$class = is_string($wrap) ? $wrap : 'reusable-content';

			return sprintf(
				'<div class="%1$s">%2$s</div>',
				$class,
				$content
			);
		}

		return $content;
	}

	/**
	 * Gets raw content for given slot
	 *
	 * @param  string $slot Slot name.
	 * @return string
	 */
	public static function getRawContent(string $slot): ?string
	{
		$slotPosts = get_option('content_slot_posts', []);

		if (! array_key_exists($slot, $slotPosts) || count($slotPosts[$slot]) === 0) {
			return null;
		}

		$posts = get_posts(
			[
			'post_type' => 'reusable-content',
			'posts_per_page' => -1,
			'post__in' => $slotPosts[$slot],
			'orderby' => 'post__in',
			]
		);

		$bs = Theme::getService(BlockSpacing::class);
		$rc = Theme::getService(static::class);
		$content = '';

		foreach ($posts as $post) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound, Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$content .= apply_filters('the_content', $post->post_content);

			$styles = $bs->getStyles($post->ID);

			if (!is_string($styles)) {
				continue;
			}

			$rc->styles[] = $styles;
		}

		return $content;
	}

	/**
	 * Content slots config
	 *
	 * @var array<string, string>
	 */
	protected array $config;

	/**
	 * Array of styles for reusable content
	 *
	 * @var array<string, mixed>
	 */
	protected array $styles = [];

	/**
	 * Loads content slots config.
	 */
	public function __construct()
	{
		$this->config = Config::get('content-slots');
	}

	/**
	 * Prints styles from BlockSpacing class for reusable content posts.
	 *
	 * @action wp_footer
	 *
	 * @return void
	 */
	public function printStyles(): void
	{
		if (count($this->styles) === 0) {
			return;
		}

		printf(
			"<style>\n%s\n</style>",
			implode("\n\n", $this->styles)
		);
	}

	/**
	 * Filter content slots ACF field
	 *
	 * @filter acf/load_field
	 *
	 * @param  array<string, mixed> $field Field config.
	 * @return array<string, mixed>
	 */
	public function contentSlotsField(array $field): array
	{
		if ($field['name'] === 'content_slot') {
			$field['choices'] = $this->config;
		}

		return $field;
	}

	/**
	 * Update slots config
	 *
	 * @action acf/save_post
	 *
	 * @param int|string $postId Post id.
	 * @return void
	 */
	public function savePost($postId): void
	{
		if (get_post_type((int)$postId) !== 'reusable-content') {
			return;
		}

		$slots = get_field('content_slot', $postId);
		$slotPosts = get_option('content_slot_posts', []);
		$allSlots = array_unique(array_merge($slots, array_keys($slotPosts)));

		foreach ($allSlots as $slot) {
			if (! array_key_exists($slot, $slotPosts)) {
				$slotPosts[$slot] = [$postId];
			} elseif (in_array($slot, $slots, true) && ! in_array($postId, $slotPosts[$slot], true)) {
				array_push($slotPosts[$slot], $postId);
			} elseif (! in_array($slot, $slots, true) && in_array($postId, $slotPosts[$slot], true)) {
				$slotPosts[$slot] = array_diff($slotPosts[$slot], [$postId]);
			}
		}

		update_option('content_slot_posts', $slotPosts);
	}
}
