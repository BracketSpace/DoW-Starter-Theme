/**
 * Internal depdependencies
 */
import { loadScript } from './utils';
import Player from './player';
import VideoModal from './modal';

declare global {
	interface Window {
		_wq?: Array<Record<string, any>>;
	}
}

export class WistiaPlayer extends Player {
	video: any; // TODO: write types for Wistia video object.
	playerElement?: HTMLDivElement;

	async apiLoadMethod() {
		await loadScript('//fast.wistia.net/assets/external/E-v1.js');
	}

	constructor(videoId: string, modal: VideoModal) {
		super(videoId, modal);

		if (!window._wq) {
			window._wq = [];
		}

		window._wq.push({
			id: '_all',
			onReady: (video: any) => this.onReady(video),
		});
	}

	async load(videoId: string) {
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

	onReady(video: any) {
		this.video = video;

		this.play();
	}

	createPlayer(id: string) {
		this.playerElement = document.createElement('div');

		this.playerElement.classList.add('wistia_embed', `wistia_async_${id}`);

		this.modal.playerWrap!.append(this.playerElement);
	}

	destroy() {
		super.destroy();

		if (this.video) {
			this.video.remove();

			if (this.playerElement) {
				this.playerElement.remove();
			}
		}
	}
}
