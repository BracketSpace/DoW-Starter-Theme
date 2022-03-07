<?php

declare(strict_types=1);

namespace DoWStarterTheme\Core;

use DoWStarterTheme\Factories\Filesystem;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;

/**
 * TemplateFilters trait
 */
class TemplateFilters
{
	use HookTrait;

	/**
	 * Template types
	 *
	 * @var array<string>
	 */
	private $templateTypes = [
		'404',
		'archive',
		'attachment',
		'author',
		'category',
		'date',
		'embed',
		'frontpage',
		'home',
		'index',
		'page',
		'paged',
		'privacypolicy',
		'search',
		'single',
		'singular',
		'tag',
		'taxonomy',
	];

	/**
	 * Hooks `templateHierarchy` method for each template type filter.
	 */
	public function __construct()
	{
		foreach ($this->templateTypes as $type) {
			add_filter("{$type}_template_hierarchy", [$this, 'filterTemplateHierarchy']);
		}
	}

	/**
	 * Determines the view to include.
	 *
	 * @filter template_include
	 *
	 * @param  string $file Template filename.
	 * @return string
	 */
	public function filterTemplateInclude(string $file): string
	{
		$path = wp_normalize_path($file);
		$viewsPath = Filesystem::get('views')->path('');
		$ext = str_replace('.', '\.', Config::get('view.extension'));

		$template = (string)preg_replace(
			"/{$ext}$/",
			'',
			str_replace($viewsPath, '', $path)
		);

		Theme::getService(Layout::class)->setTemplate($template);

		// Always return path to index.php - it's the entry point for the entire theme.
		return Filesystem::get()->path('index.php');
	}

	/**
	 * Adds views path to template files.
	 *
	 * @param  array<string> $files Template files.
	 * @return array<string>
	 */
	public function filterTemplateHierarchy(array $files): array
	{
		$location = trim(Config::get('view.location'), '/');

		foreach ($files as &$file) {
			// Prefix each file with views location path.
			$file = "{$location}/{$file}";
		}

		return $files;
	}
}
