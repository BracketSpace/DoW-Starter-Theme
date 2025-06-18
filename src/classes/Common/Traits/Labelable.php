<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Traits;

use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Labelable trait
 */
trait Labelable
{
    use Namable;

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
     * Returns post type name in plural form.
     *
     * @return string
     */
    public static function getPluralName(): string
    {
        if (! (bool)self::getData('pluralName')) {
            self::setData('pluralName', Str::plural(static::getName()));
        }

        return self::getData('pluralName');
    }

    /**
     * Returns prepared post type labels.
     *
     * @return array<string, string>
     */
    public static function getLabels(): array
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
