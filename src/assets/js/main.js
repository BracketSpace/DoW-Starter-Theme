/**
 * External dependencies
 */
import '@micropackage/vw/src/js/vw.js';
import objectFitImages from 'object-fit-images';
import responsiveEmbeds from '@micropackage/responsive-embeds';

/**
 * Internal dependencies
 */
import './blocks';

objectFitImages();
responsiveEmbeds('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]');
