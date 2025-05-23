<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Integrations;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;

/**
 * ACFBlockCreator integration class
 */
class ACFBlockCreator implements Hookable
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
     * Filters block template content.
     *
     * @filter micropackage/acf-block-creator/block/template
     *
     * @return string Block template content.
     */
    public function filterBlockTemplate(): string
    {
        $content = $this->fs->get_contents('src/templates/block.php');

        return is_string($content) ? $content : '';
    }

    /**
     * Filters block markup.
     *
     * @filter micropackage/acf-block-creator/block/markup
     *
     * @param  string $markup Block markup.
     * @return string         Modified block markup.
     */
    public function filterBlockMarkup(string $markup): string
    {
        return (string)preg_replace(
            '/if \((\$[^)]+)\)/',
            'if (is_array($1))',
            str_replace(
                ['( ', ' )'],
                ['(', ')'],
                $markup
            )
        );
    }
}
