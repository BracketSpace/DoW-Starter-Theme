/**
 * Internal dependencies
 */
import { MAX_MOBILE_HEIGHT, MIN_SPACER_HEIGHT } from './constants';

export const calculateAutoHeight = (height: number) => {
	const mobileHeight = Math.max(
		Math.min(MAX_MOBILE_HEIGHT, Math.round(height / 2)),
		MIN_SPACER_HEIGHT
	);

	return {
		height,
		mobileHeight,
		tabletHeight: Math.round((height - mobileHeight) / 2 + mobileHeight),
	};
};
