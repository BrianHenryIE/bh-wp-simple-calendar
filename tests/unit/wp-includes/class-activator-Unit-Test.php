<?php

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\Unit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\WP_Includes\Activator
 */
class Activator_Unit_Test extends Unit_Testcase {

	protected function setUp(): void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::activate
	 */
	public function test_activate(): void {

		\WP_Mock::userFunction(
			'wp_schedule_event',
			array(
				'args'  => array(
					\WP_Mock\Functions::type( 'int' ),
					'hourly',
					'bh_wp_update_calendar_caches',
				),
				'times' => 1,
			)
		);

		Activator::activate();
	}
}
