<?php

declare(strict_types=1);

namespace DoWStarterTheme\Helpers;

use DoWStarterTheme\Factories\Filesystem;

/**
 * SVG helper class
 */
class SVG
{
    /**
     * Returns SVG file content
     *
     * @param  string $filename File name.
     * @return string|null
     */
    public static function get($filename): ?string
    {
        if (substr($filename, -4) !== '.svg') {
            $filename .= '.svg';
        }

        $fs = Filesystem::get();
        $path = "assets/dist/images/{$filename}";

        if ($fs->exists($path)) {
            $content = $fs->get_contents($path);

            if (is_string($content)) {
                return static::removeDoctype($content);
            }
        }

        return null;
    }

    /**
     * Gets SVG file from attachment
     *
     * @param  int $id File ID.
     * @return string/void
     */
    public static function getAttachment(int $id): ?string
    {
        $filepath = get_attached_file($id);

        if (!is_string($filepath) || !file_exists($filepath)) {
            return null;
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $content = file_get_contents($filepath);

        if (! is_string($content)) {
            return null;
        }

        return static::removeDoctype($content);
    }

    /**
     * Echoes the SVG file content.
     *
     * @param  string $filename File name.
     * @return void
     */
    public static function print($filename)
    {
        echo static::get($filename);
    }

    /**
     * Echoes the SVG file from attachment
     *
     * @param  int $id File ID.
     * @return void
     */
    public static function printAttachment(int $id): void
    {
        echo static::getAttachment($id);
    }

    /**
     * Removes doctype from SVG string.
     *
     * @param  string $svg SVG markup.
     * @return string      SVG with removed doctype.
     */
    public static function removeDoctype(string $svg): string
    {
        return trim((string)preg_replace('/\<(\?xml|(\!DOCTYPE[^\>\[]+(\[[^\]]+)?))+[^>]+\>/', '', $svg));
    }
}
