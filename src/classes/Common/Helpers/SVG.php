<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Helpers;

use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;

/**
 * SVG helper class
 */
class SVG
{
    /**
     * Class constructor.
     *
     * @param Filesystem $fs Filesystem instance.
     */
    public function __construct(
        private Filesystem $fs,
    ) {
    }

    /**
     * Returns SVG file content
     *
     * @param string $filename SVG filename
     * @return string|null
     */
    public function get(string $filename): ?string
    {
        if (substr($filename, -4) !== '.svg') {
            $filename .= '.svg';
        }

        $path = "assets/dist/images/{$filename}";

        if ($this->fs->exists($path)) {
            $content = $this->fs->get_contents($path);

            if (is_string($content)) {
                return $this->removeDoctype($content);
            }
        }

        return null;
    }

    /**
     * Gets SVG file from attachment
     *
     * @param int $id Attachment ID.
     * @return string|null
     */
    public function getAttachment(int $id): ?string
    {
        $filepath = get_attached_file($id);

        if (! is_string($filepath) || ! file_exists($filepath)) {
            return null;
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        $content = file_get_contents($filepath);

        if (! is_string($content)) {
            return null;
        }

        return $this->removeDoctype($content);
    }

    /**
     * Echoes the SVG file content.
     *
     * @param string $filename SVG filename.
     * @return void
     */
    public function print(string $filename): void
    {
        echo $this->get($filename);
    }

    /**
     * Echoes the SVG file from attachment
     *
     * @param int $id Attachment ID.
     * @return void
     */
    public function printAttachment(int $id): void
    {
        echo $this->getAttachment($id);
    }

    /**
     * Removes doctype from SVG string.
     *
     * @param string $svg SVG content.
     * @return string
     */
    public function removeDoctype(string $svg): string
    {
        return trim((string)preg_replace('/\<(\?xml|(\!DOCTYPE[^\>\[]+(\[[^\]]+)?))+[^>]+\>/', '', $svg));
    }
}
