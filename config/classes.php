<?php
/**
 * @package DoW Starter Theme
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

return [
	/**
	 * General classes instantiated at startup
	 */
	'general' => [
		/**
		 * Core
		 */
		DoWStarterTheme\Core\Assets::class,
		DoWStarterTheme\Core\Layout::class,
		DoWStarterTheme\Core\TemplateFilters::class,

		/**
		 * Features
		 */
		DoWStarterTheme\Features\BlockSpacing::class,
		DoWStarterTheme\Features\Customizer::class,
		DoWStarterTheme\Features\Menu::class,
		DoWStarterTheme\Features\ReusableContent::class,
		DoWStarterTheme\Features\SVGSupport::class,
		DoWStarterTheme\Features\Widgets::class,

		/**
		 * Integration
		 */
		DoWStarterTheme\Integration\ACF::class,
		DoWStarterTheme\Integration\ACFBlockCreator::class,
		DoWStarterTheme\Integration\Editor::class,

		/**
		 * Post Types
		 */
		DoWStarterTheme\PostTypes\ReusableContentPostType::class,

		/**
		 * Shortcodes
		 */
		DoWStarterTheme\Shortcodes\DateShortcode::class,

		/**
		 * View
		 */
		DoWStarterTheme\View\Factory::class,
		DoWStarterTheme\View\Finder::class,
	],

	/**
	 * Widget classes passed to `register_widget`
	 * @see https://developer.wordpress.org/reference/functions/register_widget/
	 */
	'widgets' => [
		DoWStarterTheme\Widgets\SocialLinksWidget::class,
	],
];
