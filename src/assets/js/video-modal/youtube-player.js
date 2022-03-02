import Player from './player';
import { loadScript } from './utils';

/* global YT */

export class YouTubePlayer extends Player {
	ready = false;

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
		this.player = new YT.Player(this.modal.playerWrap, {
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
