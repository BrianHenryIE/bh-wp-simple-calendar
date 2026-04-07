<?php

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\Unit_Testcase;
use DateTimeImmutable;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\API\Calendar_Event
 */
class Calendar_Event_Test extends Unit_Testcase {

	/**
	 * @covers ::__construct
	 */
	public function test_constructor_basic_properties(): void {
		$start = new DateTimeImmutable( '2026-03-01T10:00:00+00:00' );
		$end   = new DateTimeImmutable( '2026-03-01T12:00:00+00:00' );

		$event = new Calendar_Event(
			summary: 'Test Event',
			status: 'CONFIRMED',
			start_time: $start,
			end_time: $end,
		);

		$this->assertSame( 'Test Event', $event->summary );
		$this->assertSame( 'CONFIRMED', $event->status );
		$this->assertSame( $start, $event->start_time );
		$this->assertSame( $end, $event->end_time );
		$this->assertNull( $event->url );
		$this->assertNull( $event->description );
		$this->assertNull( $event->location );
		$this->assertNull( $event->uid );
		$this->assertFalse( $event->is_recurring );
		$this->assertNull( $event->recurrence_rule );
		$this->assertNull( $event->recurrence_description );
	}

	/**
	 * @covers ::__construct
	 */
	public function test_constructor_all_properties(): void {
		$start = new DateTimeImmutable( '2026-03-01T10:00:00+00:00' );
		$end   = new DateTimeImmutable( '2026-03-01T12:00:00+00:00' );

		$event = new Calendar_Event(
			summary: 'Recurring Meeting',
			status: 'CONFIRMED',
			start_time: $start,
			end_time: $end,
			url: 'https://example.com',
			description: 'Weekly sync',
			location: 'Room 101',
			uid: 'abc123@example.com',
			is_recurring: true,
			recurrence_rule: 'FREQ=WEEKLY;BYDAY=TU',
			recurrence_description: 'Every week on Tuesday',
		);

		$this->assertSame( 'https://example.com', $event->url );
		$this->assertSame( 'Weekly sync', $event->description );
		$this->assertSame( 'Room 101', $event->location );
		$this->assertSame( 'abc123@example.com', $event->uid );
		$this->assertTrue( $event->is_recurring );
		$this->assertSame( 'FREQ=WEEKLY;BYDAY=TU', $event->recurrence_rule );
		$this->assertSame( 'Every week on Tuesday', $event->recurrence_description );
	}

	/**
	 * @covers ::to_array
	 */
	public function test_to_array(): void {
		$start = new DateTimeImmutable( '2026-03-01T10:00:00+00:00' );
		$end   = new DateTimeImmutable( '2026-03-01T12:00:00+00:00' );

		$event = new Calendar_Event(
			summary: 'Test Event',
			status: 'CONFIRMED',
			start_time: $start,
			end_time: $end,
			url: 'https://example.com',
			description: 'A description',
			location: 'A location',
			uid: 'uid-1',
			is_recurring: true,
			recurrence_rule: 'FREQ=DAILY',
			recurrence_description: 'Every day',
		);

		$array = $event->to_array();

		$this->assertSame( 'Test Event', $array['summary'] );
		$this->assertSame( 'CONFIRMED', $array['status'] );
		$this->assertSame( '2026-03-01T10:00:00+00:00', $array['startTime'] );
		$this->assertSame( '2026-03-01T12:00:00+00:00', $array['endTime'] );
		$this->assertSame( 'https://example.com', $array['url'] );
		$this->assertSame( 'A description', $array['description'] );
		$this->assertSame( 'A location', $array['location'] );
		$this->assertSame( 'uid-1', $array['uid'] );
		$this->assertTrue( $array['isRecurring'] );
		$this->assertSame( 'FREQ=DAILY', $array['recurrenceRule'] );
		$this->assertSame( 'Every day', $array['recurrenceDescription'] );
	}

	/**
	 * @covers ::to_array
	 */
	public function test_to_array_null_optional_fields(): void {
		$event = new Calendar_Event(
			summary: 'Minimal',
			status: 'CONFIRMED',
			start_time: new DateTimeImmutable( '2026-01-01T00:00:00+00:00' ),
			end_time: new DateTimeImmutable( '2026-01-01T01:00:00+00:00' ),
		);

		$array = $event->to_array();

		$this->assertNull( $array['url'] );
		$this->assertNull( $array['description'] );
		$this->assertNull( $array['location'] );
		$this->assertNull( $array['uid'] );
		$this->assertFalse( $array['isRecurring'] );
		$this->assertNull( $array['recurrenceRule'] );
		$this->assertNull( $array['recurrenceDescription'] );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_daily(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=DAILY' );
		$this->assertSame( 'Every day', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_daily_interval(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=DAILY;INTERVAL=3' );
		$this->assertSame( 'Every 3 days', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_weekly(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=WEEKLY' );
		$this->assertSame( 'Every week', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_weekly_with_byday(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=WEEKLY;BYDAY=TU' );
		$this->assertSame( 'Every week on Tuesday', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_weekly_with_multiple_days(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=WEEKLY;BYDAY=MO,WE,FR' );
		$this->assertSame( 'Every week on Monday, Wednesday, Friday', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_biweekly_with_day(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=WEEKLY;INTERVAL=2;BYDAY=TH' );
		$this->assertSame( 'Every 2 weeks on Thursday', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_monthly(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=MONTHLY' );
		$this->assertSame( 'Every month', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_monthly_with_bymonthday(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=MONTHLY;BYMONTHDAY=15' );
		$this->assertSame( 'Every month on the 15th', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_monthly_first_monday(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=MONTHLY;BYDAY=1MO' );
		$this->assertSame( 'Every month on 1st Monday', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_yearly(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=YEARLY' );
		$this->assertSame( 'Every year', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_yearly_with_bymonth(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=YEARLY;BYMONTH=1,6' );
		$this->assertSame( 'Every year in January, June', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_unknown_freq_returns_raw(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=SECONDLY' );
		$this->assertSame( 'FREQ=SECONDLY', $result );
	}

	/**
	 * @covers ::parse_rrule_to_description
	 */
	public function test_parse_complex_rule(): void {
		$result = Calendar_Event::parse_rrule_to_description( 'FREQ=MONTHLY;INTERVAL=2;BYDAY=1TU;BYMONTH=3,6,9,12' );
		$this->assertSame( 'Every 2 months on 1st Tuesday in March, June, September, December', $result );
	}
}
