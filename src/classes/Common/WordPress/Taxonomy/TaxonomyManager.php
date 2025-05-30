<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\WordPress\Taxonomy;

use DoWStarterTheme\Common\Abstracts\ObjectManager;

/**
 * @extends ObjectManager<Taxonomy>
 */
class TaxonomyManager extends ObjectManager
{
    protected string $parentClass = Taxonomy::class;

    protected string $configKey = 'app.taxonomies';

    /**
     * Registers taxonomies.
     *
     * @action init
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->getItems() as $taxonomy) {
            register_taxonomy(
                $taxonomy::getSlug(),
                $taxonomy::getObjectTypes(),
                array_merge(
                    $taxonomy::getArgs(),
                    ['labels' => $taxonomy::getLabels()]
                )
            );
        }
    }
}
