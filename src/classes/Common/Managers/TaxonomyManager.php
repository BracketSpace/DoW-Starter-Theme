<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Managers;

use DoWStarterTheme\Common\Abstracts\Manager;
use DoWStarterTheme\Common\Abstracts\Taxonomy;

/**
 * @extends \DoWStarterTheme\Common\Abstracts\Manager<\DoWStarterTheme\Common\Abstracts\Taxonomy>
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
