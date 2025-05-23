<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Traits;

use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * Sluggable trait
 */
trait Sluggable
{
    use WithStaticInstanceData;

    protected static string $suffix = '';

    /**
     * Returns post type slug.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        if (! (bool)self::getData('slug')) {
            $name = static::class;

            if (strlen(static::$suffix) > 0) {
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

        /**
         * @var string
         */
        return self::getData('slug');
    }
}
