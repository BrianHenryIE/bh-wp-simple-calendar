<?php
/**
 * @package PHP_Package_Name
 * @author  Your Name <email@example.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Simple_Calendar\Admin\Admin_Assets;
use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\API\Settings;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Frontend_Assets;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\I18n;
use WP_Mock\Matcher\AnyInstance;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\BH_WP_Simple_Calendar
 */
class BH_WP_Simple_Calendar_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		parent::setUp();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::set_locale
	 */
	public function test_set_locale_hooked(): void {

		\WP_Mock::expectActionAdded(
			'init',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

		$logger   = new ColorLogger();
		$settings = $this->make( Settings::class );
		$api      = self::makeEmpty( API::class );
		new BH_WP_Simple_Calendar( $api, $settings, $logger );
	}

	/**
	 * @covers ::define_admin_hooks
	 */
	public function test_admin_hooks(): void {

		$this->markTestSkipped( 'Admin_Assets not being used yet' );

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_styles' )
		);

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_scripts' )
		);

		$logger   = new ColorLogger();
		$settings = $this->make( Settings::class );
		$api      = self::makeEmpty( API::class );
		new BH_WP_Simple_Calendar( $api, $settings, $logger );
	}
}
