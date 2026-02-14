<?php

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\Unit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\API\Settings
 */
class Settings_Unit_Test extends Unit_Testcase {

	/**
	 * @covers ::get_plugin_version
	 */
	public function test_get_plugin_version_concur(): void {
		$sut = new Settings();

		global $plugin_path_php;

		$root_plugin_file = file_get_contents( $plugin_path_php ) ?: '';

		if ( false === preg_match_all( '/.*version.*(\d+\.\d+\.\d+)/i', $root_plugin_file, $output_array ) ) {
			self::fail( 'Failed to parse versions from root plugin file.' );
		}

		self::assertEquals( $output_array[1][0], $sut->get_plugin_version() );
		self::assertEquals( $output_array[1][1], $sut->get_plugin_version() );
	}

	/**
	 * @covers ::get_plugin_version
	 */
	public function test_get_plugin_version(): void {

		\Patchwork\redefine(
			'defined',
			function ( string $constant_value ): bool {
				return false;
			}
		);

		$sut = new Settings();

		self::assertEquals( '3.0.1', $sut->get_plugin_version() );

		\Patchwork\restoreAll();
	}

	/**
	 * @covers ::get_plugin_version
	 */
	public function test_get_plugin_version_defined(): void {

		\Patchwork\redefine(
			'defined',
			function ( string $constant_value ): bool {
				return true;
			}
		);

		\Patchwork\redefine(
			'constant',
			function ( string $constant_value ): string {
				return '3.0.0d';
			}
		);

		$sut = new Settings();

		self::assertEquals( '3.0.0d', $sut->get_plugin_version() );

		\Patchwork\restoreAll();
	}

	/**
	 * @covers ::get_plugin_basename
	 */
	public function test_get_plugin_basename(): void {

		\Patchwork\redefine(
			'defined',
			function ( string $constant_value ): bool {
				return false;
			}
		);

		$sut = new Settings();

		self::assertEquals( 'bh-wp-simple-calendar/bh-wp-simple-calendar.php', $sut->get_plugin_basename() );

		\Patchwork\restoreAll();
	}

	/**
	 * @covers ::get_plugin_basename
	 */
	public function test_get_plugin_basename_defined(): void {

		\Patchwork\redefine(
			'defined',
			function ( string $constant_value ): bool {
				return true;
			}
		);

		\Patchwork\redefine(
			'constant',
			function ( string $constant_value ): string {
				return 'defined-bh-wp-simple-calendar/bh-wp-simple-calendar.php';
			}
		);

		$sut = new Settings();

		self::assertEquals( 'defined-bh-wp-simple-calendar/bh-wp-simple-calendar.php', $sut->get_plugin_basename() );

		\Patchwork\restoreAll();
	}

	/**
	 * @covers ::get_plugin_name
	 */
	public function test_get_plugin_name(): void {

		$sut = new Settings();

		self::assertEquals( 'Simple Calendar', $sut->get_plugin_name() );
	}
}
