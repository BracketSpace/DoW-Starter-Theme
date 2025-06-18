<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Helpers;

use WP_Post;

/**
 * Image helper class
 */
class Image
{
    /**
     * Get attachment id
     *
     * @param mixed $attachment Attachment data.
     * @return int|null
     */
    public function getId(mixed $attachment): ?int
    {
        if (is_object($attachment) && $attachment instanceof WP_Post) {
            $attachment = $attachment->ID;
        }

        return is_numeric($attachment) ? (int)$attachment : null;
    }

    /**
     * Get attachment image.
     *
     * @param mixed  $attachment Attachment data.
     * @param string $size       Image size.
     * @return string
     */
    public function get(mixed $attachment, string $size = 'full'): string
    {
        $attachmentId = $this->getId($attachment);

        return is_int($attachmentId) ? wp_get_attachment_image($attachmentId, $size) : '';
    }

    /**
     * Print attachment image.
     *
     * @param mixed  $attachment Attachment data.
     * @param string $size       Image size.
     * @return void
     */
    public function print(mixed $attachment, string $size = 'full'): void
    {
        echo $this->get($attachment, $size);
    }

    /**
     * Get attachment image from ACF.
     *
     * @param string $fieldName Field name.
     * @param string $size      Image size.
     * @return string
     */
    public function getAcf(string $fieldName = 'image', string $size = 'full'): string
    {
        $image = get_field($fieldName);

        if (is_null($image)) {
            $image = get_sub_field($fieldName);
        }

        return $this->get($image, $size);
    }

    /**
     * Echo attachment image from ACF
     *
     * @param string $fieldName Field name.
     * @param string $size      Image size.
     * @return void
     */
    public function printAcf(string $fieldName = 'image', string $size = 'full'): void
    {
        echo $this->getAcf($fieldName, $size);
    }

    /**
     * Get retina ready attachment image
     *
     * @param mixed  $attachment Attachment data.
     * @param string $size       Image size.
     * @return string
     */
    public function getRetina(mixed $attachment, string $size = 'full'): string
    {
        $attachmentId = $this->getId($attachment);

        if (! is_int($attachmentId)) {
            return '';
        }

        $image = $this->get($attachment, $size);
        $src = wp_get_attachment_image_src($attachmentId, $size);

        if (! is_array($src)) {
            return '';
        }

        $width = round($src[1] / 2);
        $height = round($src[2] / 2);

        $image = (string)preg_replace(
            [
                '/width=\"([0-9]+)\"/',
                '/height=\"([0-9]+)\"/',
            ],
            [
                "width=\"{$width}\"",
                "height=\"{$height}\"",
            ],
            $image
        );

        return $image;
    }

    /**
     * Print retina ready attachment image.
     *
     * @param mixed  $attachment Attachment data.
     * @param string $size       Image size.
     * @return void
     */
    public function printRetina(mixed $attachment, string $size = 'full'): void
    {
        echo $this->getRetina($attachment, $size = 'full');
    }
}
