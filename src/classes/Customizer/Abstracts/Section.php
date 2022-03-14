<?php

declare(strict_types=1);

namespace DoWStarterTheme\Customizer\Abstracts;

use Kirki;
use DoWStarterTheme\Helpers\Title;
use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Customizer section class
 */
abstract class Section
{
	/**
	 * Panel of thr section.
	 *
	 * @var Panel $panel
	 */
	protected Panel $panel;

	/**
	 * Priority of the section.
	 *
	 * @var int $priority
	 */
	protected int $priority = 10;

	/**
	 * Constructor of the section.
	 *
	 * @param  Panel $panel Panel to which section belongs.
	 */
	public function __construct(Panel $panel)
	{
		$this->panel = $panel;
	}

	/**
	 * Returns ID of the section.
	 *
	 * Default value is based on section class name.
	 *
	 * @return  string
	 */
	public function getId(): string
	{
		return Str::kebab(static::class);
	}

	/**
	 * Returns name of the section.
	 *
	 * Default value is based on section class name.
	 *
	 * @return  string
	 */
	public function getName(): string
	{
		return Title::fromClass(static::class);
	}

	/**
	 * Returns description of the section.
	 *
	 * @return  string
	 */
	public function getDescription(): string
	{
		return '';
	}

	/**
	 * Registers section in the customizer.
	 *
	 * @return  void
	 */
	public function register(): void
	{
		new Kirki\Section(
			$this->getId(),
			[
				'panel' => $this->panel->getId(),
				'priority' => $this->priority,
				'title' => $this->getName(),
				'description' => $this->getDescription(),
			]
		);
	}
}
