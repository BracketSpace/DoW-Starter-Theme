<?php
/**
 * This file bootstraps the theme by creating the core Theme class instance.
 */

declare(strict_types=1);

use DoWStarterTheme\Bootstrap;

if (! defined('ABSPATH')) {
    return;
}

$prefixedAutoloader = __DIR__ . '/dependencies/autoload.php';
$baseAutoloader = __DIR__ . '/vendor/autoload.php';

if (! file_exists($prefixedAutoloader) || ! file_exists($baseAutoloader)) {
    $errorTitle = __('Autoloader not found.', 'dow-starter-theme');
    $errorDescription = __('You must run <code>composer install</code> from the theme directory.', 'dow-starter-theme');
    $errorMessage = "<h1>{$errorTitle}</h1><p>{$errorDescription}</p>";

    wp_die($errorMessage, $errorTitle);
}

require_once $prefixedAutoloader;
require_once $baseAutoloader;

new Bootstrap(__FILE__);
