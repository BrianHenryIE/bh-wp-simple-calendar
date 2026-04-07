/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import metadata from './block.json';

interface EditProps {
	attributes: { dateFormat: string; timeFormat: string; showEndTime: boolean };
	setAttributes: (
		attrs: Partial< {
			dateFormat: string;
			timeFormat: string;
			showEndTime: boolean;
		} >
	) => void;
}

const Edit: React.FC< EditProps > = ( { attributes, setAttributes } ) => {
	const blockProps = useBlockProps( {
		className: 'simple-calendar-event-date',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Date Settings', 'bh-wp-simple-calendar' ) }
				>
					<TextControl
						__nextHasNoMarginBottom
						label={ __(
							'Date format (PHP)',
							'bh-wp-simple-calendar'
						) }
						value={ attributes.dateFormat }
						onChange={ ( value ) =>
							setAttributes( { dateFormat: value } )
						}
						help={ __( 'e.g. "l F j"', 'bh-wp-simple-calendar' ) }
					/>
					<TextControl
						__nextHasNoMarginBottom
						label={ __(
							'Time format (PHP)',
							'bh-wp-simple-calendar'
						) }
						value={ attributes.timeFormat }
						onChange={ ( value ) =>
							setAttributes( { timeFormat: value } )
						}
						help={
							<>
								{ __( 'e.g. "H:i" — ', 'bh-wp-simple-calendar' ) }
								<a
									href="https://www.php.net/manual/en/datetime.format.php"
									target="_blank"
									rel="noreferrer"
								>
									{ __(
										'PHP date format reference',
										'bh-wp-simple-calendar'
									) }
								</a>
							</>
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Show end time', 'bh-wp-simple-calendar' ) }
						checked={ attributes.showEndTime }
						onChange={ ( value ) =>
							setAttributes( { showEndTime: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<time { ...blockProps }>
				{ __( 'Event Date', 'bh-wp-simple-calendar' ) }
			</time>
		</>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
