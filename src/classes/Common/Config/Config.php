<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Config;

/**
 * Config class.
 */
class Config
{
    /**
     * Class constructor.
     *
     * @param  ConfigRepository $repository Repository instance.
     */
    public function __construct(
        protected ConfigRepository $repository
    ) {
    }

    /**
     * Gets the config value.
     *
     * @template T
     * @param  string $key     Config key.
     * @param  T      $default Default value.
     * @return ($default is null ? mixed : T)
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->repository->get($key, $default);
    }

    /**
     * Gets the string value from config.
     *
     * @param  string      $key     Config key.
     * @param  string|null $default Default value.
     * @return string|null
     */
    public function getString(string $key, ?string $default = null): ?string
    {
        $value = $this->get($key, $default);

        return is_scalar($value) ? (string)$value : null;
    }
}
