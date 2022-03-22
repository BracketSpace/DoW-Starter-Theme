<?php

declare(strict_types=1);

namespace DoWStarterTheme\Helpers;

/**
 * Image helper class
 */
class Image
{
	/**
	 * Get attachment id
	 *
	 * @param  \WP_Post|int $attachment Attachment object or id.
	 * @return int|false Attachment id.
	 */
	public static function getId($attachment)
	{
		if (is_object($attachment) && isset($attachment->ID)) {
			$attachment = $attachment->ID;
		}

		return is_numeric($attachment) ? $attachment : false;
	}

	/**
	 * Get attachment image
	 *
	 * @param int|\WP_Post $attachment Attachment object or ID.
	 * @param string       $size       Image size.
	 * @return string HTML img markup.
	 */
	public static function get($attachment, $size = 'full'): string
	{
		return wp_get_attachment_image(
			(int)static::getId($attachment),
			$size
		);
	}

	/**
	 * Print attachment image
	 *
	 * @param int|\WP_Post $attachment Attachment object or ID.
	 * @param string       $size       Image size.
	 * @return void
	 */
	public static function print($attachment, $size = 'full'): void
	{
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo static::get($attachment, $size);
	}

	/**
	 * Get attachment image from ACF
	 *
	 * @param string $fieldName ACF field name.
	 * @param string $size      Image size.
	 * @return string HTML img markup.
	 */
	public static function getAcf($fieldName = 'image', $size = 'full'): string
	{
		$image = get_field($fieldName);

		if (is_null($image)) {
			$image = get_sub_field($fieldName);
		}

		return static::get($image, $size);
	}

	/**
	 * Echo attachment image from ACF
	 *
	 * @param string $fieldName ACF field name.
	 * @param string $size      Image size.
	 * @return void
	 */
	public static function printAcf($fieldName = 'image', $size = 'full'): void
	{
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo static::getAcf($fieldName, $size);
	}

	/**
	 * Get retina ready attachment image
	 *
	 * @param int|\WP_Post $attachment Attachment object or ID.
	 * @param string       $size       Image size.
	 * @return string|null
	 */
	public static function getRetina($attachment, $size = 'full'): ?string
	{
		$attachmentId = static::getId($attachment);

		if (!is_int($attachmentId)) {
			return null;
		}

		$image = static::get($attachment, $size);
		$src = wp_get_attachment_image_src($attachmentId, $size);

		if (!is_array($src)) {
			return null;
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
	 * Print retina ready attachment image
	 *
	 * @param  int|\WP_Post $attachment Arrachment object or ID.
	 * @param  string       $size       Image size.
	 * @return void
	 */
	public static function printRetina($attachment, $size = 'full'): void
	{
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo static::getRetina($attachment, $size = 'full');
	}
}
