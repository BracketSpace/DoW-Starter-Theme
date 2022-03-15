/**
 * WordPress dependencies
 */
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Attributes, Block } from './types';
import blockData from './block.json';
import edit from './edit';
import save from './save';

const { name, category, attributes: blockAttributes } = blockData;

/**
 * Type casting is necessary here since TypeScript cannot analyze the `type`
 * property values and complains about missing attribute properties.
 *
 * TODO: find a way to read json types correctly without type casting.
 */
const attributes = blockAttributes as Block['attributes'];

const config: BlockConfiguration<Attributes> = {
	title: __('Responsive Spacer', 'wsfirst'),
	description: __(
		'Add responsive white space between blocks and customize its height.',
		'wsfirst'
	),
	category,
	attributes,
	edit,
	save,
};

registerBlockType<Attributes>(name, config);
