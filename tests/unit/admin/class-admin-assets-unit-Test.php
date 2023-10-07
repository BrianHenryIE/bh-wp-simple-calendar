<?php

namespace BrianHenryIE\WP_Simple_Calendar\Admin;

use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Admin\Admin_Assets
 */
class Admin_Assets_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Verifies enqueue_styles() calls wp_enqueue_style() with appropriate parameters.
	 * Verifies the .css file exists.
	 *
	 * @covers ::__construct
	 * @covers ::enqueue_styles
	 *
	 * @see Admin::enqueue_styles()
	 * @see wp_enqueue_style()
	 */
	public function test_enqueue_styles(): void {

		global $plugin_root_dir;

		\WP_Mock::userFunction(
			'plugins_url',
			array(
				'args'   => array( 'assets/bh-wp-simple-calendar-admin.css', 'bh-wp-simple-calendar/bh-wp-simple-calendar.php' ),
				'times'  => 1,
				'return' => 'http://localhost/wp-content/plugins/bh-wp-simple-calendar/assets/bh-wp-simple-calendar-admin.css',
			)
		);

		$css_file = $plugin_root_dir . '/assets/bh-wp-simple-calendar-admin.css';
		$css_url  = 'http://localhost/wp-content/plugins/bh-wp-simple-calendar/assets/bh-wp-simple-calendar-admin.css';

		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times' => 1,
				'args'  => array(
					'bh-wp-simple-calendar',
					$css_url,
					array(),
					'1.2.3',
					'all',
				),
			)
		);

		$settings                    = $this->makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_basename' => 'bh-wp-simple-calendar/bh-wp-simple-calendar.php',
				'get_plugin_slug'     => 'bh-wp-simple-calendar',
				'get_plugin_version'  => '1.2.3',
			)
		);
		$bh_wp_simple_calendar_admin = new Admin_Assets( $settings );

		$bh_wp_simple_calendar_admin->enqueue_styles();

		$this->assertFileExists( $css_file );
	}

	/**
	 * Verifies enqueue_scripts() calls wp_enqueue_script() with appropriate parameters.
	 * Verifies the .js file exists.
	 *
	 * @covers ::enqueue_scripts
	 *
	 * @see Admin::enqueue_scripts()
	 * @see wp_enqueue_script()
	 */
	public function test_enqueue_scripts(): void {

		global $plugin_root_dir;

		\WP_Mock::userFunction(
			'plugins_url',
			array(
				'args'   => array( 'assets/bh-wp-simple-calendar-admin.js', 'bh-wp-simple-calendar/bh-wp-simple-calendar.php' ),
				'times'  => 1,
				'return' => 'http://localhost/wp-content/plugins/bh-wp-simple-calendar/assets/bh-wp-simple-calendar-admin.js',
			)
		);

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'times' => 1,
				'args'  => array_values(
					array(
						'handle'    => 'bh-wp-simple-calendar',
						'url'       => 'http://localhost/wp-content/plugins/bh-wp-simple-calendar/assets/bh-wp-simple-calendar-admin.js',
						'deps'      => array( 'jquery' ),
						'ver'       => '1.2.3',
						'in_footer' => true,
					)
				),
			)
		);

		$settings                    = $this->makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_basename' => 'bh-wp-simple-calendar/bh-wp-simple-calendar.php',
				'get_plugin_slug'     => 'bh-wp-simple-calendar',
				'get_plugin_version'  => '1.2.3',
			)
		);
		$bh_wp_simple_calendar_admin = new Admin_Assets( $settings );

		$bh_wp_simple_calendar_admin->enqueue_scripts();

		$this->assertFileExists( $plugin_root_dir . '/assets/bh-wp-simple-calendar-admin.js' );
	}
}
