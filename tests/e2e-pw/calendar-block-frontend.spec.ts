/**
 * Tests for the Simple Calendar block rendering on the frontend.
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test.describe( 'Calendar block frontend', () => {
	test( 'calendar block renders event list on frontend', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost( { title: 'Calendar Block Frontend Test' } );

		await editor.insertBlock( {
			name: 'simple-calendar/calendar',
			attributes: {
				calendarUrls: [
					'https://calendar.google.com/calendar/ical/en.usa%23holiday%40group.v.calendar.google.com/public/basic.ics',
				],
				eventCount: 5,
				eventPeriod: 365,
			},
		} );

		// Publish the post.
		await page
			.getByRole( 'button', { name: 'Publish', exact: true } )
			.click();
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

		// The calendar should render an event list.
		const eventList = page.locator( '.simple-calendar-event-list' );
		await expect( eventList ).toBeVisible( { timeout: 15000 } );

		// There should be at least one event item.
		const eventItems = page.locator( '.simple-calendar-event-item' );
		await expect( eventItems.first() ).toBeVisible();

		// Each event should have a title.
		const eventTitles = page.locator( '.simple-calendar-event-title' );
		await expect( eventTitles.first() ).toBeVisible();
		await expect( eventTitles.first() ).not.toBeEmpty();
	} );

	test( 'empty calendar URLs shows nothing on frontend', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost( { title: 'Empty Calendar Test' } );

		await editor.insertBlock( {
			name: 'simple-calendar/calendar',
			attributes: {
				calendarUrls: [],
				eventCount: 5,
				eventPeriod: 92,
			},
		} );

		await page
			.getByRole( 'button', { name: 'Publish', exact: true } )
			.click();
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

		const calendar = page.locator( '.simple-calendar-block' );
		await expect( calendar ).not.toBeVisible();
	} );

	test( 'default layout has title+date and description+location in flex rows', async ( {
		admin,
		page,
		editor,
	} ) => {
		await admin.createNewPost( {
			title: 'Calendar Layout Test',
		} );

		// Insert the calendar block — default template includes grouped layout.
		await editor.insertBlock( {
			name: 'simple-calendar/calendar',
			attributes: {
				calendarUrls: [
					'https://calendar.google.com/calendar/ical/en.usa%23holiday%40group.v.calendar.google.com/public/basic.ics',
				],
				eventCount: 3,
				eventPeriod: 365,
			},
		} );

		// Publish.
		await page
			.getByRole( 'button', { name: 'Publish', exact: true } )
			.click();
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

		// Wait for the calendar to render.
		const eventList = page.locator( '.simple-calendar-event-list' );
		await expect( eventList ).toBeVisible( { timeout: 15000 } );

		const firstItem = page.locator( '.simple-calendar-event-item' ).first();

		// The event item should contain flex group wrappers.
		const groups = firstItem.locator( '.wp-block-group' );
		await expect( groups ).toHaveCount( 2 );

		// First group: title + date on the same line.
		const firstGroup = groups.nth( 0 );
		await expect(
			firstGroup.locator( '.simple-calendar-event-title' )
		).toBeVisible();
		await expect(
			firstGroup.locator( '.simple-calendar-event-date' )
		).toBeVisible();

		// Verify they're in a flex row (space-between).
		const firstGroupStyle = await firstGroup.evaluate( ( el ) =>
			window.getComputedStyle( el ).display
		);
		expect( firstGroupStyle ).toBe( 'flex' );

		// Second group: description + location.
		const secondGroup = groups.nth( 1 );
		await expect(
			secondGroup.locator( '.simple-calendar-event-description' )
		).toBeVisible();
		// Location may be empty for some events, so just check the group exists.
		const secondGroupStyle = await secondGroup.evaluate( ( el ) =>
			window.getComputedStyle( el ).display
		);
		expect( secondGroupStyle ).toBe( 'flex' );
	} );
} );
