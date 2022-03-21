/**
 * External dependencies
 */
import { FC } from 'react';

/**
 * WordPress dependencies
 */
import { BaseControl, TextControl } from '@wordpress/components';

type SpacingControlProps = {
	bottomLabel: string;
	bottomValue: string;
	help: string;
	onChange: (postion: string, value: string) => void;
	topLabel: string;
	topValue: string;
};

const SpacingControl: FC<SpacingControlProps> = ({
	bottomLabel,
	bottomValue,
	help,
	onChange,
	topLabel,
	topValue,
}) => (
	<BaseControl id="spacing-control" help={help}>
		<div className="spacing-settings-row">
			<TextControl
				type="number"
				label={topLabel}
				value={topValue || ''}
				onChange={(value) => onChange('top', value)}
			/>
			<TextControl
				type="number"
				label={bottomLabel}
				value={bottomValue || ''}
				onChange={(value) => onChange('bottom', value)}
			/>
		</div>
	</BaseControl>
);

export default SpacingControl;
