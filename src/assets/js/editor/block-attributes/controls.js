/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies
 */
import { capitalize } from 'lodash';

/**
 * Internal dependencies
 */
import { shouldAddBlockConfig } from './functions';
import SpacingControl from './spacing-control';

export default createHigherOrderComponent((BlockEdit) => (props) => {
	if (!shouldAddBlockConfig(props.name)) {
		return <BlockEdit {...props} />;
	}

	const {
		isSelected,
		setAttributes,
		attributes: { marginTop, marginBottom, paddingTop, paddingBottom },
	} = props;

	const onChange = (prefix, sufix, value) => {
		const propName = prefix + capitalize(sufix);

		setAttributes({ [propName]: parseInt(value) || null });
	};

	return (
		<>
			<BlockEdit {...props} />
			{isSelected && (
				<InspectorControls>
					<PanelBody title={__('Spacing settings', 'starter-theme')}>
						{shouldAddBlockConfig(props.name, 'margin') && (
							<SpacingControl
								topValue={marginTop}
								bottomValue={marginBottom}
								topLabel={__('Margin Top', 'starter-theme')}
								bottomLabel={__(
									'Margin Bottom',
									'starter-theme'
								)}
								help={__(
									'Set margin values in pixels.',
									'starter-theme'
								)}
								onChange={(sufix, value) =>
									onChange('margin', sufix, value)
								}
							/>
						)}
						{shouldAddBlockConfig(props.name, 'padding') && (
							<SpacingControl
								topValue={paddingTop}
								bottomValue={paddingBottom}
								topLabel={__('Padding Top', 'starter-theme')}
								bottomLabel={__(
									'Padding Bottom',
									'starter-theme'
								)}
								help={__(
									'Set padding values in pixels.',
									'starter-theme'
								)}
								onChange={(sufix, value) =>
									onChange('padding', sufix, value)
								}
							/>
						)}
					</PanelBody>
				</InspectorControls>
			)}
		</>
	);
});
