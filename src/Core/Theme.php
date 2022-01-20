<?php
/**
 * @package DoW Starter Theme
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 */

declare(strict_types=1);

namespace DoWStarterTheme\Core;

use Micropackage\DocHooks\Helper;
use Micropackage\Filesystem\Filesystem;
use Micropackage\Singleton\Singleton;
use Micropackage\DocHooks\HookTrait;

/**
 * Core Theme class
 *
 * @phpstan-type ImageSize array{width?: int, height?: int, crop?: bool}
 */
class Theme extends Singleton
{
    use HookTrait;

    /**
     * Filesystem instance.
     *
     * @var \Micropackage\Filesystem\Filesystem
     */
    protected static $fs;

    /**
     * Returns Filesystem instance.
     *
     * @return \Micropackage\Filesystem\Filesystem
     */
    public static function getFs()
    {
        return static::$fs;
    }

    /**
     * Constructor.
     *
     * @param \Micropackage\Filesystem\Filesystem $fs Filesystem instance
     */
    protected function __construct(Filesystem $fs)
    {
        parent::__construct();

        static::$fs = $fs;

        $this->add_hooks();
    }

    /**
     * Class instances.
     *
     * @var array<object>
     */
    protected $services = [];

    /**
     * Widget classes
     *
     * @var array<string>
     */
    protected $widgets = [];

    /**
     * Theme support features to be removed
     *
     * @var array<string>
     */
    protected $removeSupport = [];

    /**
     * Image sizes
     *
     * phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName
     * @var array<string, ImageSize>
     */
    protected $imageSizes = [];

    /**
     * Bootstraps the theme.
     *
     * @param array<string> $classes Class names to bootstrap.
     * @return void
     */
    public function bootstrap(array $classes = []): void
    {
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                continue;
            }

            // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
            $this->services[$class] = new $class();

            Helper::hook($this->services[$class]);
        }
    }

    /**
     * Adds theme support options.
     *
     * @param array<string,mixed> $support Theme support configuration.
     * @return void
     */
    public function addThemeSupport(array $support): void
    {
        foreach ($support as $key => $value) {
            if ($value === false) {
                $this->removeSupport[] = $key;
                continue;
            }

            add_theme_support($key, $value);
        }

        // Disable admin bar styles.
        add_theme_support('admin-bar', [ 'callback' => '__return_false' ]);
    }

    /**
     * Removes theme support if necessary.
     *
     * @action after_setup_theme 100
     *
     * @return void
     */
    public function removeThemeSupport(): void
    {
        foreach ($this->removeSupport as $feature) {
            remove_theme_support($feature);
        }
    }

    /**
    * Adds widget classes for future registration in `widgets_init` action.
     *
     * @param array<string> $widgets Widget classes to be registered.
     * @return void
     */
    public function addWidgets(array $widgets): void
    {
        $this->widgets = $widgets;
    }

    /**
     * Registers widgets
     *
     * @action widgets_init
     *
     * @return void
     */
    public function registerWidgets(): void
    {
        foreach ($this->widgets as $widget) {
            if (!class_exists($widget)) {
                continue;
            }

            register_widget($widget);
        }
    }

    /**
     * Adds image sizes from config file.
     *
     * phpcs:ignore SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName
     * @param  array<string, ImageSize> $sizes Image sizes.
     * @return void
     */
    public function addImageSizes(array $sizes): void
    {
        $this->imageSizes = $sizes;
    }

    /**
     * Sets up image sizes.
     *
     * @return void
     */
    protected function setupImageSizes(): void
    {
        foreach ($this->imageSizes as $size => $config) {
            $args = [ $size ];

            if (isset($config['width']) && isset($config['height'])) {
                $args[] = $config['width'];
                $args[] = $config['height'];

                if (isset($config['crop'])) {
                    $args[] = $config['crop'];
                }
            }

            add_image_size(...$args);
        }
    }

    /**
     * Loads theme textdomain.
     *
     * @action after_setup_theme
     *
     * @return void
     */
    public function setup()
    {
        load_theme_textdomain('dow-starter-theme', get_stylesheet_directory() . '/languages');

        $this->setupImageSizes();
    }
}
