/**
 * WordPress dependencies
 */
import { registerBlockStyle } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import typography from 'config/typography.json';

export default () => {
	if (typography.textStyles) {
		for (const name in typography.textStyles) {
			const label =
				typography.textStyles[name].label ||
				`Styled as ${name.charAt(0).toUpperCase() + name.slice(1)}`;

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
