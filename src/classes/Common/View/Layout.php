<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

/**
 * Layout class
 */
class Layout
{
    /**
     * Template name
     *
     * @var string
     */
    private $template;

    /**
     * Template name
     *
     * @var string
     */
    private $layout = 'index';

    /**
     * Class constructor.
     *
     * @param ViewFactory $view View factory instance.
     */
    public function __construct(
        private ViewFactory $view,
    ) {
    }

    /**
     * Sets template name.
     *
     * @param string $template Template name.
     * @return void
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * Sets layout name.
     *
     * @param string $layout Layout name.
     * @return void
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Loads and displays proper layout.
     *
     * @return void
     */
    public function get(): void
    {
        $content = $this->view->get($this->template)->render();

        $this->view->get("layouts.{$this->layout}", ['content' => $content])
            ->render(true);
    }
}
