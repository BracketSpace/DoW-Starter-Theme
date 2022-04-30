<?php

declare(strict_types=1);

namespace DoWStarterTheme\Customizer;

use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Customizer Field class
 */
final class Field
{
	/**
	 * Returns value of the field with given name.
	 *
	 * @param   string $id      ID of the field.
	 * @param   mixed  $default Default value.
	 *
	 * @return  mixed
	 */
	public static function value(string $id, $default = null)
	{
		return get_theme_mod($id, $default);
	}

	/**
	 * Unique ID of the field.
	 *
	 * @var string $id
	 */
	private string $id;

	/**
	 * Section of thr field.
	 *
	 * @var Section $section
	 */
	private Section $section;

	/**
	 * Config of the field.
	 *
	 * @var array<string, mixed> $config
	 */
	private array $config;

	/**
	 * Customizer class of field.
	 *
	 * @var string $fieldsClass
	 */
	private string $fieldClass;

	/**
	 * Constructor of the field.
	 *
	 * @param  string               $id      ID of the field.
	 * @param  Section              $section Section to which field belongs.
	 * @param  array<string, mixed> $config  Field configuration.
	 */
	public function __construct(string $id, Section $section, array $config = [])
	{
		$this->id = $id;
		$this->section = $section;
		$this->config = $config;

		$this->fieldClass = $this->getClassFromType($this->config['type']);

		$this->register();
	}

	/**
	 * Converts field type to Kirki class name.
	 *
	 * @example checkbox -> Kirki\Field\Checkbox::class
	 * @example radio_image -> Kirki\Field\Radio_Image::class
	 *
	 * @param   string $type Type to convert.
	 *
	 * @return  string
	 */
	private function getClassFromType(string $type): string
	{
		$class = sprintf('Kirki\\Field\\%s', Str::of($type)->snake()->title());

		if (!class_exists($class)) {
			throw new \InvalidArgumentException("Type \"{$type}\" of Kirki Field does not exists.");
		}

		if (!is_subclass_of($class, \Kirki\Field::class)) {
			throw new \InvalidArgumentException(
				sprintf('Class "%s" is not subclass of "%s".', $class, \Kirki\Field::class)
			);
		}

		return $class;
	}

	/**
	 * Returns unique ID of field.
	 *
	 * @return  string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Registers field in the customizer.
	 *
	 * @return  void
	 */
	private function register(): void
	{
		$class = $this->fieldClass;

		// phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
		$field = new $class(
			array_merge(
				$this->config,
				[
					'settings' => $this->getId(),
					'section' => $this->section->getId(),
				]
			)
		);

		// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		global $wp_customize;

		/**
		 * Kirki by default registers fields in `customize_register` action with
		 * priority 10. Our Customizer feature uses the same action, but with
		 * priority 100, to be sure that all default Customizer options are
		 * registered. Because of that we need to manually execute Kirki
		 * methods which registers field.
		 *
		 * @see \Kirki\Field@__constructor
		 */
		$field->register_control_type($wp_customize);
		$field->add_setting($wp_customize);
		$field->add_control($wp_customize);
		// phpcs:enable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	}
}
