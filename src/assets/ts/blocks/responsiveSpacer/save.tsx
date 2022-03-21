/**
 * External dependencies
 */
import classnames from 'classnames';
import { FC } from 'react';

/**
 * Internal dependencies
 */
import { BlockSaveProps } from './types';

const Save: FC<BlockSaveProps> = ({
	className,
	attributes: { height, mobileHeight, tabletHeight, useRem, remSize },
}) => {
	const calculateHeight = (value: number) =>
		useRem
			? `${Math.round((value / remSize) * 100000) / 100000}rem`
			: `${value}px`;

	return (
		<div className={classnames(className, 'responsive-spacer')} aria-hidden>
			<div
				className="responsive-spacer-fullsize"
				style={{ height: calculateHeight(height) }}
			/>
			<div
				className="responsive-spacer-tablet"
				style={{ height: calculateHeight(tabletHeight) }}
			/>
			<div
				className="responsive-spacer-mobile"
				style={{ height: calculateHeight(mobileHeight) }}
			/>
		</div>
	);
};

export default Save;
