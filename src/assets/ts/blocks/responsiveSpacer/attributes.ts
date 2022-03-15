/**
 * Internal dependencies
 */
import { attributes as blockAttributes } from './block.json';
import { Block } from './types';

/**
 * Type casting is necessary here since TypeScript cannot analyze the `type`
 * property values and complains about missing attribute properties.
 *
 * TODO: find a way to read json types correctly without type casting.
 */
const attributes = blockAttributes as Block['attributes'];

export default attributes;
