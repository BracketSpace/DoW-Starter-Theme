<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

use DoWStarterTheme\Deps\DI\Container;
use DoWStarterTheme\Common\View\Finder\ViewFinder;

/**
 * View Factory class
 */
class ViewFactory
{
    /**
     * Sets up the Factory.
     *
     * @param Container              $container Container instance.
     * @param ViewFinder             $finder    View finder instance.
     * @param ViewComposerRepository $composer  View composer instance.
     */
    public function __construct(
        private Container $container,
        private ViewFinder $finder,
        private ViewComposerRepository $composer,
    ) {
        ViewHelper::setup($this);
    }

    /**
     * Returns view instance prepared with given data.
     *
     * @param  string               $name View name
     * @param  array<string, mixed> $data Data.
     * @return View
     */
    public function get(string $name, array $data = []): View
    {
        $containerKey = "view:{$name}";

        if (! $this->container->has($containerKey)) {
            $view = $this->make($name, $data);
            $this->container->set($containerKey, $view);
        } else {
            /** @var View */
            $view = $this->container->get($containerKey);
            $view->with($data);
        }

        $this->composer->compose($name, $view);

        return $view;
    }

    /**
     * Makes new vView instance.
     *
     * @param  string               $name View name.
     * @param  array<string, mixed> $data View data.
     * @return \DoWStarterTheme\Common\View\View
     */
    private function make(string $name, array $data = []): View
    {
        $file = $this->finder->find($name);

        if ($file === null) {
            throw new \Exception(
                /* translators: %s is a view name. */
                sprintf(__('View: "%s" does not exist.', 'dow-starter-theme'), $name)
            );
        }

        return new View($file, $data);
    }

    /**
     * Prints the view.
     *
     * @param  string               $name View name.
     * @param  array<string, mixed> $data View data.
     * @return void
     */
    public function print(string $name, array $data = []): void
    {
        echo $this->get($name, $data)->render();
    }
}
