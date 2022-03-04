/**
 * Internal depdependencies
 */
import { loadScript } from './utils';
import Player from './player';

export class WistiaPlayer extends Player {
	async apiLoadMethod() {
		await loadScript('//fast.wistia.net/assets/external/E-v1.js');
	}

	constructor(videoId, modal) {
		super(videoId, modal);

		if (!window._wq) {
			window._wq = [];
		}

		window._wq.push({
			id: '_all',
			onReady: (video) => this.onReady(video),
		});
	}

	async load(videoId) {
		try {
			await this.loadApi();
		} catch (e) {
			console.log('Could not load Wistia API.'); // eslint-disable-line no-console
		}

		this.createPlayer(videoId);
	}

	play() {
		if (this.video) {
			this.video.play();
		}
	}

	pause() {
		if (this.video) {
			this.video.pause();
		}
	}

	onReady(video) {
		this.video = video;

		this.play();
	}

	createPlayer(id) {
		this.playerElement = document.createElement('div');

		this.playerElement.classList.add('wistia_embed', `wistia_async_${id}`);

		this.modal.playerWrap.append(this.playerElement);
	}

	destroy() {
		super.destroy();

		if (this.video) {
			this.video.remove();
			this.playerElement.remove();
		}
	}
}
