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

interface DescriptionAttributes {
	showOnlyForFirstOccurrence: boolean;
}

interface EditProps {
	attributes: DescriptionAttributes;
	setAttributes: ( attrs: Partial< DescriptionAttributes > ) => void;
}

const Edit: React.FC< EditProps > = ( { attributes, setAttributes } ) => {
	const blockProps = useBlockProps( {
		className: 'simple-calendar-event-description',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Description Settings',
						'bh-wp-simple-calendar'
					) }
				>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Only show on first occurrence',
							'bh-wp-simple-calendar'
						) }
						help={ __(
							'For recurring events, display the description on the first listed instance only, rather than repeating it on every instance.',
							'bh-wp-simple-calendar'
						) }
						checked={ attributes.showOnlyForFirstOccurrence }
						onChange={ ( value ) =>
							setAttributes( {
								showOnlyForFirstOccurrence: value,
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ __( 'Event Description', 'bh-wp-simple-calendar' ) }
			</div>
		</>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
