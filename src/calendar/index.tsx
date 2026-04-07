/**
 * WordPress dependencies
 */
import { registerBlockType, createBlock } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { Edit } from './edit';

import './style.scss';

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => {
		const blockProps = useBlockProps.save( {
			className: 'simple-calendar-block',
		} );
		return (
			<div { ...blockProps }>
				<InnerBlocks.Content />
			</div>
		);
	},
	transforms: {
		from: [
			{
				type: 'block' as const,
				blocks: [
					'brianhenryie/simple-calendar',
					'brianhenryie/bh-wp-simple-calendar',
				],
				transform: ( attributes: {
					calendarId?: string;
					eventCount?: number;
					eventPeriod?: number;
				} ) => {
					const calendarUrls = attributes.calendarId
						? [ attributes.calendarId ]
						: [];
					return createBlock(
						'simple-calendar/calendar',
						{
							calendarUrls,
							eventCount: attributes.eventCount ?? 10,
							eventPeriod: attributes.eventPeriod ?? 92,
						},
						[
							createBlock( 'simple-calendar/event-template', {}, [
								createBlock(
									'simple-calendar/event-title',
									{}
								),
								createBlock( 'simple-calendar/event-date', {} ),
								createBlock(
									'simple-calendar/event-description',
									{}
								),
								createBlock(
									'simple-calendar/event-location',
									{}
								),
							] ),
						]
					);
				},
			},
		],
	},
} );
