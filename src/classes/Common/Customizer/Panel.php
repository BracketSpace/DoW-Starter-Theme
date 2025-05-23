<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Customizer;

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
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        global $wp_customize;

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        return in_array($this->id, array_keys($wp_customize->panels()), true);
    }

    /**
     * Registers panel in the customizer.
     *
     * @return  void
     */
    private function register(): void
    {
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        global $wp_customize;

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        $this->add_panel($wp_customize);
    }
}
