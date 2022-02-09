/**
 * WordPress dependencies
 */
import { kebabCase, startCase } from 'lodash';
import { registerBlockStyle } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import typography from 'config/typography.json';

export default () => {
	if (typography.textStyles) {
		for (const key in typography.textStyles) {
			const name = kebabCase(key).replace(/\-([0-9]+)/, '$1');

			const label =
				typography.textStyles[key].label ||
				`Styled as ${startCase(name).replace(/\s([0-9]+)/, '$1')}`;

			registerBlockStyle('core/paragraph', {
				name,
				label,
			});

			registerBlockStyle('core/heading', {
				name,
				label,
			});
		}
	}
};
