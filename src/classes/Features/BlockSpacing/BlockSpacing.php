<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features\BlockSpacing;

/**
 * BlockSpacing class
 */
class BlockSpacing
{
    /**
     * Returns spacing styles for post.
     *
     * @param  int $postId Post ID.
     * @return string|false
     */
    public function getStyles(int $postId)
    {
        return get_post_meta($postId, 'block_spacing_styles', true);
    }
}
