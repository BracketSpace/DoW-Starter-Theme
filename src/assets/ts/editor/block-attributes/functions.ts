/**
 * External Dependencies
 */
import { BlockConfiguration, BlockEditProps } from '@wordpress/blocks';
import { kebabCase } from 'lodash';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import config from './config';

type ConfigType = 'margin' | 'padding' | null;

export const shouldAddBlockConfig = (
	block: string,
	type: ConfigType = null
) => {
	if (type && config[type]) {
		return config[type].allowedBlocks.includes(block);
	}

	for (const key in config) {
		const configKey = key as keyof typeof config;

		if (
			config[configKey] &&
			config[configKey].allowedBlocks.includes(block)
		) {
			return true;
		}
	}

	return false;
};

type MutableBlockConfiguration = {
	-readonly [K in keyof BlockConfiguration]: BlockConfiguration[K];
};

export const addAttributes = (
	settings: MutableBlockConfiguration,
	name: string
): BlockConfiguration => {
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

type MutableEditProps<T extends Record<string, any>> = {
	-readonly [K in keyof BlockEditProps<T>]: BlockEditProps<T>[K];
};

export const addProps = (
	props: MutableEditProps<Record<string, any>>,
	{ name }: BlockConfiguration,
	attributes: BlockConfiguration['attributes']
) => {
	if (name && shouldAddBlockConfig(name)) {
		for (const attr of [
			'marginTop',
			'marginBottom',
			'paddingTop',
			'paddingBottom',
		]) {
			const key = attr as keyof BlockConfiguration['attributes'];

			if (attributes[key]) {
				props.className = classnames(
					props.className,
					`${kebabCase(attr)}-${attributes[key]}`
				);
			}
		}
	}

	return props;
};
