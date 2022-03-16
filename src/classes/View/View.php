<?php
/**
 * phpcs:disable NeutronStandard.MagicMethods.RiskyMagicMethod.RiskyMagicMethod
 */

declare(strict_types=1);

namespace DoWStarterTheme\View;

use DoWStarterTheme\Core\Theme;
use DoWStarterTheme\Helpers\View as ViewHelper;

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
	 * @param array<string, mixed> $data     Data.
	 * @param bool                 $override Whether to override the entire data array (default).
	 * @return static
	 */
	public function with(array $data, bool $override = true): View
	{
		$this->data = $override ? $data : array_merge($this->data, $data);

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
	 * Renders a view.
	 *
	 * @param  bool $echo Whether to echo the output.
	 * @return string Rendered output.
	 */
	public function render(bool $echo = false): string
	{
		ViewHelper::pushVariables($this->data);

		ob_start();
		include $this->file;
		$output = ob_get_clean();

		ViewHelper::popVariables();

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
