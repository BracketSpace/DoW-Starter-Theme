<?php

declare(strict_types=1);

namespace DoWStarterTheme\Integration;

use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Core\Config;
use WP_Block_Editor_Context;

/**
 * Gutenberg editor intefration class
 *
 * @phpstan-type Settings array<string, mixed>
 */
class Editor
{
	use HookTrait;

	/**
	 * Filters block editor settings.
	 *
	 * @filter block_editor_settings_all
	 *
	 * @param  Settings                $settings Block editor settings.
	 * @param  WP_Block_Editor_Context $context  Block editor context.
	 * @return Settings
	 */
	public function filterBlockEditorSettings(array $settings, WP_Block_Editor_Context $context): array
	{
		if ($context->post === null) {
			return $settings;
		}

		$config = Config::get("layout.{$context->post->post_type}");

		if (is_array($config)) {
			$settings['__experimentalFeatures']['layout'] = array_merge(
				$settings['__experimentalFeatures']['layout'],
				array_filter(
					$config,
					static fn($key) => in_array($key, ['contentSize', 'wideSize'], true),
					ARRAY_FILTER_USE_KEY
				)
			);

			if (isset($config['wideSize']) && $config['wideSize'] === false) {
				$settings['alignWide'] = false;
			}
		}

		return $settings;
	}
}
