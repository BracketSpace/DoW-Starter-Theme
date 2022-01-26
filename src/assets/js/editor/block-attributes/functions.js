/**
 * External Dependencies
 */
import classnames from 'classnames';
import { kebabCase } from 'lodash';

/**
 * Internal dependencies
 */
import config from './config';

export const shouldAddBlockConfig = (block, type = false) => {
	if (type && config[type]) {
		return config[type].allowedBlocks.includes(block);
	}

	for (type in config) {
		if (config[type] && config[type].allowedBlocks.includes(block)) {
			return true;
		}
	}

	return false;
};

export const addAttributes = (settings, name) => {
	if (
		shouldAddBlockConfig(name) &&
		typeof settings.attributes !== 'undefined'
	) {
		settings.attributes = {
			...settings.attributes,
			marginTop: {
				type: 'integer',
				default: null,
			},
			marginBottom: {
				type: 'integer',
				default: null,
			},
			paddingTop: {
				type: 'integer',
				default: null,
			},
			paddingBottom: {
				type: 'integer',
				default: null,
			},
		};
	}

	return settings;
};

export const addProps = (props, { name }, attributes) => {
	if (shouldAddBlockConfig(name)) {
		for (const attr of [
			'marginTop',
			'marginBottom',
			'paddingTop',
			'paddingBottom',
		]) {
			if (attributes[attr]) {
				props.className = classnames(
					props.className,
					`${kebabCase(attr)}-${attributes[attr]}`
				);
			}
		}
	}

	return props;
};
