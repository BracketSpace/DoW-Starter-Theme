<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Traits;

/**
 * Adds static data storage.
 */
trait WithStaticInstanceData
{
    /**
     * Object data
     *
     * @var array<class-string, array<string, mixed>>
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
        if (property_exists(static::class, $key) && isset(static::$$key)) {
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
}
