<?php


namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Simple_Calendar\Unit_Testcase;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\API\API
 */
class API_Test extends Unit_Testcase {

	/**
	 * Check there is a regular cron job registered after the first calendar has been added.
	 */
	public function test_adding_calendar_to_cache_adds_cron_job(): void {

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array( API::CACHED_CALENDARS_OPTION_NAME, \WP_Mock\Functions::type( 'array' ) ),
				'times'  => 1,
				'return' => array(),
			)
		);

		\WP_Mock::userFunction(
			'update_option',
			array(
				'args'   => array( API::CACHED_CALENDARS_OPTION_NAME, \WP_Mock\Functions::type( 'array' ) ),
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'wp_get_scheduled_event',
			array(
				'args'   => array( Cron::UPDATE_CACHES_CRON_JOB ),
				'times'  => 1,
				'return' => false,
			)
		);

		\WP_Mock::userFunction(
			'wp_schedule_event',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'int' ), 'hourly', Cron::UPDATE_CACHES_CRON_JOB ),
				'times'  => 1,
				'return' => true,
			)
		);

		$api = new API( $this->logger );

		$api->add_post_ref_to_calendar_cache( 'http://example.org/calendar.ics', 123 );
	}
}
