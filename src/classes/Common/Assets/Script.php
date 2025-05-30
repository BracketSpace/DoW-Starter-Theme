<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Assets;

use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * @phpstan-type ScriptMeta array{dependencies: array<string>, version: string}
 * @phpstan-type OptionalScriptMeta array{dependencies?: array<string>, version?: string}
 */
abstract class Script extends Asset
{
    /**
     * Class name suffix to be removed from name.
     */
    protected string $suffix = 'Script';

    /**
     * Localization data key. If not defined, will be generated from script name.
     */
    protected string $dataKey;

    /**
     * Determines if the script should be included in the footer.
     */
    protected bool $inFooter = false;

    /**
     * Determines if the script should be localized and when.
     *
     * @var 'register'|'enqueue'|false
     */
    protected string|false $localize = 'register';

    /**
     * Metadata.
     *
     * @var OptionalScriptMeta
     */
    protected array $meta;

    /**
     * Gets the script base path.
     *
     * @return  string
     */
    protected function getBasePath(): string
    {
        return 'dist/js';
    }

    /**
     * Returns an asset file extension.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return 'js';
    }

    /**
     * Returns an asset dependencies array.
     *
     * @return OptionalScriptMeta
     */
    protected function getMeta(): array
    {
        if (! isset($this->meta)) {
            $basePath = $this->getBasePath();
            $metaPath = "{$basePath}/{$this->getName()}.asset.php";

            if ($this->fs->exists($metaPath)) {
                $metaFile = $this->fs->path($metaPath);

                /** @var ScriptMeta */
                $meta = include $metaFile;

                $this->meta = $meta;
            } else {
                $this->meta = [];
            }
        }

        return $this->meta;
    }

    /**
     * Returns an asset dependencies array.
     *
     * @return array<string>
     */
    public function getDeps(): array
    {
        return [
            ...$this->deps,
            ...($this->getMeta()['dependencies'] ?? []),
        ];
    }

    /**
     * Returns data array.
     *
     * @return array<mixed>
     */
    public function getData(): ?array
    {
        return null;
    }

    /**
     * Registers a script.
     *
     * @return void
     */
    public function register(): void
    {
        wp_register_script(
            $this->getHandle(),
            $this->getUrl(),
            $this->getDeps(),
            $this->getMeta()['version'] ?? false,
            $this->inFooter
        );

        if ($this->localize !== 'register') {
            return;
        }

        $this->localize();
    }

    /**
     * Enqueues a script.
     *
     * @return void
     */
    public function enqueue(): void
    {
        if ($this->localize === 'enqueue') {
            $this->localize();
        }

        wp_enqueue_script($this->getHandle());
    }

    /**
     * Localizes a script.
     *
     * @return void
     */
    public function localize(): void
    {
        $data = $this->getData();

        if (! is_array($data)) {
            return;
        }

        wp_localize_script(
            $this->getHandle(),
            $this->dataKey ?? (string)Str::of($this->getName())
                ->camel()
                ->append('Data'),
            $data
        );
    }
}
