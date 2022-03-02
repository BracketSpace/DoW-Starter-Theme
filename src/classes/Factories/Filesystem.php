<?php

declare(strict_types=1);

namespace DoWStarterTheme\Factories;

use DoWStarterTheme\Dependencies\Micropackage\Filesystem\Filesystem as FilesystemClass;

/**
 * Filesystem factory class
 */
class Filesystem
{
	/**
	 * Filesystem instances
	 *
	 * @var array<\DoWStarterTheme\Dependencies\Micropackage\Filesystem\Filesystem>
	 */
	private static $instances = [];

	/**
	 * Returns instance of Filesystem class initialized with given path.
	 *
	 * @param  string      $path  Filesystem root path.
	 * @param  string|null $alias Instance alias.
	 * @return FilesystemClass
	 */
	public static function get(string $path = 'root', ?string $alias = null): FilesystemClass
	{
		if ($path === 'root') {
			return self::getRoot();
		}

		if (is_null($alias)) {
			$alias = $path;
		}

		if (!array_key_exists($alias, self::$instances)) {
			self::$instances[$alias] = self::createInstance($path);
		}

		return self::$instances[$alias];
	}

	/**
	 * Returns a root Filesystem instance.
	 *
	 * @return FilesystemClass
	 */
	private static function getRoot(): FilesystemClass
	{
		if (!array_key_exists('root', self::$instances)) {
			throw new \Exception(
				__('Accessing root filesystem before initialization is not supported.', 'dow-starter-theme')
			);
		}

		return self::$instances['root'];
	}

	/**
	 * Creates Filesystem instance.
	 *
	 * @param  string $path Filesystem root path.
	 * @return FilesystemClass
	 */
	private static function createInstance(string $path): FilesystemClass
	{
		$noRoot = false;

		try {
			$root = self::getRoot();

			if ($root->is_dir($path)) {
				$path = $root->path($path);
			}
		} catch (\Throwable $e) {
			$noRoot = true;
		}

		$fs = new FilesystemClass($path);

		if (!$fs->is_dir('')) {
			$errors = [
				/* translators: %s is the filesystem root path */
				sprintf(__('Filesystem initialized with invalid path: %s', 'dow-starter-theme'), $path),
			];

			if ($noRoot) {
				$errors[] = __('Root Filesystem not initialized.', 'dow-starter-theme');
			}

			throw new \Exception(implode(' ', $errors));
		}

		return $fs;
	}
}
