/**
 * WordPress Dependencies
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Internal Dependencies
 */
import { addAttributes, addProps } from './functions';
import controls from './controls';

export default () => {
	addFilter(
		'blocks.registerBlockType',
		'starter-theme/custom-attributes',
		addAttributes
	);

	addFilter(
		'editor.BlockEdit',
		'starter-theme/custom-advanced-control',
		controls
	);

	addFilter(
		'blocks.getSaveContent.extraProps',
		'starter-theme/add-props',
		addProps
	);
};
