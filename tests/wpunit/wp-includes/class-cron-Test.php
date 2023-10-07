<?php
/**
 * Test cron job mehtod behaves.
 *
 * @package BH_WP_Simple_Calendar
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Simple_Calendar\API\API;
use Codeception\Stub\Expected;


/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron
 */
class WpUnit_Cron_Test extends \Codeception\TestCase\WPTestCase {

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

		$logger = new ColorLogger();

		$cron = new Cron( $api_mock, $logger );

		$cron->update_calendars_caches();

		self::markTestIncomplete();
	}

	/**
	 * When cron runs, the function should run.
	 */
}
