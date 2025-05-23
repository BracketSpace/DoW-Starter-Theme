<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Helpers;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Contracts\WithContainerSetter;
use DoWStarterTheme\Common\Traits\HasContainerSetter;
use DoWStarterTheme\Deps\DI\Container;

/**
 * CSSGenerator class
 *
 * @phpstan-type Props array<string, mixed>
 */
class CSSGenerator implements WithContainerSetter
{
    use HasContainerSetter;

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

        return implode("\n\n", array_filter($blocks, static fn($value) => strlen($value) > 0));
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
            min($this->getConfig()->get('general.max-responsive-spacing'), round($value / 2)),
            $this->getConfig()->get('general.min-responsive-spacing')
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
    private function getMediaQueries(string $selector, array $props): string
    {
        $propsSm = [];
        $propsMd = [];

        foreach ($props as $prop => $value) {
            if ($value <= $this->getConfig()->get('general.min-responsive-spacing')) {
                continue;
            }

            $propsSm[$prop] = $this->getSmValue((int)$value);
            $propsMd[$prop] = $this->getMdValue((int)$value, $propsSm[$prop]);
        }

        $blocks = array_filter(
            [
                $this->getMediaQuery('md', $selector, $propsMd),
                $this->getMediaQuery('sm', $selector, $propsSm),
            ],
            static fn($value) => strlen($value) > 0
        );

        if (count($blocks) > 0) {
            return implode("\n\n", $blocks);
        }

        return '';
    }

    /**
     * Get CSS media queries
     *
     * @param  string $breakpoint CSS Breakpoint.
     * @param  string $selector   CSS selector.
     * @param  Props  $props      CSS Properties.
     * @return string
     */
    private function getMediaQuery(string $breakpoint, string $selector, array $props): string
    {
        $breakpointValue = $this->getConfig()->get("general.breakpoints.{$breakpoint}");

        if (! is_numeric($breakpointValue) || count($props) === 0) {
            return '';
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
     * Gets the config instance.
     *
     * @return Config
     */
    private function getConfig(): Config
    {
        if (! self::$container instanceof Container) {
            throw new \LogicException('Container is not available yet.');
        }

        return self::$container->get(Config::class);
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
