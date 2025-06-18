<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

use Stringable;

/**
 * View Helper class.
 */
final class ViewHelper
{
    /**
     * View variables stack.
     *
     * @var array<array<string, mixed>>
     */
    private static array $variablesStack = [];

    /**
     * View factory instance.
     *
     * @var \DoWStarterTheme\Common\View\ViewFactory
     */
    private static ViewFactory $factory;

    /**
     * Sets view factory instance.
     *
     * @param  ViewFactory $factory View factory instance.
     * @return void
     */
    public static function setup(ViewFactory $factory): void
    {
        self::$factory = $factory;
    }

    /**
     * Pushes variables to the end of stack.
     *
     * Method used internally by core, do not use it.
     *
     * @param   array<string, mixed> $variables List of variables.
     * @return  void
     */
    public static function pushVariables(array $variables): void
    {
        self::$variablesStack[] = $variables;
    }

    /**
     * Pops variables from the end of stack.
     *
     * Method used internally by core, do not use it.
     *
     * @return  void
     */
    public static function popVariables(): void
    {
        array_pop(self::$variablesStack);
    }

    /**
     * Gets all view variables from stack.
     *
     * @return  array<string, mixed>
     */
    public static function getData(): array
    {
        if (count(self::$variablesStack) === 0) {
            throw new \LogicException('There is no data in variables stack.');
        }

        return end(self::$variablesStack);
    }

    /**
     * Gets view data value.
     *
     * @param  string $key     Data key.
     * @param  mixed  $default Default value.
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return self::getData()[$key] ?? $default;
    }

    /**
     * Gets view data value.
     *
     * @param  string $key     Data key.
     * @param  mixed  $default Default value.
     * @return string
     */
    private static function getString(string $key, mixed $default = null): string
    {
        $value = self::get($key, $default);

        return is_null($value)
            || is_scalar($value)
            || (is_object($value) && $value instanceof Stringable)
            || is_resource($value)
                ? (string)$value
                : '';
    }

    /**
     * Echoes raw view data.
     *
     * @param  string $key     Data key.
     * @param  mixed  $default Default value.
     * @return void
     */
    public static function raw(string $key, $default = null): void
    {
        echo self::getString($key, $default);
    }

    /**
     * Echoes escaped value.
     *
     * @param  string $key     Data key.
     * @param  mixed  $default Default value.
     * @param  string $type    Escape function type.
     * @return void
     */
    public static function esc(string $key, $default = null, string $type = ''): void
    {
        $value = self::getString($key, $default);

        $availableTypes = [
            'html',
            'attr',
            'url',
        ];

        if (! in_array($type, $availableTypes, true)) {
            $type = 'html';
        }

        $func = "esc_{$type}";

        // phpcs:ignore NeutronStandard.Functions.DisallowCallUserFunc.CallUserFunc
        echo call_user_func($func, $value);
    }

    /**
     * Echoes escaped url.
     *
     * @param  string $key     Data key.
     * @param  mixed  $default Default value.
     * @return void
     */
    public static function url(string $key, $default = null): void
    {
        self::esc($key, $default, 'url');
    }

    /**
     * Echoes escaped attribute value.
     *
     * @param  string $key     Data key.
     * @param  mixed  $default Default value.
     * @return void
     */
    public static function attr(string $key, $default = null): void
    {
        self::esc($key, $default, 'attr');
    }

    /**
     * Creates and displays new view.
     *
     * @param string               $name Template name.
     * @param array<string, mixed> $data Variables passed to template file.
     * @return void
     */
    public static function print(string $name, array $data = []): void
    {
        self::$factory->get($name, $data)->render(true);
    }
}
