<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features\ReusableContent;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Contracts\Hookable;

/**
 * Reusable Content feature class
 */
class ReusableContentHooks implements Hookable
{
    /**
     * Loads content slots config.
     *
     * @param Config $config Config instance.
     */
    public function __construct(
        private Config $config,
    ) {
    }

    /**
     * Prints styles from BlockSpacing class for reusable content posts.
     *
     * @action wp_footer
     *
     * @return void
     */
    public function printStyles(): void
    {
        $styles = ReusableContent::getStyles();

        if (count($styles) === 0) {
            return;
        }

        printf(
            "<style>\n%s\n</style>",
            implode("\n\n", $styles)
        );
    }

    /**
     * Filter content slots ACF field
     *
     * @filter acf/load_field
     *
     * @param  array<string, mixed> $field Field config.
     * @return array<string, mixed>
     */
    public function contentSlotsField(array $field): array
    {
        if ($field['name'] === 'content_slot') {
            $field['choices'] = $this->config->get('content-slots');
        }

        return $field;
    }

    /**
     * Update slots config
     *
     * @action acf/save_post
     *
     * @param int|string $postId Post id.
     * @return void
     */
    public function savePost($postId): void
    {
        if (get_post_type((int)$postId) !== 'reusable-content') {
            return;
        }

        $slots = get_field('content_slot', $postId);
        $slotPosts = get_option('content_slot_posts', []);
        $allSlots = array_unique(array_merge($slots, array_keys($slotPosts)));

        foreach ($allSlots as $slot) {
            if (! array_key_exists($slot, $slotPosts)) {
                $slotPosts[$slot] = [$postId];
            } elseif (in_array($slot, $slots, true) && ! in_array($postId, $slotPosts[$slot], true)) {
                array_push($slotPosts[$slot], $postId);
            } elseif (! in_array($slot, $slots, true) && in_array($postId, $slotPosts[$slot], true)) {
                $slotPosts[$slot] = array_diff($slotPosts[$slot], [$postId]);
            }
        }

        update_option('content_slot_posts', $slotPosts);
    }
}
