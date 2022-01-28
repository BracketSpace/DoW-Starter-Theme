<?php
/**
 * This file bootstraps the theme by creating the core Theme class instance.
 *
 * phpcs:disable NeutronStandard.Functions.VariableFunctions.VariableFunction
 */

declare(strict_types=1);

namespace DoWStarterTheme;

use DoWStarterTheme\Core\Config;
use DoWStarterTheme\Core\Theme;
use DoWStarterTheme\Factories\Filesystem;
use DoWStarterTheme\Requirements\AssetsChecker;
use Micropackage\Requirements\Requirements;

/**
 * Composer autoload file
 */
$dowstAutoloader = __DIR__ . '/vendor/autoload.php';

if (!file_exists($dowstAutoloader)) {
    // Composer autoload file does not exist.
    $dowstTitle = __('Autoloader not found.', 'dow-starter-theme');
    $dowstDescription = __('You must run <code>composer install</code> from the theme directory.', 'dow-starter-theme');
    $dowstMessage = "<h1>{$dowstTitle}</h1><p>{$dowstDescription}</p>";

    wp_die($dowstMessage, $dowstTitle);
}

// Require autoloader.
require_once $dowstAutoloader;

// Create Requirements instance.
$dowstRequirements = new Requirements(
    'DoW Starter Theme',
    [
        'assets' => true,
        'dochooks' => true,
        'php' => '7.4',
        'php_extensions' => ['SimpleXML'],
        'plugins' => [
            [
                'file' => 'advanced-custom-fields-pro/acf.php',
                'name' => 'Advanced Custom Fields Pro',
            ],
        ],
        'wp' => '5.8',
    ]
);

$dowstRequirements->register_checker(AssetsChecker::class);

if (!$dowstRequirements->satisfied()) {
    $dowstMessage = sprintf(
        /* Translators: %s is a theme name. */
        __('The theme: %s cannot be activated.', 'dow-starter-theme'),
        '<strong>DoW Starter Theme</strong>'
    );

    if (is_admin()) {
        $dowstRequirements->print_notice($dowstMessage);
    } else {
        $dowstRequirements->kill($dowstMessage);
    }

    return;
}

// Create root Filesystem instance.
$dowstFs = Filesystem::get(__DIR__, 'root');

// Create core class instance.
$dowstTheme = Theme::get($dowstFs);

// Add WordPress actions and filters.
$dowstTheme->add_hooks();

// Bootstrap the Theme, create class instances.
$dowstTheme->bootstrap(Config::get('classes.general'));

// Add widgets to be registered.
$dowstTheme->addWidgets(Config::get('classes.widgets'));

// Add theme support.
$dowstTheme->addThemeSupport(Config::get('theme-support'));

// Add image sizes.
$dowstTheme->addImageSizes(Config::get('image-sizes'));
