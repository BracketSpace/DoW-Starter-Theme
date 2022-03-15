/**
 * External depdependencies
 */
import { type Player as BaseVimeoPlayer } from '@vimeo/player'; // eslint-disable-line import/no-unresolved

/**
 * Internal depdependencies
 */
import { loadScript } from './utils';
import Player from './player';

declare global {
	interface Window {
		Vimeo?: {
			Player: typeof BaseVimeoPlayer;
		};
	}
}

/**
 * Vimeo player class
 */
export class VimeoPlayer extends Player {
	player?: BaseVimeoPlayer;

	/**
	 * Checks if the API is loaded already. This is needed in case of presence of other scripts using the same API (e.g.
	 * youtube-embed-plus-pro WordPress plugin, which loads YouTube API at boot).
	 *
	 * @return If the API is available.
	 */
	checkApiLoaded() {
		return !!window.Vimeo;
	}

	async apiLoadMethod() {
		await loadScript('//player.vimeo.com/api/player.js');
	}

	async load(videoId: string) {
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

	createPlayer(videoId: string) {
		this.player = new window.Vimeo!.Player(this.modal.playerWrap!, {
			id: videoId as any,
			width: '100%' as any,
		});

		this.player.ready().then(() => {
			if (this.player) {
				this.player.play();
			}
		});
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
			this.player = undefined;
		}
	}
}
