<?php

declare(strict_types=1);

namespace DoWStarterTheme\Features;

use DoWStarterTheme\Deps\Micropackage\DocHooks\HookTrait;

/**
 * SVG Support class
 */
class SVGSupport
{
	use HookTrait;

	/**
	 * Adds styles necessary for media library display.
	 *
	 * @action admin_enqueue_scripts
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function enqueueScripts()
	{
		wp_add_inline_style('wp-admin', ".media .media-icon img[src$='.svg'] { width: auto; height: auto; }");
		wp_add_inline_style('wp-admin', "#postimagediv .inside img[src$='.svg'] { width: 100%; height: auto; }");
	}

	/**
	 * Adds dimensions and orientation for SVG to attachment ajax data.
	 *
	 * @filter wp_prepare_attachment_for_js
	 *
	 * @since  2.0.0
	 * @param  array<string, mixed> $response   Array of prepared attachment data.
	 * @param  \WP_Post             $attachment Attachment object.
	 * @return array<string, mixed>
	 */
	public function prepareAttachmentForJs($response, $attachment): array
	{
		if ($response['mime'] !== 'image/svg+xml' || count($response['sizes'] ?? []) === 0) {
			return $response;
		}

		$file = get_attached_file($attachment->ID);

		if (!is_string($file)) {
			return $response;
		}

		$dimensions = $this->getDimenstions($file);
		$response['sizes'] = [
			'full' => [
				'url' => $response['url'],
				'width' => $dimensions['width'],
				'height' => $dimensions['height'],
				'orientation' => $dimensions['width'] > $dimensions['height'] ? 'landscape' : 'portrait',
			],
		];

		return $response;
	}

	/**
	 * Adds SVG upload support
	 *
	 * @filter upload_mimes
	 *
	 * @param  array<string, mixed> $mimes Mime types.
	 * @return array<string, mixed>
	 */
	public function uploadMimes(array $mimes)
	{
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Parses width and height from an SVG file.
	 *
	 * @param  string $svgPath SVG Path.
	 * @return array{width: int, height: int}
	 */
	protected function getDimenstions($svgPath)
	{
		$width = 0;
		$height = 0;
		$svg = simplexml_load_file($svgPath, 'SimpleXMLElement', LIBXML_NOWARNING);

		if ($svg !== false) {
			$attributes = $svg->attributes();

			if ($attributes !== null) {
				$width = (int)$attributes->width;
				$height = (int)$attributes->height;
			}
		}

		return compact('width', 'height');
	}
}
