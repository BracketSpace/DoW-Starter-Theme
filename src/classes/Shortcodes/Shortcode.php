<?php

declare(strict_types=1);

namespace DoWStarterTheme\Shortcodes;

/**
 * AbstractShortcode class
 *
 * @template T
 */
abstract class Shortcode
{
    /**
     * Shortcode tag prefix
     *
     * @var string
     */
    private string $prefix = 'dowst_';

    /**
     * Default attributes
     *
     * @var T
     */
    protected $atts = [];

    /**
     * Shortcode tag
     *
     * @var string
     */
    protected string $tag;

    /**
     * Constructor
     *
     * @throws \Exception Not defined property exception.
     */
    public function __construct()
    {
        add_shortcode($this->prefix . $this->tag, [ $this, 'renderCallback' ]);
    }

    /**
     * Render callback wrapper.
     *
     * @param  T      $atts    Shortcode attributes.
     * @param  string $content Shortcode content.
     * @return string|null
     */
    public function renderCallback($atts, $content): ?string
    {
        $atts = shortcode_atts($this->atts, $atts);

        \ob_start();

        $this->render($atts, $content);

        $output = \ob_get_clean();

        return is_string($output) ? $output : null;
    }

    /**
     * Render callback.
     *
     * @param  T      $atts    Shortcode attributes.
     * @param  string $content Shortcode content.
     * @return void
     */
    abstract protected function render($atts, $content);
}
