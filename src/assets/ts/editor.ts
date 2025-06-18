/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import domReady from '@wordpress/dom-ready';

/**
 * Internal dependencies
 */
import './blocks/responsiveSpacer';
import registerBlockAttributes from './editor/block-attributes';
import {
	filterBlockSettings,
	registerBlockStyles,
} from './editor/block-styles';

registerBlockAttributes();

addFilter('blocks.registerBlockType', 'dow-starter-theme', filterBlockSettings);

domReady(() => {
	registerBlockStyles();
});
