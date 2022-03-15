/**
 * WordPress dependencies
 */
import lodash from 'lodash';
import { registerBlockStyle } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import typography from 'config/typography.json';

export default () => {
	if (!typography.textStyles) {
		return;
	}

	for (const key in typography.textStyles) {
		const name = lodash.kebabCase(key).replace(/\-([0-9]+)/, '$1');

		const label =
			<string>typography.textStyles[key].label ||
			`Styled as ${lodash.startCase(name).replace(/\s([0-9]+)/, '$1')}`;

		registerBlockStyle('core/paragraph', {
			name,
			label,
		});

		registerBlockStyle('core/heading', {
			name,
			label,
		});
	}
};
