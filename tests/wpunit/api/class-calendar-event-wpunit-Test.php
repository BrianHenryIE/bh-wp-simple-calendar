<?php
/**
 * Tests Calendar_Event::from_ical_event() timezone handling.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\ICal\ICal;
use BrianHenryIE\WP_Simple_Calendar\WPUnit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\API\Calendar_Event
 */
class Calendar_Event_Wpunit_Test extends WPUnit_Testcase {

	private string $original_timezone_option;

	protected function setUp(): void {
		parent::setUp();
		$this->original_timezone_option = get_option( 'timezone_string', '' );
		update_option( 'timezone_string', 'America/Los_Angeles' );
	}

	protected function tearDown(): void {
		update_option( 'timezone_string', $this->original_timezone_option );
		parent::tearDown();
	}

	/**
	 * Parse tests/_data/timezone-events.ics into Calendar_Events keyed by summary.
	 *
	 * Recurring events share a summary, so values are arrays of events.
	 *
	 * @return array<string, Calendar_Event[]>
	 */
	private function get_events(): array {
		global $project_root_dir;

		$ical = new ICal( false, array( 'defaultTimeZone' => wp_timezone_string() ) );
		$ical->initString( file_get_contents( $project_root_dir . '/tests/_data/timezone-events.ics' ) );

		$events = array();
		foreach ( $ical->events() as $ical_event ) {
			$event                       = Calendar_Event::from_ical_event( $ical_event, $ical );
			$events[ $event->summary ][] = $event;
		}

		return $events;
	}

	/**
	 * A `DTSTART;TZID=America/Los_Angeles:20260711T100000` event must display at 10:00, not 03:00.
	 *
	 * The raw `dtstart` string has the TZID parameter stripped, so it used to be treated as a
	 * floating time and interpreted as UTC.
	 *
	 * @covers ::from_ical_event
	 */
	public function test_tzid_event_keeps_its_local_time(): void {
		$event = $this->get_events()['TZID timed event'][0];

		$this->assertSame( '2026-07-11 10:00', $event->start_time->format( 'Y-m-d H:i' ) );
		$this->assertSame( '2026-07-11 12:00', $event->end_time->format( 'Y-m-d H:i' ) );
		$this->assertFalse( $event->is_all_day );
	}

	/**
	 * An event in a timezone other than the site's converts to the site timezone.
	 *
	 * 19:00 in Dublin (IST, +01:00) is 11:00 in Los Angeles (PDT, -07:00). Before the fix the
	 * TZID was ignored entirely, so this rendered as 19:00.
	 *
	 * @covers ::from_ical_event
	 */
	public function test_foreign_tzid_event_converts_to_site_timezone(): void {
		$event = $this->get_events()['Foreign TZID timed event'][0];

		$this->assertSame( '2026-07-11 11:00', $event->start_time->format( 'Y-m-d H:i' ) );
		$this->assertSame( '2026-07-11 12:30', $event->end_time->format( 'Y-m-d H:i' ) );
	}

	/**
	 * A UTC (`Z` suffixed) event converts to the site timezone.
	 *
	 * @covers ::from_ical_event
	 */
	public function test_utc_event_converts_to_site_timezone(): void {
		$event = $this->get_events()['UTC timed event'][0];

		$this->assertSame( '2026-02-06 18:30', $event->start_time->format( 'Y-m-d H:i' ) );
		$this->assertFalse( $event->is_all_day );
	}

	/**
	 * `VALUE=DATE` events are flagged all-day and anchored to midnight in the site timezone.
	 *
	 * @covers ::from_ical_event
	 */
	public function test_all_day_event_is_flagged_and_starts_at_midnight(): void {
		$event = $this->get_events()['All day single'][0];

		$this->assertTrue( $event->is_all_day );
		$this->assertSame( '2026-08-08 00:00', $event->start_time->format( 'Y-m-d H:i' ) );
		$this->assertSame( 'America/Los_Angeles', $event->start_time->getTimezone()->getName() );
		// iCal DTEND is exclusive for all-day events.
		$this->assertSame( '2026-08-09 00:00', $event->end_time->format( 'Y-m-d H:i' ) );
	}

	/**
	 * Multi-day all-day events keep their exclusive end date.
	 *
	 * @covers ::from_ical_event
	 */
	public function test_multi_day_all_day_event(): void {
		$event = $this->get_events()['All day multi'][0];

		$this->assertTrue( $event->is_all_day );
		$this->assertSame( '2026-08-15 00:00', $event->start_time->format( 'Y-m-d H:i' ) );
		$this->assertSame( '2026-08-18 00:00', $event->end_time->format( 'Y-m-d H:i' ) );
	}

	/**
	 * Generated recurrence instances inherit the TZID, so each occurrence stays at 09:00 local
	 * even across the end of daylight saving time.
	 *
	 * @covers ::from_ical_event
	 */
	public function test_recurring_tzid_event_keeps_local_time_across_dst(): void {
		$events = $this->get_events()['TZID recurring across DST'];

		$this->assertCount( 3, $events );

		$starts = array_map( fn( Calendar_Event $event ) => $event->start_time->format( 'Y-m-d H:i T' ), $events );

		$this->assertSame(
			array(
				'2026-10-24 09:00 PDT',
				'2026-10-31 09:00 PDT',
				'2026-11-07 09:00 PST',
			),
			$starts
		);
	}
}
