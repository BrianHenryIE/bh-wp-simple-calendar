<?php

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;
use Codeception\Stub\Expected;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\WP_Includes\Blocks
 */
class Blocks_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::register_block
	 */
	public function test_register_block(): void {

		global $plugin_root_dir;

		\WP_Mock::userFunction(
			'register_block_type',
			array(
				'args'  => array( "{$plugin_root_dir}/build" ),
				'times' => 1,
			)
		);

		$settings = self::makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_basename' => Expected::once( 'bh-wp-simple-calendar/bh-wp-simple-calendar.php' ),
			)
		);

		\WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( 'bh-wp-simple-calendar/bh-wp-simple-calendar.php' ),
				'times'  => 1,
				'return' => $plugin_root_dir,
			)
		);

		$sut = new Blocks( $settings );

		$sut->register_block();
	}
}
