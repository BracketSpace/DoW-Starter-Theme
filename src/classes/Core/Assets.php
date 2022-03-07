<?php

declare(strict_types=1);

namespace DoWStarterTheme\Core;

use DoWStarterTheme\Factories\Filesystem;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;

/**
 * Assets class
 */
class Assets
{
	use HookTrait;

	/**
	 * Assets list
	 *
	 * @var array<string, string>
	 */
	private $assets = [];

	/**
	 * Prepares assets configuration.
	 */
	public function __construct()
	{
		$assets = Config::get('assets');

		if (!is_array($assets)) {
			return;
		}

		foreach ($assets as $name => $config) {
			if (!is_string($name)) {
				continue;
			}

			if (is_string($config)) {
				$filename = $config;
			} elseif (is_array($config) && array_key_exists('file', $config)) {
				$filename = $config['file'];

				if (array_key_exists('hook', $config)) {
					add_action($config['hook'], fn() => $this->enqueueAsset($name));
				}
			} else {
				continue;
			}

			$this->assets[$name] = $filename;
		}
	}

	/**
	 * Enqueues asset with given name.
	 *
	 * @param  string $name Asset name.
	 * @return void
	 * @throws \Exception When asset with given name does not exist.
	 */
	protected function enqueueAsset(string $name = 'front'): void
	{
		if (!array_key_exists($name, $this->assets)) {
			throw new \Exception(
				/* translators: %s is an asset name. */
				sprintf(__('The asset with the name "%s" does not exist.', 'dow-starter-theme'), $name)
			);
		}

		$fs = Filesystem::get('build');
		$filename = $this->assets[$name];

		if (!$fs->exists($filename)) {
			throw new \Exception(
				sprintf(
					/* translators: %s is an asset name. */
					__(
						'There is no file for asset named "%s". Did you forgot to build the assets?',
						'dow-starter-theme'
					),
					$name
				)
			);
		}

		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$type = $ext === 'js' ? 'script' : 'style';
		$func = "wp_enqueue_{$type}";

		if (!is_callable($func)) {
			return;
		}

		$depsFile = $ext === 'js' ? preg_replace('/.js$/', '.asset.php', $filename) : null;
		$data = is_string($depsFile) && $fs->exists($depsFile) ? require $fs->path($depsFile) : null;
		$deps = is_array($data) && array_key_exists('dependencies', $data) ? $data['dependencies'] : [];
		$version = is_array($data) && array_key_exists('version', $data) ? $data['version'] : $fs->mtime($filename);
		$handle = "dow-starter-theme-{$name}";

		$args = [
			$handle,
			$fs->url($filename),
			$deps,
			$version,
		];

		if ($ext === 'js') {
			$args[] = true;
		}

        // phpcs:ignore NeutronStandard.Functions.DisallowCallUserFunc.CallUserFunc
		call_user_func($func, ...$args);
	}

	/**
	 * Enqueues livereload script in local environment.
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function livereload(): void
	{
		if (is_admin() || wp_get_environment_type() !== 'local') {
			return;
		}

        // phpcs:disable WordPress.WP.EnqueuedResourceParameters
		wp_enqueue_script('livereload', 'http://localhost:35729/livereload.js');
	}
}
