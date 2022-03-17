<?php

declare(strict_types=1);

namespace DoWStarterTheme\Integration;

use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Factories\Filesystem;

/**
 * ACFBlockCreator integration class
 */
class ACFBlockCreator
{
	use HookTrait;

	/**
	 * Filters block template content.
	 *
	 * @filter micropackage/acf-block-creator/block/template
	 *
	 * @return string Block template content.
	 */
	public function filterBlockTemplate(): string
	{
		return (string)Filesystem::get()->get_contents('src/templates/block.php');
	}

	/**
	 * Filters block markup.
	 *
	 * @filter micropackage/acf-block-creator/block/markup
	 *
	 * @param  string $markup Block markup.
	 * @return string         Modified block markup.
	 */
	public function filterBlockMarkup(string $markup): string
	{
		return str_replace(
			['( ', ' )'],
			['(', ')'],
			$markup
		);
	}
}
