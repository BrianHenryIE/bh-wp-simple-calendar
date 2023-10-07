<?php

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\WP_Includes\Deactivator
 */
class Deactivator_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::deactivate
	 */
	public function test_deactivate(): void {

		\WP_Mock::userFunction(
			'wp_unschedule_hook',
			array(
				'args'  => array( 'bh_wp_update_calendar_caches' ),
				'times' => 1,
			)
		);

		Deactivator::deactivate();
	}
}
