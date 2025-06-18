<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Hooks;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Common\Config\Config;

/**
 * Theme image sizes class.
 */
class ImageSizes implements Hookable
{
    /**
     * Class constructor.
     *
     * @param Config $config Config instance.
     */
    public function __construct(
        private Config $config,
    ) {
    }

    /**
     * Setups image sizes.
     *
     * @action after_setup_theme
     *
     * @return void
     */
    public function setupImageSizes(): void
    {
        foreach ($this->getImageSizes() as $size => $config) {
            $args = [$size];

            if (isset($config['width']) && isset($config['height'])) {
                $args[] = $config['width'];
                $args[] = $config['height'];

                if (isset($config['crop'])) {
                    $args[] = $config['crop'];
                }
            }

            add_image_size(...$args);
        }
    }

    /**
     * Gets the list of image sizes.
     *
     * @return  array<string, mixed>
     */
    private function getImageSizes(): array
    {
        $imageSizes = $this->config->get('image-sizes');

        return is_array($imageSizes) ? $imageSizes : [];
    }
}
