<?php

declare(strict_types=1);

namespace DoWStarterTheme\Requirements;

use DoWStarterTheme\Deps\Micropackage\Requirements\Abstracts\Checker;
use DoWStarterTheme\Core\Config;
use DoWStarterTheme\Features\Customizer;
use Kirki\Compatibility\Kirki;

/**
 * Customizer checker class
 */
class CustomizerChecker extends Checker
{
	/**
	 * Checker name
	 *
	 * @var string
	 */
	protected $name = 'customizer';

	/**
	 * Checks if assest are built.
	 *
	 * @param  mixed $enabled Whether checker is enabled or not.
	 * @return void
	 */
	public function check($enabled)
	{
		if ($enabled !== true) {
			return;
		}

		if (!in_array(Customizer::class, Config::get('classes.general'), true)) {
			return;
		}

		if (class_exists(Kirki::class)) {
			return;
		}

		$this->add_error(
			__(
				'Kirki plugin is required in order to enable Customizer feature.',
				'dow-starter-theme'
			)
		);
	}
}
