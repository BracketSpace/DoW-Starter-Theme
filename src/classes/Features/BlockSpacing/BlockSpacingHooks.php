<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features\BlockSpacing;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Common\Helpers\CSSGenerator;
use WP_Post;

/**
 * BlockSpacing class
 */
class BlockSpacingHooks implements Hookable
{
    /**
     * Class constructor.
     *
     * @param BlockSpacing $blockSpacing Block spacing helper instance.
     */
    public function __construct(
        private BlockSpacing $blockSpacing,
    ) {
    }

    /**
     * Adds spacing styles
     *
     * @action wp_enqueue_scripts 20
     *
     * @return void
     */
    public function addStyles(): void
    {
        $styles = $this->blockSpacing->getStyles((int)get_the_ID());

        if (! is_string($styles) || $styles === '') {
            return;
        }

        wp_add_inline_style('dow-starter-theme-front-style', $styles);
    }

    /**
     * Generates margin/padding classes based on the post content
     *
     * @action save_post
     *
     * @param int     $postId Post ID.
     * @param WP_Post $post   Post object.
     * @return void
     */
    public function savePost(int $postId, WP_Post $post): void
    {
        // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        preg_match_all('/class="([^"]+)"/', do_blocks($post->post_content), $matches);

        $classAtts = $matches[1];
        $classes = [];

        foreach ($classAtts as $attr) {
            preg_match_all('/(?:margin|padding)-(?:bottom|top)-(?:[0-9]+)/', $attr, $matches);
            $classes = array_merge($classes, $matches[0]);
        }

        $classes = array_unique($classes);

        if (count($classes) === 0) {
            return;
        }

        $rules = [];

        foreach ($classes as $class) {
            $parts = explode('-', $class);
            $prop = "{$parts[0]}-{$parts[1]}";
            $value = $parts[2];
            $selector = ".{$class}";

            $rules[$selector] = [];
            $rules[$selector][$prop] = $value;
        }

        $generator = new CSSGenerator($rules);

        update_post_meta($postId, 'block_spacing_styles', $generator->getCss());
    }
}
