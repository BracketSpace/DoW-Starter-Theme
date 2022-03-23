<?php

declare(strict_types=1);

namespace DoWStarterTheme\Customizer;

/**
 * Customizer Panel class
 */
final class Panel extends \Kirki\Panel
{
	/**
	 * Panel constructor.
	 *
	 * @param  string       $id   ID of the Panel.
	 * @param  array<mixed> $args Configuration of the Panel, compatible with Kirki.
	 */
	public function __construct(string $id, array $args)
	{
		parent::__construct($id);

		$this->args = $args;

		if ($this->isRegistered()) {
			return;
		}

		$this->register();
	}

	/**
	 * Returns ID of the Panel.
	 *
	 * @return  string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Indicates whether panel with given ID is registered or not.
	 *
	 * @return  bool
	 */
	private function isRegistered(): bool
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		global $wp_customize;

		return in_array($this->id, array_keys($wp_customize->panels()), true);
		// phpcs:enable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	}

	/**
	 * Registers panel in the customizer.
	 *
	 * @return  void
	 */
	private function register(): void
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		global $wp_customize;

		$this->add_panel($wp_customize);
		// phpcs:enable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	}
}
