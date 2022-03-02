<?php

declare(strict_types=1);

namespace DoWStarterTheme\Integration;

/**
 * Posts helper class
 */
class YouTube
{
	/**
	 * YouTube API Key
	 */
	private string $apiKey;

	/**
	 * YouTube API v3 base URL
	 */
	private string $baseUrl = 'https://www.googleapis.com/youtube/v3/';

	/**
	 * Cached channel info
	 *
	 * @var array<string, mixed>
	 */
	private array $channels = [];

	/**
	 * Cached videos
	 *
	 * @var array<string, mixed>
	 */
	private $videos = [];

	/**
	 * Max results count
	 */
	private int $maxResults = 9;

	/**
	 * Page token
	 *
	 * @var string|null
	 */
	private $pageToken = null;

	/**
	 * Response data.
	 *
	 * @var \stdClass
	 */
	private $data;

	/**
	 * Creates class instance with YouTube API key.
	 *
	 * @param string $apiKey YouTube API Key.
	 */
	protected function __construct(string $apiKey)
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * Performs API call.
	 *
	 * @param string               $path API path.
	 * @param array<string, mixed> $args API call arga.
	 * @return \stdClass|false
	 */
	public function apiCall(string $path, array $args)
	{
		$response = wp_remote_get($this->getUrl($path, $args));

		if (is_wp_error($response)) {
			return false;
		}

		return json_decode(wp_remote_retrieve_body($response));
	}

	/**
	 * Fetches singel item.
	 *
	 * @param string               $path API path.
	 * @param array<string, mixed> $args API call arga.
	 * @return \stdClass|false
	 */
	public function fetchItem(string $path, array $args)
	{
		$data = $this->apiCall($path, $args);

		if (is_object($data) && isset($data->items) && $data->items) {
			return $data->items[0];
		}

		return false;
	}

	/**
	 * Gets YouTube video.
	 *
	 * @param string $id   YouTube Video ID.
	 * @param string $part Part to get.
	 * @return \stdClass|false
	 */
	public function getVideo(string $id, string $part = 'snippet')
	{
		$args = [
			'part' => $part,
			'key' => $this->apiKey,
			'id' => $id,
		];

		return $this->fetchItem('videos', $args);
	}

	/**
	 * Gets YouTube channel information by channel id or username
	 *
	 * @param string $key   Whether to use id or username.
	 * @param string $value Channel ID or Username.
	 * @param string $part  Part to get.
	 * @return \stdClass|false
	 */
	public function getChannelBy(string $key, string $value, string $part = 'snippet')
	{
		$args = [
			'part' => $part,
			'key' => $this->apiKey,
		];

		if ($key === 'username') {
			$key = 'forUsername';
		}

		$args[$key] = $value;

		return $this->fetchItem('channels', $args);
	}

	/**
	 * Gets YouTube playlist items.
	 *
	 * @param string $playlistId Playlist ID.
	 * @param string $part       Part to get.
	 * @return \stdClass|false
	 */
	public function getPlaylistItems(string $playlistId, string $part = 'snippet,contentDetails')
	{
		$args = [
			'part' => $part,
			'key' => $this->apiKey,
			'playlistId' => $playlistId,
			'maxResults' => $this->maxResults,
			'pageToken' => $this->pageToken,
		];

		$data = $this->apiCall('playlistItems', $args);

		if (is_object($data) && isset($data->items)) {
			$this->data = $data;

			return $data->items;
		}

		return false;
	}

	/**
	 * Gets API URL for a resource.
	 *
	 * @param string               $resource Resource name.
	 * @param array<string, mixed> $args     URL args.
	 * @return string
	 */
	public function getUrl(string $resource, array $args): string
	{
		return add_query_arg($args, $this->baseUrl . $resource);
	}

	/**
	 * Gets YouTube channel information by channel id or username
	 *
	 * @param string $channelId Channel ID or username.
	 * @return \stdClass|false
	 */
	public function getChannelInfo(string $channelId)
	{
		if (! array_key_exists($channelId, $this->channels)) {
			$channel = $this->getChannelBy('id', $channelId, 'statistics,brandingSettings');

			if ($channel === false) {
				$channel = $this->getChannelBy('username', $channelId, 'statistics,brandingSettings');
			}

			$this->channels[$channelId] = $channel;
		}

		return $this->channels[$channelId];
	}

	/**
	 * Gets YouTube channel information by channel id or username
	 *
	 * @param string $channelId Channel ID or username.
	 * @return \stdClass|false
	 */
	public function getChannelItems(string $channelId)
	{
		$info = $this->getChannelBy('id', $channelId, 'contentDetails');

		if ($info === false) {
			$info = $this->getChannelBy('username', $channelId, 'contentDetails');
		}

		if (is_object($info) && isset($info->contentDetails->relatedPlaylists->uploads)) {
			return $this->getPlaylistItems($info->contentDetails->relatedPlaylists->uploads);
		}

		return false;
	}

	/**
	 * Gets YouTube channel subscriber count
	 *
	 * @param string $channelId Channel ID or username.
	 * @return string|null
	 */
	public function getSubscriberCount(string $channelId): ?string
	{
		$info = $this->getChannelInfo($channelId);

		if ($info !== false) {
			return $info->statistics->subscriberCount;
		}

		return null;
	}

	/**
	 * Gets YouTube channel subscribe URL
	 *
	 * @param string $channelId Channel ID or username.
	 * @return string|null
	 */
	public function getSubscribeURL(string $channelId): ?string
	{
		$info = $this->getChannelInfo($channelId);

		if ($info !== false) {
			return sprintf('https://www.youtube.com/channel/%s?sub_confirmation=1', $info->id);
		}

		return null;
	}

	/**
	 * Gets YouTube channel featured video.
	 *
	 * @param string $channelId Channel ID or username.
	 * @return \stdClass|false
	 */
	public function getFeaturedVideo(string $channelId)
	{
		$info = $this->getChannelInfo($channelId);

		if ($info !== false && isset($info->brandingSettings)) {
			$videoId = $info->brandingSettings->channel->unsubscribedTrailer;

			$video = $this->getVideo($videoId);

			if (is_object($video) && isset($video->snippet)) {
				$video->snippet->id = $videoId;
				$video->snippet->url = "https://www.youtube.com/watch?v={$videoId}";

				return $video->snippet;
			}
		}

		return false;
	}

	/**
	 * Gets YouTube videos
	 *
	 * @param string               $playlistId Playlist or channel ID.
	 * @param array<string, mixed> $args       Arguments.
	 * @return string
	 */
	public function getVideos(string $playlistId, array $args = []): string
	{
		$cacheKey = md5($playlistId . wp_json_encode($args));

		if (! array_key_exists($cacheKey, $this->videos)) {
			if (array_key_exists('maxResults', $args)) {
				$this->maxResults = $args['maxResults'];
			}

			if (array_key_exists('pageToken', $args)) {
				$this->pageToken = $args['pageToken'];
			}

			$videos = $this->getPlaylistItems($playlistId);

			if ($videos === false) {
				$videos = $this->getChannelItems($playlistId);
			}

			$this->videos[$cacheKey] = $videos;
		}

		return $this->videos[$cacheKey];
	}

	/**
	 * Returns previous page token or null if no previous page available.
	 *
	 * @return string|null Previous page token.
	 */
	public function getPrevPageToken(): ?string
	{
		return isset($this->data) && $this->data->prevPageToken ? $this->data->prevPageToken : null;
	}

	/**
	 * Returns next page token or null if no previous page available.
	 *
	 * @return string|null Next page token.
	 */
	public function getNextPageToken(): ?string
	{
		return isset($this->data) && $this->data->nextPageToken ? $this->data->nextPageToken : null;
	}
}
