<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Config;

use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;
use DoWStarterTheme\Deps\Noodlehaus\Config as BaseConfig;
use DoWStarterTheme\Deps\Noodlehaus\Parser;

/**
 * Config files repository.
 *
 * Do not use the class directly. Use `DoWStarterTheme\Config\Config`
 * class instead.
 */
class ConfigRepository
{
    /**
     * List of available config files paths.
     *
     * @var  array<string>
     */
    protected array $configPaths = [];

    /**
     * Loaded config repositories.
     *
     * @var  array<\DoWStarterTheme\Deps\Noodlehaus\Config>
     */
    protected array $config = [];

    /**
     * Config repository constructor.
     *
     * @param Filesystem $filesystem Filesystem instance.
     * @param string     $configPath Path to config files.
     */
    public function __construct(
        protected Filesystem $filesystem,
        protected string $configPath,
    ) {
    }

    /**
     * Preloads all config file paths.
     *
     * @return void
     */
    public function load(): void
    {
        $configFiles = $this->filesystem->dirlist($this->configPath);

        if (! is_array($configFiles)) {
            throw new \LogicException('Cannot load config files.');
        }

        foreach ($configFiles as $file) {
            if ($file['type'] !== 'f') {
                // Directory...
                continue;
            }

            [$namespace, $extension] = $this->parseFilename($file['name']);

            if (! $this->isSupportedExtension($extension)) {
                throw new \LogicException(
                    sprintf('Config files with [%s] extension is not supported.', $extension)
                );
            }

            $this->configPaths[$namespace] = $this->filesystem->path($this->configPath . '/' . $file['name']);
        }
    }

    /**
     * Gets the config value.
     *
     * @param string $key     Config key.
     * @param mixed  $default Default value.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        [$namespace, $key] = $this->parseKey($key);

        if (! is_string($namespace) || ! array_key_exists($namespace, $this->configPaths)) {
            return $default;
        }

        if (! isset($this->config[$namespace])) {
            $this->config[$namespace] = new BaseConfig($this->configPaths[$namespace]);
        }

        return is_string($key)
            ? $this->config[$namespace]->get($key, $default)
            : $this->config[$namespace]->all();
    }

    /**
     * Parses config filename.
     *
     * @param string $filename Config filename.
     * @return  array<string>
     */
    protected function parseFilename(string $filename): array
    {
        return [
            pathinfo($filename, PATHINFO_FILENAME),
            pathinfo($filename, PATHINFO_EXTENSION),
        ];
    }

    /**
     * Parses config key.
     *
     * @param string $key Config key.
     * @return array<string|null>
     */
    protected function parseKey(string $key): array
    {
        $parts = explode('.', $key);

        if (count($parts) === 1) {
            return [$parts[0], null];
        }

        return [$parts[0], implode('.', array_slice($parts, 1))];
    }

    /**
     * Gets the list of supported parsers.
     *
     * @return  array<class-string<\DoWStarterTheme\Deps\Noodlehaus\Parser\ParserInterface>>
     */
    protected function getParsers(): array
    {
        return [
            Parser\Ini::class,
            Parser\Json::class,
            Parser\Php::class,
            Parser\Properties::class,
            Parser\Serialize::class,
            Parser\Xml::class,
            Parser\Yaml::class,
        ];
    }

    /**
     * Gets supported file extensions.
     *
     * @return array<string> Supported extensions.
     */
    protected function getSupportedExtensions(): array
    {
        $extensions = [];

        foreach ($this->getParsers() as $parser) {
            $extensions = array_merge($extensions, $parser::getSupportedExtensions());
        }

        return array_filter($extensions, static fn($extension) => is_string($extension));
    }

    /**
     * Checks whether given extension is supported.
     *
     * @param string $extension Config file extension.
     * @return bool
     */
    protected function isSupportedExtension(string $extension): bool
    {
        return in_array($extension, $this->getSupportedExtensions(), true);
    }
}
