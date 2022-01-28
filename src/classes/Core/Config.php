<?php

declare(strict_types=1);

namespace DoWStarterTheme\Core;

use DoWStarterTheme\Factories\Filesystem;
use Noodlehaus\Config as BaseConfig;
use Noodlehaus\Parser;

/**
 * Config class
 */
class Config
{
    /**
     * Supported parsers,
     *
     * @var array<string>
     */
    protected static $supportedParsers = [
        Parser\Ini::class,
        Parser\Json::class,
        Parser\Php::class,
        Parser\Properties::class,
        Parser\Serialize::class,
        Parser\Xml::class,
        Parser\Yaml::class,
    ];

    /**
     * Array of Config instances.
     *
     * @var array<string, \Noodlehaus\Config|false>
     */
    protected static $config = [];

    /**
     * Gets config array or single config value.
     *
     * @param  string $key     Config key.
     * @param  mixed  $default Default value.
     * @return mixed             Config value.
     */
    public static function get(string $key, $default = null)
    {
        $parts = explode('.', $key, 2);
        $namespace = $parts[0];
        $key = $parts[1] ?? false;

        if (!array_key_exists($namespace, static::$config)) {
            $fs = Filesystem::get('config');
            $extensions = static::getSupportedExtensions();
            $filenames = [];

            foreach ($extensions as $ext) {
                $filename = "{$namespace}.{$ext}";

                if (!$fs->exists($filename)) {
                    continue;
                }

                $filenames[] = $fs->path($filename);
            }

            static::$config[$namespace] = count($filenames) > 0 ? new BaseConfig($filenames) : false;
        }

        if (!static::$config[$namespace] instanceof BaseConfig) {
            return $default;
        }

        if (is_string($key)) {
            return static::$config[$namespace]->get($key, $default);
        }

        return static::$config[$namespace]->all();
    }

    /**
     * Gets supported file extensions.
     *
     * @return array<string> Supported extensions.
     */
    protected static function getSupportedExtensions(): array
    {
        /**
         * @var array<string>
         */
        $extensions = [];

        foreach (static::$supportedParsers as $parser) {
            $extensions = array_merge($extensions, $parser::getSupportedExtensions());
        }

        return $extensions;
    }
}
