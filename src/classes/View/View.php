<?php
/**
 * phpcs:disable NeutronStandard.MagicMethods.RiskyMagicMethod.RiskyMagicMethod
 */

declare(strict_types=1);

namespace DoWStarterTheme\View;

use DoWStarterTheme\Core\Theme;
use DoWStarterTheme\Core\Layout;

/**
 * View class
 */
class View
{
	/**
	 * Template file.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Template variables.
	 *
	 * @var array<string, mixed>
	 */
	protected $data = [];

	/**
	 * Creates new instance and displays the view.
	 *
	 * @param string               $name Template name.
	 * @param array<string, mixed> $data Variables passed to template file.
	 * @return void
	 */
	public static function print(string $name, array $data = []): void
	{
		$factory = Theme::getService(Factory::class);
		$factory->get($name, $data)->render(true);
	}

	/**
	 * Constructor.
	 *
	 * @param string               $name View name.
	 * @param array<string, mixed> $data Optional array of vars passed to view file.
	 */
	public function __construct(string $name, array $data = [])
	{
		$file = Theme::getService(Finder::class)->find($name);

		if ($file === null) {
			throw new \Exception(
				/* translators: %s is a view name. */
				sprintf(__('View: "%s" does not exist.', 'dow-starter-theme'), $name)
			);
		}

		$this->file = $file;
		$this->data = $data;
	}

	/**
	 * Sets view data.
	 *
	 * @param array<string, mixed> $data Data.
	 * @return static
	 */
	public function with(array $data): View
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Returns data array.
	 *
	 * @return array<string, mixed> View data.
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Gets view data value.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	protected function get(string $key, $default = null)
	{
		return $this->data[$key] ?? $default;
	}

	/**
	 * Echoes raw view data.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @return void
	 */
	protected function raw(string $key, $default = null): void
	{
		echo $this->get($key, $default);
	}

	/**
	 * Echoes escaped value.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @param  string $type    Escape function type.
	 * @return void
	 */
	protected function esc(string $key, $default = null, string $type = ''): void
	{
		$value = (string)$this->get($key, $default);

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
	protected function url(string $key, $default = null): void
	{
		$this->esc($key, $default, 'url');
	}

	/**
	 * Echoes escaped attribute value.
	 *
	 * @param  string $key     Data key.
	 * @param  mixed  $default Default value.
	 * @return void
	 */
	protected function attr(string $key, $default = null): void
	{
		$this->esc($key, $default, 'attr');
	}

	/**
	 * Sets layout name.
	 *
	 * @param string $layout Layout name.
	 * @return void
	 */
	protected function layout(string $layout): void
	{
		Theme::getService(Layout::class)->setLayout($layout);
	}

	/**
	 * Displays partial view.
	 *
	 * @param string               $name Partial view name.
	 * @param array<string, mixed> $data Partial view data.
	 * @return void
	 */
	protected function partial(string $name, array $data = []): void
	{
		static::print("partials.{$name}", $data);
	}

	/**
	 * Renders a view.
	 *
	 * @param  bool $echo Whether to echo the output.
	 * @return string Rendered output.
	 */
	public function render(bool $echo = false): string
	{
		ob_start();

		include $this->file;

		$output = ob_get_clean();

		if ($echo) {
			echo $output;
		}

		return (string)$output;
	}

	/**
	 * Returns loaded template as string.
	 *
	 * @return string Template string.
	 */
	public function __toString(): string
	{
		return $this->render();
	}
}
