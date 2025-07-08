<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\View;

/**
 * View class
 */
class View
{
    protected ViewData $data;

    /**
     * Constructor.
     *
     * @param string               $file View file.
     * @param array<string, mixed> $data Optional array of vars passed to view file.
     */
    public function __construct(
        protected string $file,
        array $data = []
    ) {
        $this->data = new ViewData($data);
    }

    /**
     * Sets view data.
     *
     * @param array<string, mixed> $data     Data.
     * @param bool                 $override Whether to override the entire data array (default).
     * @return static
     */
    public function with(array $data, bool $override = true): View
    {
        if ($override) {
            $this->data->replace($data);
        } else {
            $this->data->merge($data);
        }

        return $this;
    }

    /**
     * Returns data array.
     *
     * @return ViewData View data.
     */
    public function getData(): ViewData
    {
        return $this->data;
    }

    /**
     * Renders a view.
     *
     * @param  bool $echo Whether to echo the output.
     * @return string Rendered output.
     */
    public function render(bool $echo = false): string
    {
        ViewHelper::pushVariables($this->data->getAll());

        ob_start();
        include $this->file;
        $output = ob_get_clean();

        ViewHelper::popVariables();

        if ($echo) {
            echo $output;
        }

        return (string)$output;
    }

    /**
     * Returns loaded template as string.
     *
     * @return string Template string.
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
