<?php

declare(strict_types=1);

namespace DoWStarterTheme\Traits;

use DoWStarterTheme\Deps\Illuminate\Support\Str;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;

/**
 * Labelable trait
 */
trait Labelable
{
	use HookTrait;

	/**
	 * Object class name suffix
	 */
	protected static string $suffix;

	/**
	 * Object slug
	 */
	protected static string $slug;

	/**
	 * Object name
	 */
	protected static string $name;

	/**
	 * Object name in plural form
	 */
	protected static string $pluralName;

	/**
	 * Object labels
	 *
	 * @var array<string, string>
	 */
	protected static array $labels;

	/**
	 * Object data
	 *
	 * @var array<string, mixed>
	 */
	private static array $data = [];

	/**
	 * Gets post type data for given key.
	 *
	 * @param  string $key Key.
	 * @return mixed Value for given key.
	 */
	protected static function getData(string $key)
	{
		if (isset(static::$$key)) {
			return static::$$key;
		}

		return self::$data[static::class][$key] ?? false;
	}

	/**
	 * Sets post type data for given key.
	 *
	 * @param string $key   Key.
	 * @param mixed  $value Value for given key.
	 * @return void
	 */
	protected static function setData(string $key, $value)
	{
		self::$data[static::class][$key] = $value;
	}

	/**
	 * Returns post type slug.
	 *
	 * @return string
	 */
	public static function getSlug(): string
	{
		if (!(bool)self::getData('slug')) {
			$name = static::class;

			if (isset(static::$suffix)) {
				$suffix = static::$suffix;
				$name = (string)preg_replace(
					"/{$suffix}$/",
					'',
					static::class
				);
			}

			$i = strrpos($name, '\\');
			self::setData('slug', Str::kebab($i !== false ? substr($name, $i + 1) : $name));
		}

		return self::getData('slug');
	}

	/**
	 * Returns post type name.
	 *
	 * @return string
	 */
	public static function getName(): string
	{
		if (!(bool)self::getData('name')) {
			self::setData('name', Str::singular(Str::title(str_replace('-', ' ', static::getSlug()))));
		}

		return self::getData('name');
	}

	/**
	 * Returns post type name in plural form.
	 *
	 * @return string
	 */
	public static function getPluralName(): string
	{
		if (!(bool)self::getData('pluralName')) {
			self::setData('pluralName', Str::plural(static::getName()));
		}

		return self::getData('pluralName');
	}

	/**
	 * Returns prepared post type labels.
	 *
	 * @return array<string, string>
	 */
	protected static function getLabels(): array
	{
		$name = static::getName();
		$pluralName = static::getPluralName();

		$search = [
			'{singular}',
			'{singularLower}',
			'{plural}',
			'{pluralLower}',
		];

		$replace = [
			$name,
			Str::lower($name),
			$pluralName,
			Str::lower($pluralName),
		];

		$labels = array_map(
			static fn ($item) => str_replace($search, $replace, $item),
			static::getLabelTemplates()
		);

		if (isset(static::$labels)) {
			return array_merge($labels, static::$labels);
		}

		return $labels;
	}

	/**
	 * Returns a list of label templates.
	 *
	 * @return array<string, string>
	 */
	abstract protected static function getLabelTemplates(): array;
}
