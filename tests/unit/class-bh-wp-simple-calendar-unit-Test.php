<?php
/**
 * @package PHP_Package_Name
 * @author  Your Name <email@example.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Simple_Calendar\Admin\Admin_Assets;
use BrianHenryIE\WP_Simple_Calendar\Admin\Post;
use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\API\Settings;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Block;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Blocks;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\I18n;
use lucatume\DI52\Container;
use Psr\Log\LoggerInterface;
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
	 * @covers ::__construct
	 */
	public function test_set_locale_hooked(): void {

		\WP_Mock::expectActionAdded(
			'plugins_loaded',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

		$container = new Container();
		$container->bind( LoggerInterface::class, ColorLogger::class );
		$container->bind( API_Interface::class, API::class );
		$container->bind( Settings_Interface::class, Settings::class );
		new BH_WP_Simple_Calendar( $container );
	}

	/**
	 * @covers ::define_admin_hooks
	 */
	public function test_admin_hooks(): void {

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_styles' )
		);

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Admin_Assets::class ), 'enqueue_scripts' )
		);

		$container = new Container();
		$container->bind( LoggerInterface::class, ColorLogger::class );
		$container->bind( API_Interface::class, API::class );
		$container->bind( Settings_Interface::class, Settings::class );
		new BH_WP_Simple_Calendar( $container );
	}

	/**
	 * @covers ::define_post_hooks
	 */
	public function test_post_hooks(): void {

		\WP_Mock::expectActionAdded(
			'save_post',
			array( new AnyInstance( Post::class ), 'update_cache_posts_list' ),
			10,
			3
		);

		$container = new Container();
		$container->bind( LoggerInterface::class, ColorLogger::class );
		$container->bind( API_Interface::class, API::class );
		$container->bind( Settings_Interface::class, Settings::class );
		new BH_WP_Simple_Calendar( $container );
	}

	/**
	 * @covers ::define_cron_hooks
	 */
	public function test_cron_hooks(): void {

		\WP_Mock::expectActionAdded(
			Cron::UPDATE_CACHES_CRON_JOB,
			array( new AnyInstance( Cron::class ), 'update_calendars_caches' )
		);

		$container = new Container();
		$container->bind( LoggerInterface::class, ColorLogger::class );
		$container->bind( API_Interface::class, API::class );
		$container->bind( Settings_Interface::class, Settings::class );
		new BH_WP_Simple_Calendar( $container );
	}

	/**
	 * @covers ::define_block_hooks
	 */
	public function test_block_hooks(): void {

		\WP_Mock::expectActionAdded(
			'init',
			array( new AnyInstance( Blocks::class ), 'register_block' )
		);

		$container = new Container();
		$container->bind( LoggerInterface::class, ColorLogger::class );
		$container->bind( API_Interface::class, API::class );
		$container->bind( Settings_Interface::class, Settings::class );
		new BH_WP_Simple_Calendar( $container );
	}

	/**
	 * @covers ::define_frontend_hooks
	 */
	public function test_frontend_hooks(): void {

		\WP_Mock::expectActionAdded(
			'init',
			array( new AnyInstance( Block::class ), 'register_block' )
		);

		$container = new Container();
		$container->bind( LoggerInterface::class, ColorLogger::class );
		$container->bind( API_Interface::class, API::class );
		$container->bind( Settings_Interface::class, Settings::class );
		new BH_WP_Simple_Calendar( $container );
	}
}
