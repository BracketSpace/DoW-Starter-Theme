<?php

declare(strict_types=1);

namespace DoWStarterTheme\Core;

use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;
use DoWStarterTheme\Deps\Micropackage\Singleton\Singleton;

/**
 * Core Theme class
 *
 * @phpstan-type ImageSize array{width?: int, height?: int, crop?: bool}
 *
 * @template T
 */
class Theme extends Singleton
{
	use HookTrait;

	/**
	 * Filesystem instance.
	 *
	 * @var \DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem
	 */
	protected static $fs;

	/**
	 * Returns service instance.
	 *
	 * @param  class-string<T> $class Class name.
	 * @return T
	 */
	public static function getService(string $class)
	{
		$instance = static::get();

		if (!array_key_exists($class, $instance->services)) {
			throw new \Exception(
				/* translators: %s is a class name. */
				sprintf(__('The service "%s" does not exist.', 'dow-starter-theme'), $class)
			);
		}

		return $instance->services[$class];
	}

	/**
	 * Constructor.
	 *
	 * @param \DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem $fs Filesystem instance
	 */
	protected function __construct(Filesystem $fs)
	{
		parent::__construct();

		static::$fs = $fs;
	}

	/**
	 * Class instances.
	 *
	 * @var array<T>
	 */
	protected $services = [];

	/**
	 * Widget classes
	 *
	 * @var array<string>
	 */
	protected $widgets = [];

	/**
	 * Theme support features to be removed
	 *
	 * @var array<string>
	 */
	protected $removeSupport = [];

	/**
	 * Image sizes
	 *
	 * phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName
	 * @var array<string, ImageSize>
	 */
	protected $imageSizes = [];

	/**
	 * Bootstraps the theme.
	 *
	 * @param array<class-string<T>> $classes Class names to bootstrap.
	 * @return void
	 */
	public function bootstrap(array $classes = []): void
	{
		foreach ($classes as $class) {
			if (!class_exists($class)) {
				continue;
			}

			// phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
			$this->services[$class] = new $class();

			// phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
			if ($this->isClassHookable($class)) {
				$this->services[$class]->add_hooks();
			}
		}
	}

	/**
	 * Checks whether the given class uses HookTrait.
	 *
	 * @param  string $class Class name.
	 * @return bool
	 */
	private function isClassHookable(string $class): bool
	{
		return in_array(HookTrait::class, $this->getClassTraits($class), true);
	}

	/**
	 * Returns all traits used by the given class or any of it's parents.
	 *
	 * @param  string $class Class name.
	 * @return array<string, string> List of used traits.
	 */
	public function getClassTraits(string $class): array
	{
		$traits = [];

		$addUses = static function (string $classOrTrait) use (&$traits): void {
			$uses = class_uses($classOrTrait, true);

			if (!is_array($uses)) {
				return;
			}

			$traits = array_merge($uses, $traits);
		};

		do {
			// phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
			$addUses($class);
			$class = get_parent_class($class);
		} while (is_string($class));

		$traits = array_filter($traits);

		foreach ($traits as $trait) {
			// phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
			$addUses($trait);
		}

		return array_unique($traits);
	}

	/**
	 * Adds theme support options.
	 *
	 * @param array<string,mixed> $support Theme support configuration.
	 * @return void
	 */
	public function addThemeSupport(array $support): void
	{
		foreach ($support as $key => $value) {
			if ($value === false) {
				$this->removeSupport[] = $key;
				continue;
			}

			$args = [$key];

			if ($value !== true) {
				$args[] = $value;
			}

			add_theme_support(...$args);
		}

		// Disable admin bar styles.
		add_theme_support('admin-bar', [ 'callback' => '__return_false' ]);
	}

	/**
	 * Removes theme support if necessary.
	 *
	 * @action after_setup_theme 100
	 *
	 * @return void
	 */
	public function removeThemeSupport(): void
	{
		foreach ($this->removeSupport as $feature) {
			remove_theme_support($feature);
		}
	}

	/**
	* Adds widget classes for future registration in `widgets_init` action.
	 *
	 * @param array<string> $widgets Widget classes to be registered.
	 * @return void
	 */
	public function addWidgets(array $widgets): void
	{
		$this->widgets = $widgets;
	}

	/**
	 * Registers widgets
	 *
	 * @action widgets_init
	 *
	 * @return void
	 */
	public function registerWidgets(): void
	{
		foreach ($this->widgets as $widget) {
			if (!class_exists($widget)) {
				continue;
			}

			register_widget($widget);
		}
	}

	/**
	 * Adds image sizes from config file.
	 *
	 * phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName
	 * @param  array<string, ImageSize> $sizes Image sizes.
	 * @return void
	 */
	public function addImageSizes(array $sizes): void
	{
		$this->imageSizes = $sizes;
	}

	/**
	 * Sets up image sizes.
	 *
	 * @return void
	 */
	protected function setupImageSizes(): void
	{
		foreach ($this->imageSizes as $size => $config) {
			$args = [ $size ];

			if (isset($config['width']) && isset($config['height'])) {
				$args[] = $config['width'];
				$args[] = $config['height'];

				if (isset($config['crop'])) {
					$args[] = $config['crop'];
				}
			}

			add_image_size(...$args);
		}
	}

	/**
	 * Loads theme textdomain.
	 *
	 * @action after_setup_theme
	 *
	 * @return void
	 */
	public function setup()
	{
		load_theme_textdomain('dow-starter-theme', get_stylesheet_directory() . '/languages');

		$this->setupImageSizes();
	}
}
