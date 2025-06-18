<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Traits;

use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Namable trait
 */
trait Namable
{
    use Sluggable;
    use WithStaticInstanceData;

    protected static string $name;

    /**
     * Returns post type name.
     *
     * @return string
     */
    public static function getName(): string
    {
        if (! (bool)self::getData('name')) {
            self::setData('name', Str::singular(Str::title(str_replace('-', ' ', static::getSlug()))));
        }

        return self::getData('name');
    }
}
