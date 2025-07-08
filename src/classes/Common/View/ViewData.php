<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

/**
 * View Data Container.
 */
class ViewData
{
    /** @var array<string, mixed> */
    private array $data;

    /**
     * ViewData constructor.
     *
     * @param array<string, mixed> $data Initial data for the view.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the view data.
     *
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        return $this->data;
    }

    /**
     * Get a specific data item.
     *
     * @param string $key     Key of the data item to retrieve.
     * @param mixed  $default Default value if the key does not exist.
     * @return mixed|null
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set a specific data item.
     *
     * @param string $key   Key of the data item to set.
     * @param mixed  $value Value to set for the key.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Merge additional data into the existing data array.
     *
     * @param array<string, mixed> $data Data to merge with the existing data.
     * @return void
     */
    public function merge(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Replace the entire data array with new data.
     *
     * @param array<string, mixed> $data New data.
     * @return void
     */
    public function replace(array $data): void
    {
        $this->data = $data;
    }
}
