<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

use InvalidArgumentException;

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
     * Current layout section being rendered.
     *
     * @var string|null
     */
    private $currentSection = null;

    /**
     * Sections storage.
     *
     * @var string[]
     */
    private $sections = [];


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

        $this->view->get(
            "layouts.{$this->layout}",
            array_merge(
                ['content' => $content],
                $this->sections
            )
        )->render(true);
    }

    /**
     * Starts a new section in the layout.
     *
     * @param string $section
     * @return void
     */
    public function startSection(string $section): void
    {
        $this->currentSection = $section;
        ob_start();
    }

    /**
     * Ends the current section.
     *
     * @param string $section
     * @return void
     */
    public function endSection(string $section): void
    {
        if ($this->currentSection !== $section) {
            ob_end_clean();

            throw new InvalidArgumentException(
                $this->currentSection === null
                    ? sprintf('Ending section "%s" without starting it.', $section)
                    : sprintf('Ending section "%s" does not match current section "%s".', $section, $this->currentSection)
            );
        }

        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }
}
