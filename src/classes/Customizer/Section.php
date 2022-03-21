<?php

declare(strict_types=1);

namespace DoWStarterTheme\Customizer;

/**
 * Customizer Section class
 */
class Section extends \Kirki\Section
{
	/**
	 * Parent Panel of the Section.
	 *
	 * @var  Panel|null
	 */
	protected ?Panel $panel = null;

	/**
	 * Section constructor.
	 *
	 * @param  string       $id   ID of the Section.
	 * @param  array<mixed> $args Configuration of the Section, compatible with Kirki.
	 */
	public function __construct(string $id, array $args)
	{
		parent::__construct($id);

		$this->args = $args;
	}

	/**
	 * Sets the panel to which section belongs to.
	 *
	 * @param  Panel $panel Parent Panel of the Section.
	 * @return void
	 */
	public function setPanel(Panel $panel): void
	{
		$this->args['panel'] = $panel->getId();
	}

	/**
	 * Returns ID of the Section.
	 *
	 * @return  string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Indicates whether section with given ID is registered or not.
	 *
	 * @return  bool
	 */
	public function isRegistered(): bool
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		global $wp_customize;

		return in_array($this->id, array_keys($wp_customize->sections()), true);
		// phpcs:enable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	}

	/**
	 * Registers section in the customizer.
	 *
	 * @return  void
	 */
	public function register(): void
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		global $wp_customize;

		$this->add_section($wp_customize);
		// phpcs:enable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	}
}
