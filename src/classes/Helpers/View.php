<?php

declare(strict_types=1);

namespace DoWStarterTheme\Helpers;

use DoWStarterTheme\Core\Theme;
use DoWStarterTheme\Core\Layout;
use DoWStarterTheme\View\Factory;

/**
 * View Helper class.
 */
final class View
{
	/**
	 * View variables stack.
	 *
	 * @var  array<array<string, mixed>>
	 */
	private static array $variablesStack = [];

	/**
	 * Pushes variables to the end of stack.
	 *
	 * Method used internally by core, do not use it.
	 *
	 * @param   array<string, mixed> $variables List of variables.
	 * @return  void
	 */
	public static function pushVariables(array $variables): void
	{
		self::$variablesStack[] = $variables;
	}

	/**
	 * Pops variables from the end of stack.
	 *
	 * Method used internally by core, do not use it.
	 *
	 * @return  void
	 */
	public static function popVariables(): void
	{
		array_pop(self::$variablesStack);
	}

	/**
	 * Returns view factory.
	 *
	 * @return  Factory
	 */
	private static function getViewFactory(): Factory
	{
		return Theme::getService(Factory::class);
	}

	/**
	 * Returns layout service.
	 *
	 * @return  Layout
	 */
	private static function getLayoutService(): Layout
	{
		return Theme::getService(Layout::class);
	}

	/**
	 * Gets all view variables from stack.
	 *
	 * @return  array<string, mixed>
	 */
	public static function getData(): array
	{
		if (count(self::$variablesStack) === 0) {
			throw new \LogicException('There is no data in variables stack.');
		}

		return self::$variablesStack[count(self::$variablesStack) - 1];
	}

	/**
	 * Gets view data value.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	public static function get(string $key, $default = null)
	{
		return self::getData()[$key] ?? $default;
	}

	/**
	 * Echoes raw view data.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @return void
	 */
	public static function raw(string $key, $default = null): void
	{
		echo self::get($key, $default);
	}

	/**
	 * Echoes escaped value.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @param  string $type    Escape function type.
	 * @return void
	 */
	public static function esc(string $key, $default = null, string $type = ''): void
	{
		$value = (string)self::get($key, $default);

		$availableTypes = [
			'html',
			'attr',
			'url',
		];

		if (!in_array($type, $availableTypes, true)) {
			$type = 'html';
		}

		$func = "esc_{$type}";

		if (!is_callable($func)) {
			return;
		}

		// phpcs:ignore NeutronStandard.Functions.DisallowCallUserFunc.CallUserFunc
		echo call_user_func($func, $value);
	}

	/**
	 * Echoes escaped url.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @return void
	 */
	public static function url(string $key, $default = null): void
	{
		self::esc($key, $default, 'url');
	}

	/**
	 * Echoes escaped attribute value.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @return void
	 */
	public static function attr(string $key, $default = null): void
	{
		self::esc($key, $default, 'attr');
	}

	/**
	 * Sets layout name.
	 *
	 * @param string $layout Layout name.
	 * @return void
	 */
	public static function layout(string $layout): void
	{
		self::getLayoutService()->setLayout($layout);
	}

	/**
	 * Displays partial view.
	 *
	 * @param string               $name Partial view name.
	 * @param array<string, mixed> $data Partial view data.
	 * @return void
	 */
	public static function partial(string $name, array $data = []): void
	{
		self::print("partials.{$name}", $data);
	}

	/**
	 * Creates and displays new view.
	 *
	 * @param string               $name Template name.
	 * @param array<string, mixed> $data Variables passed to template file.
	 * @return void
	 */
	public static function print(string $name, array $data = []): void
	{
		self::getViewFactory()->get($name, $data)->render(true);
	}
}
