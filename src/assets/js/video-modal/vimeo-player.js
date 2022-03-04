/**
 * Internal depdependencies
 */
import { loadScript } from './utils';
import Player from './player';

/**
 * Vimeo player class
 */
export class VimeoPlayer extends Player {
	/**
	 * Checks if the API is loaded already. This is needed in case of presence of other scripts using the same API (e.g.
	 * youtube-embed-plus-pro WordPress plugin, which loads YouTube API at boot).
	 *
	 * @return {boolean} If the API is available.
	 */
	checkApiLoaded() {
		return !!window.Vimeo;
	}

	async apiLoadMethod() {
		await loadScript('//player.vimeo.com/api/player.js');
	}

	async load(videoId) {
		try {
			await this.loadApi();
		} catch (e) {
			console.log('Could not load Vimeo API.'); // eslint-disable-line no-console
		}

		if (this.player) {
			this.player.destroy();
		}

		this.createPlayer(videoId);
	}

	createPlayer(videoId) {
		this.player = new window.Vimeo.Player(this.modal.playerWrap, {
			id: videoId,
			width: '100%',
		});

		this.player.ready().then(() => this.player.play());
	}

	play() {
		if (this.player) {
			this.player.play();
		}
	}

	pause() {
		if (this.player) {
			this.player.pause();
		}
	}

	destroy() {
		super.destroy();

		if (this.player) {
			this.player.destroy();
			this.player = null;
		}
	}
}
