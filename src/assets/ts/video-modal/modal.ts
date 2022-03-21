/**
 * External dependencies
 */
import { on } from 'delegated-events';

/**
 * Internal dependencies
 */
import { YouTubePlayer, WistiaPlayer, VimeoPlayer } from '.';
import { getVideoId } from './utils';

export default class VideoModal {
	activeProvider?: string;
	contentRatio = 0;
	isOpen = false;
	isOpening = false;
	modal?: HTMLElement;
	players = {};
	playerClasses = {
		youtube: YouTubePlayer,
		wistia: WistiaPlayer,
		vimeo: VimeoPlayer,
	};
	playerWrap?: HTMLElement;

	constructor() {
		on('click', '[data-video-provider]', this.handleClick.bind(this));
		document.addEventListener('click', this.closeModal.bind(this));
		window.addEventListener('resize', this.adjust.bind(this));
	}

	handleClick(e: MouseEvent) {
		e.preventDefault();
		e.stopPropagation();

		const target = <HTMLElement>e.target;

		const element =
			undefined === target.dataset.videoProvider
				? <HTMLElement>target.closest('[data-video-provider]')
				: target;

		if (!element) {
			return;
		}

		const provider = element.dataset.videoProvider;
		const videoId =
			element.dataset.videoId ||
			getVideoId(<string>element.getAttribute('href'), provider);

		if (!videoId || !provider) {
			return;
		}

		this.openModal();
		this.loadVideo(videoId, provider);
	}

	adjust() {
		if (!this.modal || !this.isOpen) {
			return;
		}

		const modalStyle = window.getComputedStyle(this.modal);
		const modalWidth =
			this.modal.offsetWidth -
			parseInt(modalStyle.paddingLeft) -
			parseInt(modalStyle.paddingRight);
		const modalHeight =
			this.modal.offsetHeight -
			parseInt(modalStyle.paddingTop) -
			parseInt(modalStyle.paddingBottom);

		const ratio = modalHeight / modalWidth;

		if (
			ratio > this.contentRatio &&
			this.modal.classList.contains('match-height')
		) {
			this.modal.classList.remove('match-height');
		} else if (
			ratio < this.contentRatio &&
			!this.modal.classList.contains('match-height')
		) {
			this.modal.classList.add('match-height');
		}
	}

	openModal() {
		this.isOpen = true;
		this.isOpening = true;

		const modal = this.getModal();

		setTimeout(() => modal.classList.add('visible'), 0);

		this.adjust();
	}

	setReady() {
		this.getModal().classList.remove('is-loading');
	}

	closeModal() {
		if (this.isOpening) {
			this.isOpening = false;
			return;
		}

		if (!this.modal || !this.isOpen) {
			return;
		}

		if (this.activeProvider && this.players[this.activeProvider]) {
			this.players[this.activeProvider].pause();
		}

		this.isOpen = false;

		this.modal.classList.add('hiding');
		this.modal.classList.remove('is-loading');
	}

	hideModal() {
		const modal = this.getModal();

		if (modal.classList.contains('hiding')) {
			modal.classList.remove('hiding', 'visible');
		}
	}

	getModal() {
		if (!this.modal) {
			this.modal = this.createElement('video-modal');
			this.playerWrap = this.createElement('modal-player-wrap');

			const modalContent = this.createElement('video-modal-content');
			const overlay = this.createElement('video-modal-overlay');
			const closeButton = this.createElement(
				'video-modal-close',
				'button'
			);

			modalContent.appendChild(this.playerWrap);

			this.modal.appendChild(overlay);
			this.modal.appendChild(modalContent);
			this.modal.appendChild(closeButton);

			modalContent.addEventListener('click', (e) => e.stopPropagation());
			this.modal.addEventListener(
				'transitionend',
				this.hideModal.bind(this)
			);

			document.body.appendChild(this.modal);

			this.contentRatio =
				modalContent.offsetHeight / modalContent.offsetWidth;

			this.modal.classList.add('is-loading');
		}

		return this.modal;
	}

	loadVideo(videoId: string, provider: string) {
		if (this.activeProvider && provider !== this.activeProvider) {
			this.players[this.activeProvider].destroy();
		}

		const key = provider as keyof typeof this.players;

		if (!this.players[key]) {
			if (!this.playerClasses[key]) {
				console.log(`Invalid video provider: ${provider}`); // eslint-disable-line no-console
				return;
			}

			this.players[key] = new this.playerClasses[key](videoId, this);
		} else {
			this.players[key].loadVideo(videoId);
		}

		this.activeProvider = provider;
	}

	createElement(context: string, tag = 'div') {
		const el = document.createElement(tag);

		if (context) {
			el.classList.add(context);
		}

		return el;
	}
}
