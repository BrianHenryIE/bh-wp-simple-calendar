/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

/**
 * Default template for event layout.
 *
 * Uses core/group blocks with flex row layout to put title+date
 * and description+location on separate lines, each with left/right alignment.
 */
const DEFAULT_TEMPLATE: [
	string,
	Record< string, unknown >,
	any[] | undefined,
][] = [
	[
		'core/group',
		{
			layout: {
				type: 'flex',
				flexWrap: 'wrap',
				justifyContent: 'space-between',
			},
		},
		[
			[ 'simple-calendar/event-title', {} ],
			[ 'simple-calendar/event-date', {} ],
		],
	],
	[
		'core/group',
		{
			layout: {
				type: 'flex',
				flexWrap: 'wrap',
				justifyContent: 'space-between',
			},
		},
		[
			[ 'simple-calendar/event-description', {} ],
			[ 'simple-calendar/event-location', {} ],
		],
	],
];

export const Edit: React.FC = () => {
	const blockProps = useBlockProps( {
		className: 'simple-calendar-event-template',
	} );

	return (
		<div { ...blockProps }>
			<InnerBlocks
				template={ DEFAULT_TEMPLATE }
				templateLock={ false }
				renderAppender={ InnerBlocks.DefaultBlockAppender }
			/>
		</div>
	);
};
