<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use DoWStarterTheme\Customizer\Abstracts\Panel;
use DoWStarterTheme\Customizer\Abstracts\Section;
use DoWStarterTheme\Customizer\Field;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Core\Config;

/**
 * Customizer registrar class
 */
class Customizer
{
	use HookTrait;

	/**
	 * Initializes customizer sections, panels and fields.
	 *
	 * @action init
	 *
	 * @return  void
	 */
	public function initialize(): void
	{
		$config = Config::get('customizer');

		$this->registerPanels($config);
	}

	/**
	 * Registers customizer panels.
	 *
	 * @param   array<mixed> $panels List of panels.
	 *
	 * @return  void
	 */
	protected function registerPanels(array $panels): void
	{
		foreach ($panels as $panel => $sections) {
			$panel = $this->registerPanel($panel);

			$this->registerSections($panel, $sections);
		}
	}

	/**
	 * Registers customizer sections.
	 *
	 * @param   Panel        $panel    Panel in which sections should be registered.
	 * @param   array<mixed> $sections List of sections.
	 *
	 * @return  void
	 */
	protected function registerSections(Panel $panel, array $sections): void
	{
		foreach ($sections as $section => $fields) {
			$section = $this->registerSection($panel, $section);

			$this->registerFields($section, $fields);
		}
	}

	/**
	 * Registers customizer fields.
	 *
	 * @param   Section      $section Section in which fields should be registered.
	 * @param   array<mixed> $fields  List of fields.
	 *
	 * @return  void
	 */
	protected function registerFields(Section $section, array $fields): void
	{
		foreach ($fields as $id => $config) {
			$this->registerField($id, $section, $config);
		}
	}

	/**
	 * Registers single panel.
	 *
	 * @param   string $panel Name of the panel class.
	 * @return  Panel
	 * @throws  \InvalidArgumentException if the given class name does not exists
	 */
	protected function registerPanel(string $panel): Panel
	{
		if (!class_exists($panel)) {
			throw new \InvalidArgumentException(
				sprintf('Class "%s" does not exists.', $panel)
			);
		}

		if (!is_subclass_of($panel, Panel::class)) {
			throw new \InvalidArgumentException(
				sprintf('Class "%s" is not subclass of "%s".', $panel, Panel::class)
			);
		}

		// phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
		$panel = new $panel();
		$panel->register();

		return $panel;
	}

	/**
	 * Registers single section in given panel.
	 *
	 * @param   Panel  $panel   Instance of panel in which .
	 * @param   string $section Name of the section class.
	 * @return  Section
	 * @throws  \InvalidArgumentException if the given class name does not exists
	 */
	protected function registerSection(Panel $panel, string $section): Section
	{
		if (!class_exists($section)) {
			throw new \InvalidArgumentException(
				sprintf('Class "%s" does not exists.', $section)
			);
		}

		if (!is_subclass_of($section, Section::class)) {
			throw new \InvalidArgumentException(
				sprintf('Class "%s" is not subclass of "%s".', $section, Section::class)
			);
		}

		// phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
		$section = new $section($panel);
		$section->register();

		return $section;
	}

	/**
	 * Registers single field in given section.
	 *
	 * @param   string               $id      ID of the field.
	 * @param   Section              $section Instance of section.
	 * @param   array<string, mixed> $config  Config of the field.
	 * @return  Field
	 */
	protected function registerField(string $id, Section $section, array $config): Field
	{
		$field = new Field($id, $section, $config);
		$field->register();

		return $field;
	}
}