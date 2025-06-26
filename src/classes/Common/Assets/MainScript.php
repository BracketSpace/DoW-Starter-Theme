<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Assets;

class MainScript extends Script
{
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
