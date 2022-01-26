/**
 * WordPress dependencies
 */
import { BaseControl, TextControl } from '@wordpress/components';

export default ({
	topLabel,
	bottomLabel,
	topValue,
	bottomValue,
	onChange,
	help,
}) => (
	<BaseControl help={help}>
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
