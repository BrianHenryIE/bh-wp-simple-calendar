/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import metadata from './block.json';

interface LocationRegex {
	regex: string;
	replacement: string;
	comment: string;
}

interface EditProps {
	attributes: {
		linkToMaps: boolean;
		locationRegexes: LocationRegex[];
	};
	setAttributes: (
		attrs: Partial< {
			linkToMaps: boolean;
			locationRegexes: LocationRegex[];
		} >
	) => void;
}

const Edit: React.FC< EditProps > = ( { attributes, setAttributes } ) => {
	const blockProps = useBlockProps( {
		className: 'simple-calendar-event-location',
	} );

	const { locationRegexes } = attributes;

	const addRegex = () => {
		setAttributes( {
			locationRegexes: [
				...locationRegexes,
				{ regex: '', replacement: '', comment: '' },
			],
		} );
	};

	const updateRegex = (
		index: number,
		field: keyof LocationRegex,
		value: string
	) => {
		setAttributes( {
			locationRegexes: locationRegexes.map( ( entry, i ) =>
				i === index ? { ...entry, [ field ]: value } : entry
			),
		} );
	};

	const removeRegex = ( index: number ) => {
		setAttributes( {
			locationRegexes: locationRegexes.filter( ( _, i ) => i !== index ),
		} );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Location Settings',
						'bh-wp-simple-calendar'
					) }
				>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __(
							'Link to Google Maps',
							'bh-wp-simple-calendar'
						) }
						checked={ attributes.linkToMaps }
						onChange={ ( value ) =>
							setAttributes( { linkToMaps: value } )
						}
					/>
					{ locationRegexes.map( ( entry, index ) => (
						<div
							key={ index }
							className="simple-calendar-location-regex"
						>
							<TextControl
								__nextHasNoMarginBottom
								label={ __( 'Note', 'bh-wp-simple-calendar' ) }
								value={ entry.comment }
								onChange={ ( value ) =>
									updateRegex( index, 'comment', value )
								}
								placeholder={ __(
									'What this pattern does',
									'bh-wp-simple-calendar'
								) }
							/>
							<TextControl
								__nextHasNoMarginBottom
								label={ __(
									'Search regex',
									'bh-wp-simple-calendar'
								) }
								value={ entry.regex }
								onChange={ ( value ) =>
									updateRegex( index, 'regex', value )
								}
								help={ __(
									'PHP regex. e.g. "/Room \\d+,?\\s*/"',
									'bh-wp-simple-calendar'
								) }
							/>
							<TextControl
								__nextHasNoMarginBottom
								label={ __(
									'Replacement',
									'bh-wp-simple-calendar'
								) }
								value={ entry.replacement }
								onChange={ ( value ) =>
									updateRegex( index, 'replacement', value )
								}
							/>
							<Button
								isDestructive
								variant="tertiary"
								size="small"
								onClick={ () => removeRegex( index ) }
							>
								{ __( 'Remove', 'bh-wp-simple-calendar' ) }
							</Button>
						</div>
					) ) }
					<Button
						variant="secondary"
						size="small"
						onClick={ addRegex }
					>
						{ locationRegexes.length === 0
							? __(
									'Add search pattern',
									'bh-wp-simple-calendar'
							  )
							: __(
									'Add another',
									'bh-wp-simple-calendar'
							  ) }
					</Button>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ __( 'Event Location', 'bh-wp-simple-calendar' ) }
			</div>
		</>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
