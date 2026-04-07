/**
 * Tests for the Simple Calendar block in the WordPress block editor.
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Calendar block editor', () => {
	test( 'can insert the calendar block', async ( { admin, page, editor } ) => {
		await admin.createNewPost();

		await editor.insertBlock( { name: 'simple-calendar/calendar' } );

		const calendarBlock = page.locator(
			'.wp-block-simple-calendar-calendar'
		);
		await expect( calendarBlock ).toBeVisible();
	} );

	test( 'calendar block contains event-template with default child blocks', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost();

		await editor.insertBlock( { name: 'simple-calendar/calendar' } );

		const eventTemplate = page.locator(
			'.wp-block-simple-calendar-event-template'
		);
		await expect( eventTemplate ).toBeVisible();

		await expect(
			page.locator( '.wp-block-simple-calendar-event-title' )
		).toBeVisible();
		await expect(
			page.locator( '.wp-block-simple-calendar-event-date' )
		).toBeVisible();
		await expect(
			page.locator( '.wp-block-simple-calendar-event-description' )
		).toBeVisible();
		await expect(
			page.locator( '.wp-block-simple-calendar-event-location' )
		).toBeVisible();
	} );

	test( 'shows placeholder notice when no calendar URL is set', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost();

		await editor.insertBlock( { name: 'simple-calendar/calendar' } );

		const notice = page.locator( '.simple-calendar-placeholder-notice' );
		await expect( notice ).toBeVisible();
		await expect( notice ).toContainText( 'Add a calendar URL' );
	} );

	test( 'event-title block has linkToUrl setting in inspector', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost();

		await editor.insertBlock( { name: 'simple-calendar/calendar' } );

		// Click on the event title block.
		const titleBlock = page.locator(
			'.wp-block-simple-calendar-event-title'
		);
		await titleBlock.click();

		// The "Link to event URL" toggle should be visible in the sidebar.
		await expect(
			page.locator( 'text=Link to event URL' )
		).toBeVisible();
	} );

	test( 'event-date block has date format setting in inspector', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost();

		await editor.insertBlock( { name: 'simple-calendar/calendar' } );

		// Click on the event date block.
		const dateBlock = page.locator(
			'.wp-block-simple-calendar-event-date'
		);
		await dateBlock.click();

		// The date format input should be visible in the sidebar.
		await expect(
			page.locator( 'text=Date format (PHP)' )
		).toBeVisible();
	} );
} );
