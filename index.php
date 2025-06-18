<?php
/**
 * Index
 */

declare(strict_types=1);

use DoWStarterTheme\Bootstrap as StarterTheme;
use DoWStarterTheme\Common\View\Layout;

?><!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div class="site-container">
        <?php StarterTheme::getContainer()->get(Layout::class)->get(); ?>
    </div>

    <?php wp_footer(); ?>
  </body>
</html>
