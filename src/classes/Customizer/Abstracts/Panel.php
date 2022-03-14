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
	 * Priority of the panel.
	 *
	 * @var int $priority
	 */
	protected int $priority = 10;

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
				'priority' => $this->priority,
				'title' => $this->getName(),
				'description' => $this->getDescription(),
			]
		);
	}
}
