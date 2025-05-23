<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Abstracts;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\Illuminate\Support\Str;
use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;

/**
 * @phpstan-type ScriptMeta array{dependencies: array<string>, version: string}
 */
abstract class Asset implements Hookable
{
    /**
     * Class name suffix to be removed from name.
     */
    protected string $suffix = 'Asset';

    /**
     * Asset name (filename without the extension).
     */
    protected string $name;

    /**
     * Dependencies list.
     *
     * @var array<string>
     */
    protected array $deps = [];

    /**
     * Determines if asset should be auto-registered in `init` hook.
     */
    protected bool $register = true;

    /**
     * Class constructor.
     *
     * @param Filesystem $fs Filesystem instance.
     */
    public function __construct(
        protected Filesystem $fs,
    ) {
    }

    /**
     * Returns an asset name.
     *
     * @return string
     */
    public function getName(): string
    {
        if (! isset($this->name)) {
            $name = (string)preg_replace(
                "/{$this->suffix}$/",
                '',
                static::class
            );

            $i = strrpos($name, '\\');
            $this->name = Str::kebab($i !== false ? substr($name, $i + 1) : $name);
        }

        return $this->name;
    }

    /**
     * Returns an asset handle created from prefixed name.
     *
     * @return string
     */
    public function getHandle(): string
    {
        return "dowst/{$this->getName()}";
    }

    /**
     * Returns relative asset path created from the base path, asset name and
     * extension.
     *
     * @return string
     */
    public function getRelativePath(): string
    {
        return "{$this->getBasePath()}/{$this->getName()}.{$this->getExtension()}";
    }

    /**
     * Returns an asset path created from the base path, asset name and
     * extension.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->fs->path($this->getRelativePath());
    }

    /**
     * Returns an asset url created from the base path, asset name and
     * extension.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->fs->url("{$this->getBasePath()}/{$this->getName()}.{$this->getExtension()}");
    }

    /**
     * Returns an asset file contents.
     *
     * @return string
     */
    public function getContents(): string
    {
        return (string)$this->fs->get_contents($this->getRelativePath());
    }

    /**
     * Returns an asset dependencies array.
     *
     * @return array<string>
     */
    public function getDeps(): array
    {
        return $this->deps;
    }

    /**
     * Registers an asset.
     *
     * @action init
     *
     * @return void
     */
    public function registerAction(): void
    {
        if (! $this->register) {
            return;
        }

        $this->register();
    }

    /**
     * Returns a relative base path for asset.
     *
     * @return string
     */
    abstract protected function getBasePath(): string;

    /**
     * Returns an asset file extension.
     *
     * @return string
     */
    abstract public function getExtension(): string;

    /**
     * Registers an asset.
     *
     * @return void
     */
    abstract public function register(): void;

    /**
     * Enqueues an asset.
     *
     * @return void
     */
    abstract public function enqueue(): void;
}
