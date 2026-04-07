/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import metadata from './block.json';

interface EditProps {
	attributes: { linkToUrl: boolean };
	setAttributes: ( attrs: Partial< { linkToUrl: boolean } > ) => void;
}

const Edit: React.FC< EditProps > = ( { attributes, setAttributes } ) => {
	const blockProps = useBlockProps( {
		className: 'simple-calendar-event-title',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Title Settings', 'bh-wp-simple-calendar' ) }
				>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Link to event URL',
							'bh-wp-simple-calendar'
						) }
						checked={ attributes.linkToUrl }
						onChange={ ( value ) =>
							setAttributes( { linkToUrl: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<h3 { ...blockProps }>
				{ __( 'Event Title', 'bh-wp-simple-calendar' ) }
			</h3>
		</>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
