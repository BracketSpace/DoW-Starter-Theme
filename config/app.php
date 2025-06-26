<?php

declare(strict_types=1);

// phpcs:disable SlevomatCodingStandard.Namespaces.UseFromSameNamespace.UseFromSameNamespace

use DoWStarterTheme\Common\{
    Assets,
    Customizer,
    Helpers,
    Hooks,
    Integrations as CommonIntegrations,
    Managers as CommonManagers,
    Menu,
    Shortcodes,
    View,
    Widget,
    WordPress,
};
use DoWStarterTheme\Features\{
    BlockSpacing,
    ReusableContent,
};

return [
    'providers' => [
        View\Finder\ViewFinderProvider::class,
    ],

    'components' => [
        Hooks\I18n::class,
        Hooks\ImageSizes::class,
        Hooks\SVGSupport::class,
        Hooks\TemplateFilters::class,
        Hooks\ThemeSupport::class,

        CommonManagers\ShortcodeManager::class,

        CommonIntegrations\ACF::class,
        CommonIntegrations\ACFBlockCreator::class,

        // Assets
        Assets\MainScript::class,
        Assets\MainStyle::class,

        // Block Editor Integration
        CommonIntegrations\Editor\Editor::class,

        Customizer\Customizer::class,
        ReusableContent\ReusableContentHooks::class,

        // Block Spacing
        BlockSpacing\BlockSpacingHooks::class,

        // Menu
        Menu\MenuRegistrar::class,

        // Post Type
        WordPress\PostType\PostTypeManager::class,

        // Taxonomy
        WordPress\Taxonomy\TaxonomyManager::class,

        // Widgets
        Widget\WidgetAreaManager::class,
        Widget\WidgetHooks::class,
        Widget\WidgetManager::class,
    ],

    'initialize' => [
        Widget\Widget::class,
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
