<?php
/**
 * phpcs:disable NeutronStandard.MagicMethods.RiskyMagicMethod.RiskyMagicMethod
 */

declare(strict_types=1);

namespace DoWStarterTheme\View;

use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * View Composer abstract class
 */
abstract class Composer
{
	/**
	 * List of views to receive data by this composer
	 *
	 * @var array<string>
	 */
	protected static $views;

	/**
	 * List of views served by this composer
	 *
	 * @return array<string>
	 */
	public static function getViews()
	{
		if (isset(static::$views)) {
			return static::$views;
		}

		$name = (string)preg_replace(
			'/Composer$/',
			'',
			static::class
		);

		return [
			implode(
				'.',
				array_map(
					[Str::class, 'kebab'],
					array_slice(explode('\\', $name), 3)
				)
			),
		];
	}

	/**
	 * Composes the view.
	 *
	 * @param View $view View instance.
	 * @return void
	 */
	public function compose(View $view): void
	{
		$viewData = $view->getData();

		$view->with(
			array_merge(
				$this->with(),
				$viewData,
				$this->override()
			)
		);
	}

	/**
	 * Returns an array of data for the view.
	 *
	 * @return array<string, mixed> Data.
	 */
	protected function with(): array
	{
		return [];
	}

	/**
	 * Returns an array of data which will override other data (set using `with` method or passed to the view as
	 * parameter).
	 *
	 * @return array<string, mixed> Data.
	 */
	protected function override(): array
	{
		return [];
	}
}
