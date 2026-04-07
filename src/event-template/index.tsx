/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { Edit } from './edit';

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => {
		const blockProps = useBlockProps.save( {
			className: 'simple-calendar-event-template',
		} );
		return (
			<div { ...blockProps }>
				<InnerBlocks.Content />
			</div>
		);
	},
} );
