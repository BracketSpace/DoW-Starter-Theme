<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features\ReusableContent;

use DoWStarterTheme\Features\BlockSpacing\BlockSpacing;

/**
 * Reusable Content helper class
 */
class ReusableContent
{
    /**
     * Array of styles for reusable content
     *
     * @var array<string>
     */
    protected static array $styles = [];

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
     * Displays content for given slot.
     *
     * @param string      $slot Slot name.
     * @param bool|string $wrap Whether to wrap the content in a div element. If a string is passed, it will be used as
     *                          a wrapper element's class name.
     * @return void
     */
    public function display(string $slot, $wrap = true): void
    {
        echo $this->get($slot, $wrap);
    }

    /**
     * Gets content for given slot.
     *
     * @param string      $slot Slot name.
     * @param bool|string $wrap Whether to wrap the content in a div element. If a string is passed, it will be used as
     *                          a wrapper element's class name.
     * @return string|null
     */
    public function get(string $slot, $wrap = true): ?string
    {
        $content = $this->getRawContent($slot);

        if (! is_string($content)) {
            return null;
        }

        if ($wrap !== false) {
            $class = is_string($wrap) ? $wrap : 'reusable-content';

            return sprintf(
                '<div class="%1$s">%2$s</div>',
                $class,
                $content
            );
        }

        return $content;
    }

    /**
     * Gets raw content for given slot
     *
     * @param  string $slot Slot name.
     * @return string
     */
    public function getRawContent(string $slot): ?string
    {
        $slotPosts = get_option('content_slot_posts', []);

        if (! array_key_exists($slot, $slotPosts) || count($slotPosts[$slot]) === 0) {
            return null;
        }

        $posts = get_posts(
            [
                'post_type' => 'reusable-content',
                'posts_per_page' => -1,
                'post__in' => $slotPosts[$slot],
                'orderby' => 'post__in',
            ]
        );

        $content = '';

        foreach ($posts as $post) {
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound, Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            $content .= apply_filters('the_content', $post->post_content);

            $styles = $this->blockSpacing->getStyles($post->ID);

            if (! is_string($styles)) {
                continue;
            }

            self::addStyles($styles);
        }

        return $content;
    }

    /**
     * Adds the styles to the heap.
     *
     * @param string $styles Style content.
     * @return void
     */
    public static function addStyles(string $styles): void
    {
        self::$styles[] = $styles;
    }

    /**
     * Gets the styles list.
     *
     * @return array<string>
     */
    public static function getStyles(): array
    {
        return self::$styles;
    }
}
