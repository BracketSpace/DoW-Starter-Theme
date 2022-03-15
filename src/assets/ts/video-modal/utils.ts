/**
 * Loads script. Returns Promise, which gets resolved in `onload` event callback and rejected in case of an error.
 *
 * @param  src Script source URL.
 */
export const loadScript = (src: string) =>
	new Promise((resolve, reject) => {
		const script = document.createElement('script');
		script.async = true;
		script.src = src;
		script.onload = resolve;
		script.onerror = reject;

		document.body.append(script);
	});

/**
 * Video services regex patterns.
 */
export const patterns = {
	youtube:
		/(?:http(?:s)??:\/\/)?(?:www\.)?(?:(?:youtube\.com\/watch\?v=)|(?:youtu\.be\/))([a-z0-9-_]+)/i,
	wistia: /(?:https?:\/\/(?:.+)?(?:wistia\.com|wi\.st)\/(?:medias|embed)\/)(.*)/i,
	vimeo: /(?:https?:\/\/(?:[w]+\.)*vimeo\.com(?:[/w:]*(?:\/videos)?)?\/([0-9]+)[^s]*)/i,
};

/**
 * Gets video ID from given URL based on given provider name.
 *
 * @param  url      Video URL.
 * @param  provider Video provider name.
 * @return         Video ID.
 */
export const getVideoId = (url: string, provider?: string) => {
	if (provider && patterns[provider as keyof typeof patterns]) {
		const matches = url.match(patterns[provider as keyof typeof patterns]);

		if (matches && matches[1]) {
			return matches[1];
		}
	}

	return false;
};
