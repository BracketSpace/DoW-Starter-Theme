/**
 * Extednal dependencies
 */
import '@types/youtube'; // eslint-disable-line import/no-unresolved

/**
 * Internal depdependencies
 */
import { loadScript } from './utils';
import Player from './player';

declare global {
	interface Window {
		onYouTubeIframeAPIReady: () => void;
	}
}

/**
 * YouTube player class
 */
export class YouTubePlayer extends Player {
	player?: YT.Player;
	ready: boolean = false;

	/**
	 * Checks if the API is loaded already. This is needed in case of presence of other scripts using the same API (e.g.
	 * youtube-embed-plus-pro WordPress plugin, which loads YouTube API at boot).
	 *
	 * @return If the API is available.
	 */
	checkApiLoaded() {
		return !!window.YT;
	}

	async apiLoadMethod() {
		return new Promise<void>(async (resolve, reject) => {
			window.onYouTubeIframeAPIReady = resolve;

			try {
				await loadScript('//www.youtube.com/iframe_api');
			} catch (e) {
				reject();
			}
		});
	}

	async load(videoId: string) {
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
		if (this.ready && this.player) {
			this.player.playVideo();
		}
	}

	pause() {
		if (this.ready && this.player) {
			this.player.pauseVideo();
		}
	}

	createPlayer(videoId: string) {
		this.player = new window.YT.Player(this.modal.playerWrap!, {
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
			this.player = undefined;
		}
	}
}
