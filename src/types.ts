/**
 * Shared types for Simple Calendar blocks.
 */

export interface CalendarEvent {
	summary: string;
	status: string;
	startTime: string;
	endTime: string;
	url: string | null;
	description: string | null;
	location: string | null;
	uid: string | null;
	isRecurring: boolean;
	recurrenceRule: string | null;
	recurrenceDescription: string | null;
}

/**
 * Placeholder events shown before a calendar URL is configured.
 */
export const PLACEHOLDER_EVENTS: CalendarEvent[] = [
	{
		summary: 'Community Meetup',
		status: 'CONFIRMED',
		startTime: new Date( Date.now() + 86400000 ).toISOString(),
		endTime: new Date( Date.now() + 86400000 + 7200000 ).toISOString(),
		url: 'https://example.com/meetup',
		description: 'Join us for our monthly community gathering.',
		location: 'Downtown Community Center',
		uid: 'placeholder-1',
		isRecurring: true,
		recurrenceRule: 'FREQ=MONTHLY;BYDAY=1TU',
		recurrenceDescription: 'Every month on 1st Tuesday',
	},
	{
		summary: 'Workshop: Getting Started',
		status: 'CONFIRMED',
		startTime: new Date( Date.now() + 172800000 ).toISOString(),
		endTime: new Date( Date.now() + 172800000 + 3600000 ).toISOString(),
		url: null,
		description: 'An introductory workshop for beginners.',
		location: null,
		uid: 'placeholder-2',
		isRecurring: false,
		recurrenceRule: null,
		recurrenceDescription: null,
	},
	{
		summary: 'Annual Celebration',
		status: 'CONFIRMED',
		startTime: new Date( Date.now() + 604800000 ).toISOString(),
		endTime: new Date( Date.now() + 604800000 + 14400000 ).toISOString(),
		url: 'https://example.com/celebration',
		description:
			'Our annual celebration event with food and entertainment.',
		location: 'City Park Pavilion',
		uid: 'placeholder-3',
		isRecurring: false,
		recurrenceRule: null,
		recurrenceDescription: null,
	},
];

/**
 * Block context keys used by event field blocks.
 */
export const CONTEXT_PREFIX = 'simple-calendar';
