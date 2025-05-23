<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Abstracts;

use DoWStarterTheme\Deps\Illuminate\Support\Str;
use WP_Post;

/**
 * AbstractShortcode class
 *
 * @template T of array<string, mixed>
 */
abstract class Shortcode extends Sluggable
{
    protected static string $suffix = 'Shortcode';

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
     * Get shortcode tag
     *
     * @return string
     */
    public function getTag(): string
    {
        if (! isset($this->tag)) {
            $this->tag = Str::of(self::getSlug())
                ->replace('-', '_')
                ->prepend($this->prefix)
                ->toString();
        }

        return $this->tag;
    }

    /**
     * Checks if the shortcode is being used in the current post content.
     *
     * @return bool
     */
    public function isUsed(): bool
    {
        $post = get_post();

        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        return $post instanceof WP_Post && has_shortcode($post->post_content, $this->getTag());
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
        /** @var T */
        $atts = shortcode_atts($this->atts, $atts);

        ob_start();

        $this->render($atts, $content);

        $output = ob_get_clean();

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
