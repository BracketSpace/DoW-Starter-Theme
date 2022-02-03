<?php

declare(strict_types=1);

namespace DoWStarterTheme\Widgets;

use DoWStarterTheme\Helpers\SVG;

/**
 * Social Links Widget class
 */
class SocialLinksWidget extends \WP_Widget
{
	/**
	 * Current link title
	 *
	 * @var string
	 */
	private $currentLinkTitle;

	/**
	 * Widget constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'social_links',
			__('Social Links', 'dow-starter-theme'),
			[
				'description' => __('Social Links', 'dow-starter-theme'),
			]
		);
	}

	/**
	 * Output widget
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 *
	 * @param array<mixed> $args     Widget wrapper.
	 * @param array<mixed> $instance The widget options.
	 * @return void
	 */
	public function widget($args, $instance)
	{
		$widgetId = 'widget_' . $args['widget_id'];
		$links = [];

		$title = get_field('title', $widgetId);

		if (is_string($title) && $title !== '') {
			$title = $args['before_title'] . get_field('title', $widgetId) . $args['after_title'];
		}

		add_filter('wp_get_attachment_image_attributes', [ $this, 'attachmentAttributes' ]);

		while (have_rows('links', $widgetId)) {
			the_row();
			$link = get_sub_field('link');

			if (!is_array($link)) {
				continue;
			}

			$this->currentLinkTitle = $link['title'];
			$link['title'] = SVG::getAttachment(get_sub_field('icon'));

			$links[] = sprintf(
				'<li><a href="%2$s" target="%3$s">%1$s</li>',
				$link['title'],
				$link['url'],
				$link['target'],
			);
		}

		remove_filter('wp_get_attachment_image_attributes', [ $this, 'attachmentAttributes' ]);

		$menu = sprintf('<ul class="social-links-menu">%s</ul>', implode('', $links));

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'] . $title . $menu . $args['after_widget'];
	}

	/**
	 * Filters attachment attributes
	 *
	 * @param  array<string, mixed> $atts Attributes array.
	 * @return array<string, mixed>       Filtered attributes.
	 */
	public function attachmentAttributes(array $atts): array
	{
		$atts['alt'] = $this->currentLinkTitle;

		return $atts;
	}

	/**
	 * Creates widget form field
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 *
	 * @param array<mixed> $instance Widget options.
	 * @return string
	 */
	public function form($instance): string
	{
		return '';
	}
}
