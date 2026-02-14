<?php
/**
 * Test cron job mehtod behaves.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\WPUnit_Testcase;
use Codeception\Stub\Expected;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron
 */
class WpUnit_Cron_Test extends WPUnit_Testcase {

	/**
	 * When the cron function runs, the API method should be called.
	 */
	public function test_call_method_directly(): void {

		$api_mock = self::make(
			API::class,
			array(
				'update_caches' => Expected::once(),
			)
		);

		$cron = new Cron( $api_mock, $this->logger );

		$cron->update_calendars_caches();

		self::markTestIncomplete();
	}

	/**
	 * When cron runs, the function should run.
	 */
}
