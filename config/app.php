<?php

declare(strict_types=1);

use DoWStarterTheme\Common\View\Finder\ViewFinderProvider;
use DoWStarterTheme\Common\{
    Abstracts,
    Core,
    Customizer,
    Helpers,
    Integrations as CommonIntegrations,
    Managers as CommonManagers,
    View,
};
use DoWStarterTheme\Features\ReusableContent;

return [
    'providers' => [
        ViewFinderProvider::class,
    ],

    'components' => [
        Core\BlockSpacing::class,
        Core\I18n::class,
        Core\ImageSizes::class,
        Core\Menu::class,
        Core\SVGSupport::class,
        Core\TemplateFilters::class,
        Core\ThemeSupport::class,
        Core\Widgets::class,

        CommonManagers\PostTypeManager::class,
        CommonManagers\ShortcodeManager::class,
        CommonManagers\TaxonomyManager::class,
        CommonManagers\WidgetAreaManager::class,
        CommonManagers\WidgetManager::class,

        CommonIntegrations\ACF::class,
        CommonIntegrations\ACFBlockCreator::class,
        CommonIntegrations\Editor::class,

        Customizer\Customizer::class,
        ReusableContent\ReusableContentHooks::class,
    ],

    'initialize' => [
        Abstracts\Widget::class,
        Helpers\CSSGenerator::class,
    ],

    'post-types' => [
        ReusableContent\ReusableContentPostType::class,
    ],

    'taxonomies' => [
    ],

    'shortcodes' => [
        Shortcodes\DateShortcode::class,
    ],
];
