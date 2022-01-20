<?php

declare(strict_types=1);

namespace DoWStarterTheme\Requirements;

use Micropackage\Requirements\Abstracts\Checker;

/**
 * Assets checker class
 */
class AssetsChecker extends Checker
{
    /**
     * Checker name
     *
     * @var string
     */
    protected $name = 'assets';

    /**
     * Checks if assest are built.
     *
     * @param  mixed $enabled Whether the assets should be built.
     * @return void
     */
    public function check($enabled)
    {
        if ($enabled !== true) {
            return;
        }

        if (file_exists(get_stylesheet_directory() . '/assets/build/styles.css')) {
            return;
        }

        $this->add_error(
            __(
                'Assets build directory not found. You must run `yarn install & yarn build` from the theme directory.',
                'dow-starter-theme'
            )
        );
    }
}
