<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Traits;

use DoWStarterTheme\Deps\Illuminate\Support\Collection;

/**
 * Cast fields trait.
 */
trait WithCastableFields
{
    /**
     * The attributes that should be casted.
     *
     * @var array<string, string>
     */
    protected array $casts = [];

    /**
     * Casts field value.
     *
     * @param string $field Field name.
     * @param mixed  $value Field value to cast.
     * @return mixed
     */
    public function castValue(string $field, mixed $value): mixed
    {
        $cast = array_key_exists($field, $this->casts) ? $this->casts[$field] : null;

        if (! is_string($cast)) {
            return $value;
        }

        $callback = [$this, $cast];

        if (is_callable($callback)) {
            // phpcs:ignore NeutronStandard.Functions.DisallowCallUserFunc.CallUserFunc
            return call_user_func_array($callback, [$value, $field]);
        }

        if (is_array($value)) {
            return Collection::make($value)
                ->map(
                    fn ($value, $key) => $this->castValue(
                        is_numeric($key)
                        ? "{$field}.*"
                        : "{$field}.{$key}",
                        $value
                    )
                )
                ->all();
        }

        return match ($cast) {
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'int', 'integer' => is_numeric($value) ? (int)$value : null,
            'nullable' => is_string($value) && strlen($value) > 0 ? $value : null,
            default => $value,
        };
    }

    /**
     * Set casts map.
     *
     * @param  array<string, string> $casts Casts map.
     * @return void
     */
    public function addCasts(array $casts): void
    {
        $this->casts = array_merge($this->casts, $casts);
    }
}
