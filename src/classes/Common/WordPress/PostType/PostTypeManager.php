<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\WordPress\PostType;

use DoWStarterTheme\Common\Abstracts\ObjectManager;

/**
 * @extends ObjectManager<PostType>
 */
class PostTypeManager extends ObjectManager
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
