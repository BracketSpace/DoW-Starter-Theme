/**
 * External dependencies
 */
import { FC } from 'react';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	TextControl,
	PanelBody,
	RangeControl,
	ResizableBox,
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { BlockEditProps } from './types';
import { calculateAutoHeight } from './utils';
import { MAX_SPACER_HEIGHT, MIN_SPACER_HEIGHT } from './constants';

const Edit: FC<BlockEditProps> = ({
	className,
	attributes: { auto, height, mobileHeight, tabletHeight, useRem, remSize },
	setAttributes,
	isSelected,
	toggleSelection,
}) => {
	const updateTabletHeight = (value: number | undefined) =>
		setAttributes({ tabletHeight: value });

	const updateMobileHeight = (value: number | undefined) =>
		setAttributes({ mobileHeight: value });

	const updateHeight = (value: number, force = false) =>
		setAttributes(
			auto || force ? calculateAutoHeight(value) : { height: value }
		);

	const toggleAutoCalculate = (checked: boolean) => {
		setAttributes({ auto: checked });

		if (checked) {
			updateHeight(height, true);
		}
	};

	return (
		<>
			<ResizableBox
				className={classnames(
					'block-library-spacer__resize-container',
					className,
					{
						'is-selected': isSelected,
					}
				)}
				size={{
					height,
					width: '100%',
				}}
				minHeight={MIN_SPACER_HEIGHT}
				enable={{
					top: false,
					right: false,
					bottom: true,
					left: false,
					topRight: false,
					bottomRight: false,
					bottomLeft: false,
					topLeft: false,
				}}
				onResizeStart={() => toggleSelection && toggleSelection(false)}
				onResizeStop={(event, direction, elt, delta) => {
					toggleSelection && toggleSelection(true);

					const spacerHeight = Math.min(
						height + delta.height,
						MAX_SPACER_HEIGHT
					);
					updateHeight(spacerHeight);
				}}
				showHandle={isSelected}
			/>
			<InspectorControls>
				<PanelBody title={__('Size settings')}>
					<RangeControl
						label={__('Height in pixels')}
						min={MIN_SPACER_HEIGHT}
						max={Math.max(MAX_SPACER_HEIGHT, height)}
						value={height}
						onChange={updateHeight}
					/>
					<ToggleControl
						checked={auto}
						label={__(
							'Automatically calculate responsive size',
							'wsfirst'
						)}
						onChange={toggleAutoCalculate}
					/>
					<RangeControl
						label={__('Tablet height in pixels')}
						min={0}
						max={Math.max(MAX_SPACER_HEIGHT, tabletHeight)}
						value={tabletHeight}
						onChange={updateTabletHeight}
						disabled={auto}
					/>
					<RangeControl
						label={__('Mobile height in pixels')}
						min={0}
						max={Math.max(MAX_SPACER_HEIGHT, mobileHeight)}
						value={mobileHeight}
						onChange={updateMobileHeight}
						disabled={auto}
					/>
				</PanelBody>
				<PanelBody title={__('Unit settings')}>
					<ToggleControl
						checked={useRem}
						label={__('Use rem units', 'wsfirst')}
						help={__(
							'If turned on, pixel values from the fields above will be converted to rem units.',
							'wsfirst'
						)}
						onChange={(value) => setAttributes({ useRem: value })}
					/>
					{useRem && (
						<TextControl
							type="number"
							label={__('Rem unit size in pixels', 'wsfirst')}
							value={remSize}
							onChange={(value) =>
								setAttributes({ remSize: parseInt(value) })
							}
						/>
					)}
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default Edit;
