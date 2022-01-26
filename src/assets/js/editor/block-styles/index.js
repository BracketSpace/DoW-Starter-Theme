/**
 * External dependencies
 */
import { filter, find, kebabCase, map, startCase } from 'lodash';

/**
 * Internal dependencies
 */
import registerTextStyles from './text';
import styles from 'config/block-styles.json';

export const registerBlockStyles = () => {
	registerTextStyles();
};

export const filterBlockSettings = (settings, name) => {
	if (!Object.keys(styles).includes(name)) {
		return settings;
	}

	const blockStyles = filter(settings.styles, (style) =>
		styles[name].includes(style.name)
	);

	const newStyles = map(
		filter(
			styles[name],
			(styleName) => !find(blockStyles, { name: styleName })
		),
		(styleName) => ({
			name: kebabCase(styleName),
			label: startCase(styleName),
		})
	);

	if (!find(blockStyles, { isDefault: true }) && newStyles.length) {
		newStyles[0].isDefault = true;
	}

	return {
		...settings,
		styles: [...blockStyles, ...newStyles],
	};
};
