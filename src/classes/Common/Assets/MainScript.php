<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Assets;

class MainScript extends Script
{
    /**
     * Determines if the script should be included in the footer.
     */
    protected bool $inFooter = true;

    /**
     * Enqueues asset.
     *
     * @action wp_enqueue_scripts
     */
    public function enqueueAsset(): void
    {
        $this->enqueue();
    }
}
