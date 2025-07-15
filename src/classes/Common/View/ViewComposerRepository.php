<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\View\Finder\ViewFinder;
use DoWStarterTheme\Deps\DI\Container;
use LogicException;

/**
 * View Composer Repository class
 */
class ViewComposerRepository
{
    /**
     * Composer instances
     *
     * @var array<class-string<ViewComposer>, ViewComposer>
     */
    private array $composers = [];

    /**
     * Map of view names to composer classes
     *
     * @var array<string, array<class-string<ViewComposer>>>
     */
    private array $viewComposers = [];

    /**
     * Class constructor.
     *
     * @param Config     $config    Config instance.
     * @param Container  $container Container instance.
     * @param ViewFinder $finder    ViewFinder instance.
     */
    public function __construct(
        private Config $config,
        private Container $container,
        private ViewFinder $finder,
    ) {
        $this->registerComposers();
    }

    /**
     * Registers composers.
     *
     * @return void
     */
    private function registerComposers(): void
    {
        $composers = $this->config->get('view.composers');
        $composers = is_array($composers) ? $composers : [];

        foreach ($composers as $composer) {
            if (! is_string($composer) || ! is_subclass_of($composer, ViewComposer::class)) {
                throw new LogicException('Composer [' . $composer . '] does not exists.');
            }

            $this->mapComposerToViews($composer);
        }
    }

    /**
     * Maps Composer to views.
     *
     * @param class-string<ViewComposer> $composer Composer class name.
     * @return void
     */
    private function mapComposerToViews(string $composer): void
    {
        $views = $composer::getViews();
        $shouldValidate = $composer::shouldValidate();

        foreach ($views as $view) {
            if ($shouldValidate && $this->finder->find($view) === null) {
                throw new LogicException('View file for composer [' . $composer . '] does not exist: ' . $view);
            }

            if (! isset($this->viewComposers[$view])) {
                $this->viewComposers[$view] = [];
            }

            $this->viewComposers[$view][] = $composer;
        }
    }

    /**
     * Returns composer instance.
     *
     * @param  class-string<ViewComposer> $composer Composer class name.
     * @return ViewComposer
     */
    private function getComposer(string $composer): ViewComposer
    {
        if (! isset($this->composers[$composer])) {
            $this->composers[$composer] = $this->container->get($composer);
        }

        return $this->composers[$composer];
    }

    /**
     * Apply composers to the view.
     *
     * @param string $name View name.
     * @param View   $view View instance.
     * @return void
     */
    public function compose(string $name, View $view): void
    {
        if (! isset($this->viewComposers[$name])) {
            return;
        }

        foreach ($this->viewComposers[$name] as $composer) {
            $this->getComposer($composer)->compose($view);
        }
    }
}
