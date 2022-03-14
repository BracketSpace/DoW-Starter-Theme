<?php

declare(strict_types=1);

namespace DoWStarterTheme\Customizer\Abstracts;

use Kirki;
use DoWStarterTheme\Helpers\Title;
use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Customizer panel class
 */
abstract class Panel
{
	/**
	 * Returns ID of the panel.
	 *
	 * Default value is based on panel class name.
	 *
	 * @return  string
	 */
	public function getId(): string
	{
		return Str::kebab(static::class);
	}

	/**
	 * Returns priority of the panel.
	 *
	 * @return  int
	 */
	public function getPriority(): int
	{
		return 10;
	}

	/**
	 * Returns name of the panel.
	 *
	 * Default value is based on panel class name.
	 *
	 * @return  string
	 */
	public function getName(): string
	{
		return Title::fromClass(static::class);
	}

	/**
	 * Returns description of the panel.
	 *
	 * @return  string
	 */
	public function getDescription(): string
	{
		return '';
	}

	/**
	 * Registers panel in the customizer.
	 *
	 * @return  void
	 */
	public function register(): void
	{
		new Kirki\Panel(
			$this->getId(),
			[
				'priority' => $this->getPriority(),
				'title' => $this->getName(),
				'description' => $this->getDescription(),
			]
		);
	}
}
