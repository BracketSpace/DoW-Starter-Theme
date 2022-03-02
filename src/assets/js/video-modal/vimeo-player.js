/* global Vimeo */

import Player from './player';
import { loadScript } from './utils';

export class VimeoPlayer extends Player {
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
		this.player = new Vimeo.Player(this.modal.playerWrap, {
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
