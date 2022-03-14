<?php

declare(strict_types=1);

namespace DoWStarterTheme\Customizer\Panels;

use DoWStarterTheme\Customizer\Abstracts\Panel;

/**
 * Customizer example panel class
 */
final class ExamplePanel extends Panel
{
	/**
	 * Returns priority of the panel.
	 *
	 * @return  int
	 */
	public function getPriority(): int
	{
		return 100;
	}
}
