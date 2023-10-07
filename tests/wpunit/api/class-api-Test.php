<?php
/**
 * Tests the API functions.
 *
 * @package simple-google-calendar-block
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\ColorLogger\ColorLogger;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\API\API
 */
class API_Test extends \Codeception\TestCase\WPTestCase {

	public function test_get_upcoming_events(): void {

		$api = new class(new ColorLogger() ) extends API {
			public function __construct( $logger ) {
				parent::__construct( $logger );

				$url = $this->get_calendar_url( '3bpg24atqjbsmhdb00ilcdrj5c@group.calendar.google.com' );

				$cache_key = $this->get_calendar_cache_option_name( $url );

				delete_option( $cache_key );
			}
		};

		add_filter(
			'pre_http_request',
			function ( $return_value, $parsed_args, $url ) {

				global $project_root_dir;
				$test_data = file_get_contents( $project_root_dir . '/tests/_data/saba-events.ics' );

				return array( 'body' => $test_data );
			},
			10,
			3
		);

		$events = $api->get_upcoming_events( '3bpg24atqjbsmhdb00ilcdrj5c@group.calendar.google.com', 30, 10 );

		$a = $events;
	}

	// protected get_calendar_ics

	// protected fetch_remote_calendar

	/**
	 * uses WordPress is_email function.
	 */
	public function test_calendar_id_email(): void {

		$api = new class( new ColorLogger() ) extends API {
			public function protected_get_calendar_url( $input ) {
				return $this->get_calendar_url( $input );
			}
		};

		$input = '3bpg24atqjbsmhdb00ilcdrj5c@group.calendar.google.com';

		// "Public address in ical format" from Google Calendar settings.
		$expected = 'https://calendar.google.com/calendar/ical/3bpg24atqjbsmhdb00ilcdrj5c%40group.calendar.google.com/public/basic.ics';

		$calendar_url = $api->protected_get_calendar_url( $input );

		$this->assertSame( $expected, $calendar_url );
	}

	public function test_calendar_id_url(): void {
		$api = new class( new ColorLogger() ) extends API {
			public function protected_get_calendar_url( $input ) {
				return $this->get_calendar_url( $input );
			}
		};

		$input = 'https://events.sacbike.org/calendar/subscribe';

		$expected = 'https://events.sacbike.org/calendar/subscribe';

		$calendar_url = $api->protected_get_calendar_url( $input );

		$this->assertSame( $expected, $calendar_url );
	}

	public function test_get_calendar_cache_option_name(): void {

		$api = new API( new ColorLogger() );

		$input = 'https://events.sacbike.org/calendar/subscribe';

		$expected = 'bh_wp_calendar_ebircsbusF2%radnelacF2%gro.ekibcas.stneveF2%F2%A3%sptth';

		$actual = $api->get_calendar_cache_option_name( $input );

		$this->assertSame( $expected, $actual );
	}
}
