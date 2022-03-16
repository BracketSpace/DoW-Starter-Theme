<?php

declare(strict_types=1);

namespace DoWStarterTheme\Core;

use DoWStarterTheme\View\Factory;

/**
 * Layout class
 */
class Layout
{
	/**
	 * Template name
	 *
	 * @var string
	 */
	private $template;

	/**
	 * Template name
	 *
	 * @var string
	 */
	private $layout = 'index';

	/**
	 * Sets template name.
	 *
	 * @param string $template Template name.
	 * @return void
	 */
	public function setTemplate(string $template): void
	{
		$this->template = $template;
	}

	/**
	 * Sets layout name.
	 *
	 * @param string $layout Layout name.
	 * @return void
	 */
	public function setLayout(string $layout): void
	{
		$this->layout = $layout;
	}

	/**
	 * Loads and displays proper layout.
	 *
	 * @return void
	 */
	public function get(): void
	{
		$viewFactory = Theme::getService(Factory::class);

		$content = $viewFactory->get($this->template)->render();

		$viewFactory->get("layouts.{$this->layout}")
			->with(
				[
					'content' => $content,
				],
				false
			)->render(true);
	}
}
