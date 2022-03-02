export const loadScript = (src) =>
	new Promise((resolve, reject) => {
		const script = document.createElement('script');
		script.async = true;
		script.src = src;
		script.onload = resolve;
		script.onerror = reject;

		document.body.append(script);
	});

export const patterns = {
	youtube: /(?:http(?:s)??:\/\/)?(?:www\.)?(?:(?:youtube\.com\/watch\?v=)|(?:youtu\.be\/))([a-z0-9-_]+)/i,
	wistia: /(?:https?:\/\/(?:.+)?(?:wistia\.com|wi\.st)\/(?:medias|embed)\/)(.*)/i,
	vimeo: /(?:https?:\/\/(?:[w]+\.)*vimeo\.com(?:[/w:]*(?:\/videos)?)?\/([0-9]+)[^s]*)/i,
};

export const getVideoId = (url, provider) => {
	if (patterns[provider]) {
		const matches = url.match(patterns[provider]);

		if (matches && matches[1]) {
			return matches[1];
		}
	}

	return false;
};
