<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Assets;

/**
 * Style asset class
 */
abstract class Style extends Asset
{
    /**
     * Class name suffix to be removed from name.
     */
    protected string $suffix = 'Style';

    /** @var string Stylesheet version (optional - file checksum used by default). */
    protected string $version;

    /** @var string Stylesheet media (possible values: `all` | `print` | `screen`). */
    protected string $media = 'all';

    /**
     * Returns a relative base path for asset.
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return 'build/css';
    }

    /**
     * Returns an asset file extension.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return 'css';
    }

    /**
     * Registers a stylesheet.
     *
     * @return void
     */
    public function register(): void
    {
        wp_register_style(
            $this->getHandle(),
            $this->getUrl(),
            $this->getDeps(),
            $this->version ?? md5($this->getContents()),
            $this->media
        );
    }

    /**
     * Enqueues a stylesheet.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_style($this->getHandle());
    }
}
