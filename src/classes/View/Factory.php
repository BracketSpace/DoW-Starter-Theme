<?php

declare(strict_types=1);

namespace DoWStarterTheme\View;

use DoWStarterTheme\Core\Config;

/**
 * View Factory class
 *
 * @phpstan-type ComposerClass class-string<\DoWStarterTheme\View\Composer>
 */
class Factory
{
	/**
	 * Composer instances
	 *
	 * @var array<ComposerClass, \DoWStarterTheme\View\Composer>
	 */
	private $composers = [];

	/**
	 * Map of view names to composer classes
	 *
	 * @var array<string, array<ComposerClass>>
	 */
	private $viewComposerMap = [];

	/**
	 * Sets up the Factory.
	 */
	public function __construct()
	{
		$this->registerComposers();
	}

	/**
	 * Registers composers.
	 *
	 * @return void
	 */
	private function registerComposers(): void
	{
		/**
		 * @var array<string>
		 */
		$classes = Config::get('view.composers');

		foreach ($classes as $class) {
			if (!is_subclass_of($class, Composer::class)) {
				continue;
			}

			$this->mapComposerToViews($class);
		}
	}

	/**
	 * Maps Composer to views.
	 *
	 * @param ComposerClass $class Composer class name.
	 * @return void
	 */
	private function mapComposerToViews(string $class): void
	{
		$views = $class::getViews();

		foreach ($views as $view) {
			if (!isset($this->viewComposerMap[$view])) {
				$this->viewComposerMap[$view] = [];
			}

			$this->viewComposerMap[$view][] = $class;
		}
	}

	/**
	 * Returns composer instance.
	 *
	 * @param  ComposerClass $class Composer class name.
	 * @return Composer
	 */
	private function getComposer(string $class): Composer
	{
		if (!isset($this->composers[$class])) {
            // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
			$this->composers[$class] = new $class();
		}

		return $this->composers[$class];
	}

	/**
	 * Apply composers to the view.
	 *
	 * @param string $name View name.
	 * @param View   $view View instance.
	 * @return void
	 */
	private function compose(string $name, View $view): void
	{
		if (!isset($this->viewComposerMap[$name])) {
			return;
		}

		foreach ($this->viewComposerMap[$name] as $class) {
			$this->getComposer($class)->compose($view);
		}
	}

	/**
	 * View instances
	 *
	 * @var array<\DoWStarterTheme\View\View>
	 */
	private $views = [];

	/**
	 * Returns view instance prepared with given data.
	 *
	 * @param  string               $name View name
	 * @param  array<string, mixed> $data Data.
	 * @return \DoWStarterTheme\View\View
	 */
	public function get(string $name, array $data = []): View
	{
		if (!$this->exists($name)) {
			$view = $this->make($name, $data);
		} else {
			$view = $this->views[$name];
			$view->with($data);
		}

		$this->compose($name, $view);

		return $view;
	}

	/**
	 * Checks if an instance of View class for given view name exists.
	 *
	 * @param  string $name View name.
	 * @return bool
	 */
	public function exists(string $name): bool
	{
		return array_key_exists($name, $this->views);
	}

	/**
	 * Makes new vView instance.
	 *
	 * @param  string               $name View name.
	 * @param  array<string, mixed> $data View data.
	 * @return \DoWStarterTheme\View\View
	 */
	private function make(string $name, array $data = []): View
	{
		$this->views[$name] = new View($name, $data);

		return $this->views[$name];
	}
}
