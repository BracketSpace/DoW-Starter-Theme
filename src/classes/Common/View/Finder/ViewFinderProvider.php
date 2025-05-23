<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View\Finder;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Contracts\ServiceProvider;
use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;

/**
 * View finder service provider.
 */
class ViewFinderProvider implements ServiceProvider
{
    /**
     * Defines application services.
     *
     * @return array<string, mixed>
     */
    public function provides(): array
    {
        return [
            ViewFinder::class => static function (Config $config, Filesystem $fs) {
                /** @var string */
                $location = $config->get('view.location', 'views');

                if (! $fs->exists($location)) {
                    throw new \Exception(
                        /* translators: %s is a directory path. */
                        sprintf(__('Views directory: "%s" does not exist.', 'dow-starter-theme'), $location)
                    );
                }

                /** @var string */
                $ext = $config->get('view.extension', '.php');

                return new ViewFinder(
                    new Filesystem($fs->path($location)),
                    $ext
                );
            },
        ];
    }
}
