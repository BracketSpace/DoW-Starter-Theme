<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Customizer;

/**
 * Customizer Section class
 */
final class Section extends \Kirki\Section
{
    /**
     * Section constructor.
     *
     * @param  string       $id    ID of the Section.
     * @param  array<mixed> $args  Configuration of the Section, compatible with Kirki.
     * @param  Panel|null   $panel Parent Panel of the Section.
     */
    public function __construct(string $id, array $args, ?Panel $panel = null)
    {
        parent::__construct($id);

        $this->args = $args;

        if ($panel !== null) {
            $this->args['panel'] = $panel->getId();
        }

        if ($this->isRegistered()) {
            return;
        }

        $this->register();
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
    private function isRegistered(): bool
    {
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        global $wp_customize;

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        return in_array($this->id, array_keys($wp_customize->sections()), true);
    }

    /**
     * Registers section in the customizer.
     *
     * @return  void
     */
    private function register(): void
    {
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        global $wp_customize;

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.NotCamelCaps
        $this->add_section($wp_customize);
    }
}
