<?php
/**
 * This file bootstraps the theme by creating the core Theme class instance.
 *
 * @package DoW Starter Theme
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL-3.0-or-later
 *
 * phpcs:disable NeutronStandard.Functions.VariableFunctions.VariableFunction
 */

declare(strict_types=1);

namespace DoWStarterTheme;

use DoWStarterTheme\Core\Config;
use DoWStarterTheme\Core\Theme;
use Micropackage\Filesystem\Filesystem;
use Micropackage\Requirements\Requirements;

/**
 * Helper function for displaying errors
 *
 * @param  string $message Error message.
 * @param  string $title   Error title.
 * @return void
 */
$dowstError = static function ( $message, $title ): void {
    $message = "<h1>{$title}</h1><p>{$message}</p>";
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    wp_die($message, $title);
};

/**
 * Composer autoload file
 */
$dowstAutoloader = __DIR__ . '/vendor/autoload.php';

if (!file_exists($dowstAutoloader)) {
    // Composer autoload file does not exist.
    $dowstError(
        __('You must run <code>composer install</code> from the theme directory.', 'dow-starter-theme'),
        __('Autoloader not found.', 'dow-starter-theme')
    );
}

// Require autoloader.
require_once $dowstAutoloader;

// Create Requirements instance.
$dowstRequirements = new Requirements(
    'DoW Starter Theme',
    [
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

if (!$dowstRequirements->satisfied()) {
    $dowstRequirements->print_notice();

    return;
}

// Create Filesystem instance.
$dowstFs = new Filesystem(__DIR__);

if (!$dowstFs->exists('assets/dist')) {
    // Assets build dir does not exist.
    $dowstError(
        __('You must run <code>yarn install & yarn build</code> from the theme directory.', 'dow-starter-theme'),
        __('Assets build directory not found.', 'dow-starter-theme')
    );
}

// Create core class instance.
$dowstTheme = Theme::get($dowstFs);

// Bootstrap the Theme, create class instances.
$dowstTheme->bootstrap(Config::get('bootstrap.classes'));

// Add widgets to be registered.
$dowstTheme->addWidgets(Config::get('bootstrap.widgets'));

// Add theme support.
$dowstTheme->addThemeSupport(Config::get('theme-support'));

// Add image sizes.
$dowstTheme->addImageSizes(Config::get('image-sizes'));
