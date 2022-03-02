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
	isOpen = false;
	isOpening = false;
	contentRatio = 0;
	activeProvider = null;
	players = {};
	playerClasses = {
		youtube: YouTubePlayer,
		wistia: WistiaPlayer,
		vimeo: VimeoPlayer,
	};

	constructor() {
		on('click', '[data-video-provider]', this.handleClick.bind(this));
		document.addEventListener('click', this.closeModal.bind(this));
		window.addEventListener('resize', this.adjust.bind(this));
	}

	handleClick(e) {
		e.preventDefault();
		e.stopPropagation();

		const element =
			undefined === e.target.dataset.videoProvider
				? e.target.closest('[data-video-provider]')
				: e.target;

		if (!element) {
			return;
		}

		const provider = element.dataset.videoProvider;
		const videoId =
			element.dataset.videoId || getVideoId(element.href, provider);

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

		if (!this.modal) {
			this.createModal();

			this.modal.classList.add('is-loading');

			setTimeout(() => this.modal.classList.add('visible'), 30);
		} else {
			this.modal.classList.add('visible');
		}

		this.adjust();
	}

	setReady() {
		this.modal.classList.remove('is-loading');
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
		if (this.modal.classList.contains('hiding')) {
			this.modal.classList.remove('hiding', 'visible');
		}
	}

	createModal() {
		this.modal = this.createElement('video-modal');
		this.playerWrap = this.createElement('modal-player-wrap');

		const modalContent = this.createElement('video-modal-content');
		const overlay = this.createElement('video-modal-overlay');
		const closeButton = this.createElement('video-modal-close', 'button');

		modalContent.appendChild(this.playerWrap);

		this.modal.appendChild(overlay);
		this.modal.appendChild(modalContent);
		this.modal.appendChild(closeButton);

		modalContent.addEventListener('click', (e) => e.stopPropagation());
		this.modal.addEventListener('transitionend', this.hideModal.bind(this));

		document.body.appendChild(this.modal);

		this.contentRatio =
			modalContent.offsetHeight / modalContent.offsetWidth;
	}

	loadVideo(videoId, provider) {
		if (this.activeProvider && provider !== this.activeProvider) {
			this.players[this.activeProvider].destroy();
		}

		if (!this.players[provider]) {
			if (!this.playerClasses[provider]) {
				console.log(`Invalid video provider: ${provider}`); // eslint-disable-line no-console
				return;
			}

			this.players[provider] = new this.playerClasses[provider](
				videoId,
				this
			);
		} else {
			this.players[provider].loadVideo(videoId);
		}

		this.activeProvider = provider;
	}

	createElement(context, tag = 'div') {
		const el = document.createElement(tag);

		if (context) {
			el.classList.add(context);
		}

		return el;
	}
}
