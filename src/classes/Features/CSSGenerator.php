<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use DoWStarterTheme\Core\Config;

/**
 * CSSGenerator class
 *
 * @phpstan-type Props array<string, mixed>
 */
class CSSGenerator
{
	/**
	 * Rem unit value in pixels
	 *
	 * @var int
	 */
	private static $remValue = 16;

	/**
	 * CSS Rules
	 *
	 * @var Props
	 */
	private $rules;

	/**
	 * Constructor
	 *
	 * @param Props $rules CSS rules array.
	 */
	public function __construct(array $rules = [])
	{
		$this->rules = $rules;
	}

	/**
	 * Return prepared CSS
	 *
	 * @return string Resulting CSS.
	 */
	public function getCss(): string
	{
		$blocks = [];

		foreach ($this->rules as $selector => $props) {
			$blocks[] = $this->getCodeBlock($selector, $props);
			$blocks[] = $this->getMediaQueries($selector, $props);
		}

		return implode("\n\n", array_filter($blocks));
	}

	/**
	 * Gets small value
	 *
	 * @param  int $value Initial value.
	 * @return int        Calculated value.
	 */
	private function getSmValue(int $value): int
	{
		return (int)max(
			min(Config::get('general.max-responsive-spacing'), round($value / 2)),
			Config::get('general.min-responsive-spacing')
		);
	}

	/**
	 * Gets medium value
	 *
	 * @param  int $value   Initial value.
	 * @param  int $valueSm Small value.
	 * @return int
	 */
	private function getMdValue(int $value, int $valueSm): int
	{
		return (int)round(($value - $valueSm) / 2) + $valueSm;
	}

	/**
	 * Gets CSS media queries
	 *
	 * @param  string $selector CSS selector.
	 * @param  Props  $props    CSS Properties.
	 * @return string
	 */
	private function getMediaQueries(string $selector, array $props): ?string
	{
		$propsSm = [];
		$propsMd = [];

		foreach ($props as $prop => $value) {
			if ($value <= Config::get('general.min-responsive-spacing')) {
				continue;
			}

			$propsSm[$prop] = $this->getSmValue((int)$value);
			$propsMd[$prop] = $this->getMdValue((int)$value, $propsSm[$prop]);
		}

		$blocks = array_filter(
			[
				$this->getMediaQuery('md', $selector, $propsMd),
				$this->getMediaQuery('sm', $selector, $propsSm),
			]
		);

		if (count($blocks) > 0) {
			return implode("\n\n", $blocks);
		}

		return null;
	}

	/**
	 * Get CSS media queries
	 *
	 * @param  string $breakpoint CSS Breakpoint.
	 * @param  string $selector   CSS selector.
	 * @param  Props  $props      CSS Properties.
	 * @return string
	 */
	private function getMediaQuery(string $breakpoint, string $selector, array $props): ?string
	{
		$breakpointValue = Config::get("general.breakpoints.{$breakpoint}");

		if (!is_numeric($breakpointValue) || count($props) === 0) {
			return null;
		}

		$parts = [
			sprintf(
				'@media (max-width: %s) {',
				self::emCalc((int)$breakpointValue)
			),
			$this->getCodeBlock($selector, $props, "\t"),
			'}',
		];

		return implode("\n", $parts);
	}

	/**
	 * Get CSS code block
	 *
	 * @param  string $selector CSS selector.
	 * @param  Props  $props    CSS Properties.
	 * @param  string $prepend  String to prepend to each line.
	 * @return string
	 */
	private function getCodeBlock(string $selector, array $props, ?string $prepend = null): string
	{
		$lines = [];

		$lines[] = "{$prepend}{$selector} {";

		foreach ($props as $prop => $value) {
			if (is_numeric($value)) {
				$value = self::remCalc((int)$value);
			}

			$lines[] = "$prepend\t{$prop}: {$value} !important;";
		}

		$lines[] = "{$prepend}}";

		return implode("\n", $lines);
	}

	/**
	 * Calculates rem value.
	 *
	 * @param  int $value Initial value in pixels.
	 * @return string
	 */
	public static function remCalc(int $value): string
	{
		return ($value / self::$remValue) . 'rem';
	}

	/**
	 * Calculates em value.
	 *
	 * @param  int $value Initial value in pixels.
	 * @return string
	 */
	public static function emCalc(int $value): string
	{
		return ($value / 16) . 'em';
	}
}
