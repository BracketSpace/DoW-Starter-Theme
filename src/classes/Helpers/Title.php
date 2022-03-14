<?php

declare(strict_types=1);

namespace DoWStarterTheme\Helpers;

use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Title helper class
 */
class Title
{
	/**
	 * Converts class name to human-readable, formatted title.
	 *
	 * @param   string $class Class name.
	 *
	 * @return  string
	 */
	public static function fromClass(string $class): string
	{
		return Str::headline(class_basename($class));
	}
}
