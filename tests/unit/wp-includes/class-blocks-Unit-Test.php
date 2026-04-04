<?php

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\Unit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\WP_Includes\Blocks
 */
class Blocks_Unit_Test extends Unit_Testcase {

	protected function setUp(): void {
		parent::setUp();

		$build_dir  = codecept_root_dir( 'build' );
		$block_dirs = glob( $build_dir . '/*/block.json' );

		if ( empty( $block_dirs ) ) {
			$this->fail( 'Test needs you to run: npm run build' );
		}
	}

	/**
	 * @covers ::register_block
	 * @covers ::__construct
	 */
	public function test_register_block(): void {

		\Patchwork\redefine(
			'constant',
			function ( string $constant_name ) {
				switch ( $constant_name ) {
					case 'WP_PLUGIN_DIR':
						return realpath( codecept_root_dir( '/..' ) );
					default:
						return \Patchwork\relay( func_get_args() );
				}
			}
		);

		\WP_Mock::userFunction(
			'register_block_type',
			array(
				'times' => 9,
			)
		);

		$settings = self::makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_basename' => 'bh-wp-simple-calendar/bh-wp-simple-calendar.php',
			)
		);

		$api = $this->makeEmpty( API_Interface::class );

		$sut = new Blocks( $settings, $api );

		$sut->register_block();
	}
}
