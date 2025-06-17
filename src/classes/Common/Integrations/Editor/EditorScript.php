<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Integrations\Editor;

use DoWStarterTheme\Common\Assets\Script;

/**
 * Editor Script asset class.
 *
 * This class is responsible for enqueuing scripts in the block editor.
 */
class EditorScript extends Script
{
    /**
     * Enqueues asset.
     *
     * @action enqueue_block_editor_assets
     *
     * @return void
     */
    public function enqueueAsset(): void
    {
        $this->enqueue();
    }
}
