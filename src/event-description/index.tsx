/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import metadata from './block.json';

const Edit: React.FC = () => {
	const blockProps = useBlockProps( {
		className: 'simple-calendar-event-description',
	} );

	return (
		<div { ...blockProps }>
			{ __( 'Event Description', 'bh-wp-simple-calendar' ) }
		</div>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
