<?php
/**
 * Tests the API functions.
 *
 * @package simple-google-calendar-block
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar\API;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\API\API
 */
class API_Test extends \Codeception\TestCase\WPTestCase {


	// public get_upcoming_events

	public function test_get_upcoming_events() {

		$api = new class() extends API {
			public function __construct() {

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
	public function test_calendar_id_email() {

		$api = new class() extends API {
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

	public function test_calendar_id_url() {
		$api = new class() extends API {
			public function protected_get_calendar_url( $input ) {
				return $this->get_calendar_url( $input );
			}
		};

		$input = 'https://events.sacbike.org/calendar/subscribe';

		$expected = 'https://events.sacbike.org/calendar/subscribe';

		$calendar_url = $api->protected_get_calendar_url( $input );

		$this->assertSame( $expected, $calendar_url );
	}

	public function test_get_calendar_cache_option_name() {

		$api = new API();

		$input = 'https://events.sacbike.org/calendar/subscribe';

		$expected = 'bh_wp_calendar_ebircsbusF2%radnelacF2%gro.ekibcas.stneveF2%F2%A3%sptth';

		$actual = $api->get_calendar_cache_option_name( $input );

		$this->assertSame( $expected, $actual );
	}


	// public update_caches

	// protected add_post_ref_to_calendar_cache

	// protected get_calendar_from_cache


	// protected save_calendar_to_cache
}
