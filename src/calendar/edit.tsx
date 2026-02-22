/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RangeControl,
	Button,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import type { CalendarEvent } from '../types';
import { PLACEHOLDER_EVENTS } from '../types';

import './editor.scss';

interface CalendarAttributes {
	calendarUrls: string[];
	eventCount: number;
	eventPeriod: number;
}

interface EditProps {
	attributes: CalendarAttributes;
	setAttributes: ( attrs: Partial< CalendarAttributes > ) => void;
}

const TEMPLATE: [ string, Record< string, unknown > ][] = [
	[ 'simple-calendar/event-template', {} ],
];

export const Edit: React.FC< EditProps > = ( {
	attributes,
	setAttributes,
} ) => {
	const { calendarUrls, eventCount, eventPeriod } = attributes;
	// TODO: Pass events to child blocks for editor preview.
	// eslint-disable-next-line @typescript-eslint/no-unused-vars
	const [ events, setEvents ] = useState< CalendarEvent[] >( [] );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ newUrl, setNewUrl ] = useState( '' );

	const blockProps = useBlockProps( {
		className: 'simple-calendar-block',
	} );

	// Fetch events when URLs/settings change.
	useEffect( () => {
		if ( calendarUrls.length === 0 ) {
			setEvents( PLACEHOLDER_EVENTS );
			return;
		}

		setIsLoading( true );

		const params = new URLSearchParams();
		calendarUrls.forEach( ( url ) =>
			params.append( 'calendarUrls[]', url )
		);
		params.set( 'eventCount', String( eventCount ) );
		params.set( 'eventPeriod', String( eventPeriod ) );

		apiFetch< CalendarEvent[] >( {
			path: `/simple-calendar/v1/events?${ params.toString() }`,
		} )
			.then( ( data ) => {
				setEvents( data.length > 0 ? data : PLACEHOLDER_EVENTS );
			} )
			.catch( () => {
				setEvents( PLACEHOLDER_EVENTS );
			} )
			.finally( () => {
				setIsLoading( false );
			} );
	}, [ calendarUrls, eventCount, eventPeriod ] );

	const addUrl = () => {
		const trimmed = newUrl.trim();
		if ( trimmed && ! calendarUrls.includes( trimmed ) ) {
			setAttributes( {
				calendarUrls: [ ...calendarUrls, trimmed ],
			} );
			setNewUrl( '' );
		}
	};

	const removeUrl = ( index: number ) => {
		setAttributes( {
			calendarUrls: calendarUrls.filter( ( _, i ) => i !== index ),
		} );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Calendar Sources', 'bh-wp-simple-calendar' ) }
				>
					{ calendarUrls.map( ( url, index ) => (
						<div key={ index } className="simple-calendar-url-item">
							<TextControl
								__nextHasNoMarginBottom
								value={ url }
								onChange={ ( value: string ) => {
									const updated = [ ...calendarUrls ];
									updated[ index ] = value;
									setAttributes( {
										calendarUrls: updated,
									} );
								} }
							/>
							<Button
								isDestructive
								variant="tertiary"
								onClick={ () => removeUrl( index ) }
								size="small"
							>
								{ __( 'Remove', 'bh-wp-simple-calendar' ) }
							</Button>
						</div>
					) ) }
					<div className="simple-calendar-add-url">
						<TextControl
							__nextHasNoMarginBottom
							label={ __(
								'Add calendar URL',
								'bh-wp-simple-calendar'
							) }
							placeholder="https://calendar.google.com/…/basic.ics"
							value={ newUrl }
							onChange={ setNewUrl }
							onKeyDown={ ( e: React.KeyboardEvent ) => {
								if ( e.key === 'Enter' ) {
									e.preventDefault();
									addUrl();
								}
							} }
						/>
						<Button variant="secondary" onClick={ addUrl }>
							{ __( 'Add', 'bh-wp-simple-calendar' ) }
						</Button>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Display Settings', 'bh-wp-simple-calendar' ) }
				>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Number of events',
							'bh-wp-simple-calendar'
						) }
						value={ eventCount }
						onChange={ ( value ) =>
							setAttributes( { eventCount: value } )
						}
						min={ 1 }
						max={ 50 }
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __(
							'Days to look ahead',
							'bh-wp-simple-calendar'
						) }
						value={ eventPeriod }
						onChange={ ( value ) =>
							setAttributes( { eventPeriod: value } )
						}
						min={ 1 }
						max={ 365 }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ isLoading && (
					<p className="simple-calendar-loading">
						{ __( 'Loading events…', 'bh-wp-simple-calendar' ) }
					</p>
				) }
				{ calendarUrls.length === 0 && (
					<p className="simple-calendar-placeholder-notice">
						{ __(
							'Add a calendar URL in the block settings to display real events. Showing placeholder data.',
							'bh-wp-simple-calendar'
						) }
					</p>
				) }
				<InnerBlocks template={ TEMPLATE } templateLock="all" />
			</div>
		</>
	);
};
