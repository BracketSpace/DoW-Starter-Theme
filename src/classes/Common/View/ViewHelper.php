<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

use DoWStarterTheme\Common\Helpers\SVG;
use DoWStarterTheme\Common\Menu\Menu;
use DoWStarterTheme\Deps\DI\Container;
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
     * Container instance
     *
     * @var Container
     */
    private static Container $container;

    /**
     * Sets view factory instance.
     *
     * @param  ViewFactory $factory View factory instance.
     * @return void
     */
    public static function setup(
        ViewFactory $factory,
        Container $container,
    ): void {
        self::$factory = $factory;
        self::$container = $container;
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

        if (! is_callable($func)) {
            return;
        }

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
     * Sets layout name.
     *
     * @param string $layout Layout name.
     * @return void
     */
    public static function layout(string $layout): void
    {
        self::$container->get(Layout::class)->setLayout($layout);
    }

    /**
     * Checks whether view exists.
     *
     * @param  string $name View name.
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return self::$factory->exists($name);
    }

    /**
     * Displays partial view.
     *
     * @param string               $name Partial view name.
     * @param array<string, mixed> $data Partial view data.
     * @return void
     */
    public static function partial(string $name, array $data = []): void
    {
        self::print("partials.{$name}", $data);
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

    /**
     * Prints the SVG icon.
     *
     * @param   string $name Icon name.
     * @return  void
     */
    public static function icon(string $name): void
    {
        self::$container->get(SVG::class)->print("icons/{$name}");
    }

    /**
     * Creates and gets HTML of new view.
     *
     * @param string               $name Template name.
     * @param array<string, mixed> $data Variables passed to template file.
     * @return string
     */
    public static function getHtml(string $name, array $data = []): string
    {
        return self::$factory->get($name, $data)->render(false);
    }

    /**
     * Gets instance from container.
     *
     * @template T
     *
     * @param string|class-string<T> $class Template name.
     * @return mixed|T
     */
    public static function getInstance(string $class): mixed
    {
        return self::$container->get($class);
    }

    /**
     * Starts a new section in the layout.
     *
     * @param string $sectionName
     * @return void
     */
    public static function start(string $sectionName): void
    {
        self::getInstance(Layout::class)->startSection($sectionName);
    }

    /**
     * Ends the current section.
     *
     * @param string $sectionName
     * @return void
     */
    public static function end(string $sectionName): void
    {
        self::getInstance(Layout::class)->endSection($sectionName);
    }

    /**
     * Display menu.
     *
     * @param string $id
     * @return void
     */
    public static function menu(string $id): void
    {
        self::getInstance(Menu::class)->display($id);
    }
}
