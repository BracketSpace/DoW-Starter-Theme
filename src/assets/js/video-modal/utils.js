/**
 * Loads script. Returns Promise, which gets resolved in `onload` event callback and rejected in case of an error.
 *
 * @param {string} src Script source URL.
 * @return {Promise}   Promise resolving on success, rejecting in case of an error.
 */
export const loadScript = (src) =>
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
 *
 * @type {Object}
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
 * @param {string} url      Video URL.
 * @param {string} provider Video provider name.
 * @return {string}         Video ID.
 */
export const getVideoId = (url, provider) => {
	if (patterns[provider]) {
		const matches = url.match(patterns[provider]);

		if (matches && matches[1]) {
			return matches[1];
		}
	}

	return false;
};
