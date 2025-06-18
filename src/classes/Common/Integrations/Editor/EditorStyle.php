<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Integrations\Editor;

use DoWStarterTheme\Common\Assets\Style;

/**
 * Editor Style asset class.
 */
class EditorStyle extends Style
{
    protected string $name = 'style-editor';

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
