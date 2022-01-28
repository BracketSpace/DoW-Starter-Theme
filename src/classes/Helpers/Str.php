<?php

declare(strict_types=1);

namespace DoWStarterTheme\Helpers;

/**
 * Text helper class
 */
class Str
{
    /**
     * Converts given string to start case.
     *
     * @param  string $string Input string in kebab-case camelCase or snake_case format.
     * @return string         Humanized string.
     */
    public static function startCase(string $string): string
    {
        $string = (string)preg_replace('/(?<!\ )[A-Z]/', ' $0', $string);
        $string = (string)preg_replace('/[\-\_]{1}/', ' ', $string);

        return ucwords($string);
    }

    /**
     * Converts given string to kebab-case format.
     *
     * @param  string $string String to convert.
     * @return string
     */
    public static function kebabCase(string $string): string
    {
        return _wp_to_kebab_case($string);
    }
}
