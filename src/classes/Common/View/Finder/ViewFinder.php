<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View\Finder;

use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;

/**
 * View Finder class
 */
class ViewFinder
{
    /**
     * Sets up the view path.
     *
     * @param Filesystem $fs        Filesystem instance.
     * @param string     $extension Default extension of views.
     */
    public function __construct(
        private Filesystem $fs,
        private string $extension
    ) {
    }

    /**
     * Finds template file.
     *
     * @param  string $name Template name.
     * @return string|null  Template file path or null if not found.
     */
    public function find(string $name): ?string
    {
        $pathBase = str_replace('.', '/', $name);
        $path = "{$pathBase}{$this->extension}";

        if (! $this->fs->exists($path)) {
            return null;
        }

        return $this->fs->path($path);
    }
}
