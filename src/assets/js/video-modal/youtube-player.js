/**
 * Internal depdependencies
 */
import { loadScript } from './utils';
import Player from './player';

/**
 * YouTube player class
 */
export class YouTubePlayer extends Player {
	ready = false;

	/**
	 * Checks if the API is loaded already. This is needed in case of presence of other scripts using the same API (e.g.
	 * youtube-embed-plus-pro WordPress plugin, which loads YouTube API at boot).
	 *
	 * @return {boolean} If the API is available.
	 */
	checkApiLoaded() {
		return !!window.YT;
	}

	async apiLoadMethod() {
		return new Promise(async (resolve, reject) => {
			window.onYouTubeIframeAPIReady = resolve;

			try {
				await loadScript('//www.youtube.com/iframe_api');
			} catch (e) {
				reject();
			}
		});
	}

	async load(videoId) {
		try {
			await this.loadApi();
		} catch (e) {
			console.log('Could not load YouTube API.'); // eslint-disable-line no-console
		}

		if (this.player) {
			this.player.loadVideoById(videoId);
		} else {
			this.createPlayer(videoId);
		}
	}

	play() {
		if (this.ready) {
			this.player.playVideo();
		}
	}

	pause() {
		if (this.ready) {
			this.player.pauseVideo();
		}
	}

	createPlayer(videoId) {
		this.player = new window.YT.Player(this.modal.playerWrap, {
			width: '100%',
			height: '100%',
			events: {
				onReady: (e) => {
					this.ready = true;

					if (this.modal.isOpen) {
						e.target.playVideo();
					}
				},
			},
			videoId,
		});
	}

	destroy() {
		super.destroy();

		if (this.player) {
			this.player.destroy();
			this.player = null;
		}
	}
}
