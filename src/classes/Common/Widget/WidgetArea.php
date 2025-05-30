<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Widget;

use DoWStarterTheme\Common\View\ViewFactory;

/**
 * Widgets area class
 */
class WidgetArea
{
    /**
     * Class constructor.
     *
     * @param ViewFactory $view View factory instance.
     */
    public function __construct(
        private ViewFactory $view,
    ) {
    }

    /**
     * Displays a widget area.
     *
     * @param string $id Widget area ID.
     * @return void
     */
    public function display(string $id): void
    {
        if (! is_active_sidebar($id)) {
            return;
        }

        $this->view->print('partials.widget-area', ['id' => $id]);
    }
}
