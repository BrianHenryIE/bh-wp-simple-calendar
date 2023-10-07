<?php


namespace BrianHenryIE\WP_Simple_Calendar\API;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\API\API
 */
class API_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	// protected get_calendar_cache_option_name

	/**
	 * Check there is a regular cron job registered after the first calendar has been added.
	 */
	public function test_adding_calendar_to_cache_adds_cron_job() {

		$api = new API();

		$api->add_post_ref_to_calendar_cache( 'http://example.org/calendar.ics', 123 );

		$this->assertTrue( false );
	}
}
