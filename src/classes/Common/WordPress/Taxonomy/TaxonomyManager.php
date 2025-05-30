<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\WordPress\Taxonomy;

use DoWStarterTheme\Common\Abstracts\Manager;

/**
 * @extends \DoWStarterTheme\Common\Abstracts\Manager<Taxonomy>
 */
class TaxonomyManager extends Manager
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
