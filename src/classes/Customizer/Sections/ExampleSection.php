<?php

declare(strict_types=1);

namespace DoWStarterTheme\Customizer\Sections;

use DoWStarterTheme\Customizer\Abstracts\Section;

/**
 * Customizer example section class
 */
final class ExampleSection extends Section
{
	/**
	 * Returns priority of the section.
	 *
	 * @return  int
	 */
	public function getPriority(): int
	{
		return 100;
	}
}
