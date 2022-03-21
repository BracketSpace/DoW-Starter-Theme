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
	protected string $id;

	/**
	 * Section of thr field.
	 *
	 * @var Section $section
	 */
	protected Section $section;

	/**
	 * Config of the field.
	 *
	 * @var array<string, mixed> $config
	 */
	protected array $config;

	/**
	 * Customizer class of field.
	 *
	 * @var string $fieldsClass
	 */
	protected string $fieldClass;

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

		$this->fieldClass = $this->convertTypeToClass($this->config['type']);
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
	private function convertTypeToClass(string $type): string
	{
		$class = sprintf('Kirki\\Field\\%s', Str::of($type)->title()->snake());

		if (!class_exists($class)) {
			throw new \InvalidArgumentException("Type \"{$type}\" of Kirki Field does not exists.");
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
	public function register(): void
	{
		if (!class_exists($this->fieldClass)) {
			throw new \InvalidArgumentException(
				sprintf('Class "%s" does not exists.', $this->fieldClass)
			);
		}

		if (!is_subclass_of($this->fieldClass, \Kirki\Field::class)) {
			throw new \InvalidArgumentException(
				sprintf('Class "%s" is not subclass of "%s".', $this->fieldClass, \Kirki\Field::class)
			);
		}

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

		// Remove default Kirki actions from constructor, because of they high
		// priority (higher than our customizer registrar).
		remove_action('customize_register', [$field, 'register_control_type']);
		remove_action('customize_register', [$field, 'add_setting']);
		remove_action('customize_register', [$field, 'add_control']);

		// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
		global $wp_customize;

		// Execute manually Kirki actions.
		$field->register_control_type($wp_customize);
		$field->add_setting($wp_customize);
		$field->add_control($wp_customize);
		// phpcs:enable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
	}
}
