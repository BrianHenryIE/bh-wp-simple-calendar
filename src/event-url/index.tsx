/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import metadata from './block.json';

interface EditProps {
	attributes: { linkText: string };
	setAttributes: ( attrs: Partial< { linkText: string } > ) => void;
}

const Edit: React.FC< EditProps > = ( { attributes, setAttributes } ) => {
	const blockProps = useBlockProps( {
		className: 'simple-calendar-event-url',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Link Settings', 'bh-wp-simple-calendar' ) }
				>
					<TextControl
						__nextHasNoMarginBottom
						label={ __(
							'Link text (leave empty to show URL)',
							'bh-wp-simple-calendar'
						) }
						value={ attributes.linkText }
						onChange={ ( value ) =>
							setAttributes( { linkText: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<a href="#event-url">
					{ attributes.linkText ||
						__( 'Event Link', 'bh-wp-simple-calendar' ) }
				</a>
			</div>
		</>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
