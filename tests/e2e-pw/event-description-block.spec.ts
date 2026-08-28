/**
 * Tests for the Event Description block's "Only show on first occurrence" option.
 *
 * The fixture calendar contains two weekly recurring events (Tuesday and Thursday) with no end
 * date, so there are always upcoming instances. It is served by the wp-env WordPress container
 * itself, from the plugin directory, so the PHP side can fetch it without external network access.
 */
import { test, expect, Editor } from '@wordpress/e2e-test-utils-playwright';
import type { Page } from '@playwright/test';

const FIXTURE_CALENDAR_URL =
	'http://localhost/wp-content/plugins/bh-wp-simple-calendar/tests/e2e-pw/fixtures/recurring-calendar.ics';

const TOGGLE_LABEL = 'Only show on first occurrence';

/**
 * Six events from two alternating weekly series gives three instances of each.
 */
const EVENT_COUNT = 6;

async function insertCalendarBlock( editor: Editor ): Promise< void > {
	await editor.insertBlock( {
		name: 'simple-calendar/calendar',
		attributes: {
			calendarUrls: [ FIXTURE_CALENDAR_URL ],
			eventCount: EVENT_COUNT,
			eventPeriod: 365,
		},
	} );
}

async function publishAndVisitPost( page: Page ): Promise< void > {
	await page.getByRole( 'button', { name: 'Publish', exact: true } ).click();
	await page
		.getByRole( 'region', { name: 'Editor publish' } )
		.getByRole( 'button', { name: 'Publish', exact: true } )
		.click();

	await expect( page.getByTestId( 'snackbar' ) ).toBeVisible( {
		timeout: 10000,
	} );

	const viewLink = page.locator(
		'.post-publish-panel__postpublish-header a'
	);
	const postUrl = await viewLink.getAttribute( 'href' );
	await page.goto( postUrl! );

	await expect( page.locator( '.simple-calendar-event-list' ) ).toBeVisible( {
		timeout: 15000,
	} );
}

test.describe( 'Event description block', () => {
	test( 'has "Only show on first occurrence" toggle in inspector, off by default', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost();

		await editor.insertBlock( { name: 'simple-calendar/calendar' } );

		await page
			.locator( '.wp-block-simple-calendar-event-description' )
			.click();

		const toggle = page.getByLabel( TOGGLE_LABEL );
		await expect( toggle ).toBeVisible();
		await expect( toggle ).not.toBeChecked();
	} );

	test( 'toggling the option sets the block attribute', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost();

		await editor.insertBlock( { name: 'simple-calendar/calendar' } );

		await page
			.locator( '.wp-block-simple-calendar-event-description' )
			.click();

		const toggle = page.getByLabel( TOGGLE_LABEL );
		await toggle.click();
		await expect( toggle ).toBeChecked();

		const attributes = await page.evaluate( () => {
			// eslint-disable-next-line @typescript-eslint/no-explicit-any
			const blockEditor = ( window as any ).wp.data.select(
				'core/block-editor'
			);
			const [ descriptionClientId ] = blockEditor.getBlocksByName(
				'simple-calendar/event-description'
			);
			return blockEditor.getBlockAttributes( descriptionClientId );
		} );

		expect( attributes.showOnlyForFirstOccurrence ).toBe( true );
	} );

	test( 'by default the description is printed on every instance of a recurring event', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost( { title: 'Repeated Description Test' } );

		await insertCalendarBlock( editor );

		await publishAndVisitPost( page );

		const eventItems = page.locator( '.simple-calendar-event-item' );
		await expect( eventItems ).toHaveCount( EVENT_COUNT );

		const descriptions = page.locator(
			'.simple-calendar-event-description'
		);
		await expect( descriptions ).toHaveCount( EVENT_COUNT );

		await expect(
			descriptions.filter( {
				hasText: 'Weekly sync meeting description.',
			} )
		).toHaveCount( EVENT_COUNT / 2 );
		await expect(
			descriptions.filter( { hasText: 'Weekly run description.' } )
		).toHaveCount( EVENT_COUNT / 2 );
	} );

	test( 'with the option enabled the description is printed only on the first instance of each recurring event', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost( {
			title: 'First Occurrence Description Test',
		} );

		await insertCalendarBlock( editor );

		// Enable the option through the block inspector UI.
		await page
			.locator( '.wp-block-simple-calendar-event-description' )
			.click();
		const toggle = page.getByLabel( TOGGLE_LABEL );
		await toggle.click();
		await expect( toggle ).toBeChecked();

		await publishAndVisitPost( page );

		const eventItems = page.locator( '.simple-calendar-event-item' );
		await expect( eventItems ).toHaveCount( EVENT_COUNT );

		// Every instance still has its title.
		await expect(
			page.locator( '.simple-calendar-event-title' )
		).toHaveCount( EVENT_COUNT );

		// One description per recurring series, not per instance.
		const descriptions = page.locator(
			'.simple-calendar-event-description'
		);
		await expect( descriptions ).toHaveCount( 2 );
		await expect(
			descriptions.filter( {
				hasText: 'Weekly sync meeting description.',
			} )
		).toHaveCount( 1 );
		await expect(
			descriptions.filter( { hasText: 'Weekly run description.' } )
		).toHaveCount( 1 );

		// The two series alternate weekly, so the first two items are the first instance of each
		// series and carry the description; every later item is a repeat and does not.
		await expect(
			eventItems.nth( 0 ).locator( '.simple-calendar-event-description' )
		).toHaveCount( 1 );
		await expect(
			eventItems.nth( 1 ).locator( '.simple-calendar-event-description' )
		).toHaveCount( 1 );
		for ( let i = 2; i < EVENT_COUNT; i++ ) {
			await expect(
				eventItems
					.nth( i )
					.locator( '.simple-calendar-event-description' )
			).toHaveCount( 0 );
		}
	} );
} );
