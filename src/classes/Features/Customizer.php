<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use DoWStarterTheme\Customizer\Panel;
use DoWStarterTheme\Customizer\Section;
use DoWStarterTheme\Customizer\Field;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Core\Config;

/**
 * Customizer registrar class
 */
class Customizer
{
	use HookTrait;

	protected const TYPE_PANEL = 'panel';
	protected const TYPE_SECTION = 'section';

	/**
	 * Initializes customizer Panels, Sections and Fields.
	 *
	 * @action customize_register 100
	 *
	 * @return  void
	 */
	public function initialize(): void
	{
		$config = Config::get('customizer');

		if (!is_array($config)) {
			return;
		}

		foreach ($config as $key => $data) {
			['type' => $type, 'id' => $id] = $this->parseKey($key);

			if ($type === self::TYPE_PANEL) {
				$this->registerPanel($id, $data);
			} elseif ($type === self::TYPE_SECTION) {
				$this->registerSection($id, $data);
			}
		}
	}

	/**
	 * Parses Panel or Section configuration key.
	 *
	 * @param   string $key Panel or section configuration key.
	 * @return  array<string, string>
	 */
	protected function parseKey(string $key): array
	{
		$result = preg_match('/^(?P<type>.+?):(?P<id>.+)$/', $key, $matches);

		return !$result
			? ['type' => self::TYPE_SECTION, 'id' => $key]
			: ['type' => $matches['type'], 'id' => $matches['id']];
	}

	/**
	 * Registers Panel in Customizer.
	 *
	 * @param   string       $id   ID of the Panel.
	 * @param   array<mixed> $data Configuration of the Panel, compatible with Kirki.
	 * @return  void
	 */
	protected function registerPanel(string $id, array $data): void
	{
		$panel = new Panel($id, $data);

		if (!$panel->isRegistered()) {
			$panel->register();
		}

		foreach ($data['children'] as $key => $sectionData) {
			['id' => $sectionId] = $this->parseKey($key);

			$this->registerSection($sectionId, $sectionData, $panel);
		}
	}

	/**
	 * Registers Section in Customizer.
	 *
	 * @param   string       $id    ID of the Section.
	 * @param   array<mixed> $data  Configuration of the Section, compatible with Kirki.
	 * @param   Panel|null   $panel Parent Panel of the Section.
	 * @return  void
	 */
	protected function registerSection(string $id, array $data, ?Panel $panel = null): void
	{
		$section = new Section($id, $data);

		if ($panel) {
			$section->setPanel($panel);
		}

		if (!$section->isRegistered()) {
			$section->register();
		}

		foreach ($data['children'] as $fieldId => $fieldData) {
			$this->registerField($fieldId, $fieldData, $section);
		}
	}

	/**
	 * Registers Field in Customizer.
	 *
	 * @param   string       $id      ID of the Field.
	 * @param   array<mixed> $data    Configuration of the Field, compatible with Kirki.
	 * @param   Section|null $section Parent Section of the Field.
	 * @return  void
	 */
	protected function registerField(string $id, array $data, Section $section): void
	{
		$field = new Field($id, $section, $data);
		$field->register();
	}
}
