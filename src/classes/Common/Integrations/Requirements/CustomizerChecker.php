<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Integrations\Requirements;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Customizer\Customizer;
use DoWStarterTheme\Deps\Micropackage\Requirements\Abstracts\Checker;
use Kirki\Compatibility\Kirki;

/**
 * Customizer checker class
 */
class CustomizerChecker extends Checker
{
    /**
     * Checker name
     *
     * @var string
     */
    protected $name = 'customizer';

    /**
     * Class constructor.
     *
     * @param  Config $config Config instance.
     */
    public function __construct(
        private Config $config
    ) {
    }

    /**
     * Checks if assest are built.
     *
     * @param  mixed $enabled Whether checker is enabled or not.
     * @return void
     */
    public function check($enabled)
    {
        if ($enabled !== true) {
            return;
        }

        if (! in_array(Customizer::class, $this->config->get('app.components'), true)) {
            return;
        }

        if (class_exists(Kirki::class)) {
            return;
        }

        $this->add_error('Kirki plugin is required in order to enable Customizer feature.');
    }
}
