/**
 * WordPress dependencies
 */
import {
	Block as BaseBlock,
	BlockEditProps as BaseBlockEditProps,
	BlockSaveProps as BaseBlockSaveProps,
} from '@wordpress/blocks';

/**
 * Block attributes type
 */
export type Attributes = {
	height: number;
	auto: boolean;
	tabletHeight: number;
	mobileHeight: number;
	useRem: boolean;
	remSize: number;
};

/**
 * Block with attributes type
 */
export type Block = BaseBlock<Attributes>;

/**
 * Edit component props
 */
export type BlockEditProps = BaseBlockEditProps<Attributes> & {
	toggleSelection?: (value: boolean) => void;
};

/**
 * Save component props
 */
export type BlockSaveProps = BaseBlockSaveProps<Attributes> & {
	className?: string;
};
