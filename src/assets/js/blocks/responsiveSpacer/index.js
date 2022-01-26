/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import edit from './edit';
import blockData from './block.json';
import save from './save';

const { name, category, attributes } = blockData;

registerBlockType(name, {
	title: __('Responsive Spacer', 'wsfirst'),
	description: __(
		'Add responsive white space between blocks and customize its height.',
		'wsfirst'
	),
	category,
	attributes,
	edit,
	save,
});
