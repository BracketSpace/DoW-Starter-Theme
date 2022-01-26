import { kebabCase } from 'lodash';

export default class Block {
	constructor(block) {
		this.block = block;
	}

	static init() {
		if ('_default' === this.name) {
			return;
		}

		const blocks = document.querySelectorAll(
			`.block.${kebabCase(this.name)}`
		);

		for (const block of blocks) {
			new this(block);
		}
	}
}
