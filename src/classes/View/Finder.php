<?php
/**
 * phpcs:disable NeutronStandard.MagicMethods.RiskyMagicMethod.RiskyMagicMethod
 */

declare(strict_types=1);

namespace DoWStarterTheme\View;

use DoWStarterTheme\Core\Config;
use DoWStarterTheme\Factories\Filesystem;

/**
 * View Finder class
 */
class Finder
{
	/**
	 * Filesystem instance.
	 *
	 * @var \DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem
	 */
	protected $fs;

	/**
	 * Sets up the view path.
	 *
	 * @throws \Exception When View location is not set or the directory does not exist.
	 */
	public function __construct()
	{
		$location = Config::get('view.location');

		if ($location === null) {
			throw new \Exception(__('Views location is not set.', 'dow-starter-theme'));
		}

		$rootFs = Filesystem::get();

		if (!$rootFs->exists($location)) {
			throw new \Exception(
				/* translators: %s is a directory path. */
				sprintf(__('Views directory: "%s" does not exist.', 'dow-starter-theme'), $location)
			);
		}

		$this->fs = Filesystem::get($location, 'views');
	}

	/**
	 * Finds template file.
	 *
	 * @param  string $name Template name.
	 * @return string|null  Template file path or null if not found.
	 */
	public function find(string $name): ?string
	{
		$ext = Config::get('view.extension', '.php');
		$pathBase = str_replace('.', '/', $name);
		$path = "{$pathBase}{$ext}";

		if (!$this->fs->exists($path)) {
			return null;
		}

		return $this->fs->path($path);
	}
}
