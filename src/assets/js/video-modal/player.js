/**
 * Base player class to be extened by dedicated classes for each video provider.
 */
export default class {
	apiLoaded = false;

	constructor(videoId = null, modal) {
		this.modal = modal;

		if (videoId) {
			this.loadVideo(videoId);
		}
	}

	async loadApi() {
		if (!this.apiLoaded) {
			this.apiLoaded = this.checkApiLoaded();
		}

		if (this.apiLoaded) {
			return true;
		}

		if (!this.apiLoadMethod) {
			throw new Error('No API method defined.');
		}

		await this.apiLoadMethod();

		this.apiLoaded = true;
	}

	async loadVideo(videoId) {
		if (this.lastVideoId === videoId) {
			this.play();
		} else {
			this.lastVideoId = videoId;
			await this.load(videoId);
			this.modal.setReady();
		}
	}

	/**
	 * Checks if the API is loaded already. This method can be implemented in subclasses and should return `true` if the
	 * API is available, `false` otherwise. This is needed in case of presence of other scripts using the same API (e.g.
	 * youtube-embed-plus-pro WordPress plugin, which loads YouTube API at boot).
	 *
	 * @return {boolean} If the API is available.
	 */
	checkApiLoaded() {
		return false;
	}

	apiLoadMethod() {}

	load() {}

	play() {}

	pause() {}

	destroy() {
		this.lastVideoId = null;
	}
}
