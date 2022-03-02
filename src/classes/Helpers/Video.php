<?php

declare(strict_types=1);

namespace DoWStarterTheme\Helpers;

/**
 * Video helper class
 */
class Video
{
	/**
	 * Video URL patterns for each available provider.
	 *
	 * @var array<string>
	 */
	protected static array $urlPatterns = [
		'youtube' => '%(?:http(?:s)??://)?(?:www\.)?(?:(?:youtube\.com/watch\?v=)|(?:youtu.be/))([a-z0-9\-_]+)%i',
		'wistia' => '%(?:https?:\/\/(?:.+)?(?:wistia\.com|wi\.st)\/(?:medias|embed)\/)(.*)%i',
		'vimeo' => '%(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w:]*(?:\/videos)?)?\/([0-9]+)[^\s]*)%i',
	];

	/**
	 * Gets video id from URL.
	 *
	 * @param  string $url      Video URL.
	 * @param  string $provider Video Provider. Possible options are `youtube`, 'wistia' and `vimeo`.
	 * @return string
	 */
	public static function getId(string $url, ?string $provider = null): ?string
	{
		if (!is_string($provider)) {
			$provider = static::getProvider($url);
		}

		if ($provider === null) {
			return null;
		}

		if (
			array_key_exists($provider, static::$urlPatterns) &&
			preg_match(static::$urlPatterns[$provider], $url, $match) === 1
		) {
			return $match[1];
		}

		return null;
	}

	/**
	 * Prints video id for given URL
	 *
	 * @param  string $url      Video URL.
	 * @param  string $provider Video Provider. Possible options are `youtube`, 'wistia' and `vimeo`.
	 * @return void
	 */
	public static function theId(string $url, ?string $provider = null): void
	{
		echo static::getId($url, $provider);
	}

	/**
	 * Gets video provider from url
	 *
	 * @param  string $url Video URL.
	 * @return string|null
	 */
	public static function getProvider(string $url): ?string
	{
		if ((bool)strpos($url, 'youtu')) {
			return 'youtube';
		}

		if ((bool)strpos($url, 'wistia') || (bool)strpos($url, 'wi.st')) {
			return 'wistia';
		}

		if ((bool)strpos($url, 'vimeo')) {
			return 'vimeo';
		}

		return null;
	}

	/**
	 * Prints video provider for given URL.
	 *
	 * @param  string $url Video URL.
	 * @return void
	 */
	public static function theProvider(string $url): void
	{
		echo static::getProvider($url);
	}
}
