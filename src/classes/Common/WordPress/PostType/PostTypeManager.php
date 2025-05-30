<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\WordPress\PostType;

use DoWStarterTheme\Common\Abstracts\Manager;

/**
 * @extends \DoWStarterTheme\Common\Abstracts\Manager<\DoWStarterTheme\Common\WordPress\PostType\PostType>
 */
class PostTypeManager extends Manager
{
    protected string $parentClass = PostType::class;

    protected string $configKey = 'app.post-types';

    /**
     * Registers post types.
     *
     * @action init
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->getItems() as $postType) {
            register_post_type(
                $postType::getSlug(),
                array_merge(
                    $postType::getArgs(),
                    ['labels' => $postType::getLabels()]
                )
            );
        }
    }
}
