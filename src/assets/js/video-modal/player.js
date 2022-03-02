export default class {
	apiLoaded = false;

	constructor(videoId = null, modal) {
		this.modal = modal;

		if (videoId) {
			this.loadVideo(videoId);
		}
	}

	async loadApi() {
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

	apiLoadMethod() {}

	load() {}

	play() {}

	pause() {}

	destroy() {
		this.lastVideoId = null;
	}
}
