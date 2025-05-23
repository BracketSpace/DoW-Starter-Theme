<?php

declare(strict_types=1);

namespace DoWStarterTheme;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Config\ConfigRepository;
use DoWStarterTheme\Common\Contracts\ServiceProvider;
use DoWStarterTheme\Common\Contracts\WithContainerSetter;
use DoWStarterTheme\Common\Helpers\Factory;
use DoWStarterTheme\Common\Integrations\Requirements as ExtraRequirements;
use DoWStarterTheme\Deps\DI\Container;
use DoWStarterTheme\Deps\DI\ContainerBuilder;
use DoWStarterTheme\Deps\Micropackage\DocHooks\Helper;
use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;
use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;
use DoWStarterTheme\Deps\Micropackage\Requirements\Requirements;

/**
 * Theme bootstrapper.
 */
class Bootstrap
{
    use HookTrait;

    /**
     * Container instance.
     */
    public static ?Container $container = null;

    /**
     * Filesystem instance.
     */
    protected ?Filesystem $filesystem = null;

    /**
     * Config instance.
     */
    protected ?Config $config = null;

    /**
     * Bootstraper constructor.
     *
     * @param string $themeFile Base file of theme.
     */
    public function __construct(
        public string $themeFile
    ) {
        if ($this->checkRequirements() === false) {
            return;
        }

        $this->buildContainer();
        $this->registerComponents();
        $this->initClasses();
        $this->registerHooks();
    }

    /**
     * Gets the container instance.
     *
     * @return Container
     */
    public static function getContainer(): Container
    {
        if (! self::$container instanceof Container) {
            throw new \LogicException('Cannot access Container, because it is not instantiated.');
        }

        return self::$container;
    }

    /**
     * Check theme requirements.
     *
     * @return  bool
     */
    protected function checkRequirements(): bool
    {
        $requirements = new Requirements(
            $this->getThemeName(),
            [
                'assets' => true,
                'customizer' => true,
                'dochooks' => true,
                'php' => '8.2',
                'php_extensions' => ['SimpleXML'],
                'plugins' => [
                    [
                        'file' => 'advanced-custom-fields-pro/acf.php',
                        'name' => 'Advanced Custom Fields Pro',
                    ],
                ],
                'wp' => '6.8',
            ]
        );

        $requirements->register_checker(ExtraRequirements\AssetsChecker::class);
        $requirements->register_checker(new ExtraRequirements\CustomizerChecker($this->config()));

        if (! $requirements->satisfied()) {
            if (is_admin()) {
                $requirements->print_notice();
            } else {
                $requirements->kill();
            }

            return false;
        }

        return true;
    }

    /**
     * Returns theme name.
     *
     * @return string
     */
    protected function getThemeName(): string
    {
        return 'Starter Theme';
    }

    /**
     * Gets the theme path.
     *
     * @return string
     */
    protected function getThemePath(): string
    {
        return dirname($this->themeFile);
    }

    /**
     * Gets the config path.
     *
     * @return string
     */
    protected function getConfigPath(): string
    {
        return 'config';
    }

    /**
     * Gets the filesystem instance.
     *
     * @return Filesystem
     */
    protected function filesystem(): Filesystem
    {
        if (! isset($this->filesystem)) {
            $this->filesystem = new Filesystem($this->getThemePath());
        }

        return $this->filesystem;
    }

    /**
     * Gets the config instance.
     *
     * @return Config
     */
    protected function config(): Config
    {
        if (! isset($this->config)) {
            $repository = new ConfigRepository($this->filesystem(), $this->getConfigPath());
            $repository->load();

            $this->config = new Config($repository);
        }

        return $this->config;
    }

    /**
     * Builds container.
     *
     * @return void
     */
    protected function buildContainer(): void
    {
        $builder = new ContainerBuilder();

        $this->registerContainterConstants($builder);
        $this->registerBaseServices($builder);
        $this->registerServices($builder);

        self::$container = $builder->build();
    }

    /**
     * Registers constants in service container.
     *
     * @param ContainerBuilder<Container> $builder Container builder instance.
     * @return void
     */
    protected function registerContainterConstants(ContainerBuilder $builder): void
    {
        $builder->addDefinitions(
            [
                'theme.name' => $this->getThemeName(),
                'theme.path' => $this->getThemePath(),
                'config.path' => $this->getConfigPath(),
            ]
        );
    }

    /**
     * Registers base theme services.
     *
     * Those services will be added to the container before any other serivces.
     *
     * @param ContainerBuilder<Container> $builder Container builder instance.
     * @return void
     */
    protected function registerBaseServices(ContainerBuilder $builder): void
    {
        $builder->addDefinitions(
            [
                Filesystem::class => $this->filesystem(),
                Config::class => $this->config(),
            ]
        );
    }

    /**
     * Register services.
     *
     * @param ContainerBuilder<Container> $builder Container builder instance.
     * @return void
     */
    protected function registerServices(ContainerBuilder $builder): void
    {
        $providers = $this->config()->get('app.providers');

        if (! is_array($providers)) {
            $providers = [];
        }

        foreach ($providers as $provider) {
            if (! class_exists($provider) || ! is_subclass_of($provider, ServiceProvider::class)) {
                continue;
            }

            // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
            $builder->addDefinitions((new $provider())->provides());
        }
    }

    /**
     * Registers components.
     *
     * @return void
     */
    protected function registerComponents(): void
    {
        /** @var array<class-string> $components */
        $components = $this->config()->get('app.components', []);

        $factory = self::getContainer()->get(Factory::class);
        $factory->makeFromArray($components);
    }

    /**
     * Initialize classes.
     *
     * @return void
     */
    protected function initClasses(): void
    {
        /** @var array<class-string> */
        $classes = $this->config()->get('app.initialize', []);

        foreach ($classes as $class) {
            if (! class_exists($class)) {
                throw new \LogicException(sprintf('[%s] class cannot be initialized.', $class));
            }

            if (! is_subclass_of($class, WithContainerSetter::class)) {
                continue;
            }

            $class::setContainer(self::getContainer());
        }
    }

    /**
     * Registers WordPress hooks.
     *
     * @return void
     */
    protected function registerHooks(): void
    {
        if (Helper::is_enabled() || ! $this->filesystem()->exists('compat/register-hooks.php')) {
            return;
        }

        include_once $this->filesystem()->path('compat/register-hooks.php');
    }
}
