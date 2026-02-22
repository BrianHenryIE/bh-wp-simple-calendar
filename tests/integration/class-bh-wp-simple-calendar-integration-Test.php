<?php
/**
 * Tests for BH_WP_Simple_Calendar main setup class. Tests the actions are correctly added.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\WP_Simple_Calendar\Admin\Admin_Assets;
use BrianHenryIE\WP_Simple_Calendar\Admin\Post;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\Cron;
use BrianHenryIE\WP_Simple_Calendar\WP_Includes\I18n;

/**
 * @coversNothing
 */
class BH_WP_Simple_Calendar_Integration_Test extends WPUnit_Testcase {

	/**
	 * Verify admin_enqueue_scripts action is correctly added for styles, at priority 10.
	 */
	public function test_action_admin_enqueue_scripts_styles() {

		$this->markTestSkipped( 'No styles added on admin' );

		$action_name       = 'admin_enqueue_scripts';
		$expected_priority = 10;
		$class_type        = Admin_Assets::class;
		$method_name       = 'enqueue_styles';

		global $wp_filter;

		self::assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

		$actions_hooked = $wp_filter[ $action_name ];

		self::assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

		$hooked_method = null;
		foreach ( $actions_hooked[ $expected_priority ] as $action ) {
			$action_function = $action['function'];
			if ( is_array( $action_function ) ) {
				if ( $action_function[0] instanceof $class_type ) {
					$hooked_method = $action_function[1];
				}
			}
		}

		self::assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

		self::assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );
	}

	/**
	 * Verify admin_enqueue_scripts action is added for scripts, at priority 10.
	 */
	public function test_action_admin_enqueue_scripts_scripts() {

		$this->markTestSkipped( 'No scripts added on admin' );

		$action_name       = 'admin_enqueue_scripts';
		$expected_priority = 10;
		$class_type        = Admin_Assets::class;
		$method_name       = 'enqueue_scripts';

		global $wp_filter;

		self::assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

		$actions_hooked = $wp_filter[ $action_name ];

		self::assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

		$hooked_method = null;
		foreach ( $actions_hooked[ $expected_priority ] as $action ) {
			$action_function = $action['function'];
			if ( is_array( $action_function ) ) {
				if ( $action_function[0] instanceof $class_type ) {
					$hooked_method = $action_function[1];
				}
			}
		}

		self::assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

		self::assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );
	}

	/**
	 * Verify action to call load textdomain is added.
	 */
	public function test_action_plugins_loaded_load_plugin_textdomain() {

		$action_name       = 'plugins_loaded';
		$expected_priority = 10;
		$class_type        = I18n::class;
		$method_name       = 'load_plugin_textdomain';

		global $wp_filter;

		self::assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

		$actions_hooked = $wp_filter[ $action_name ];

		self::assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

		$hooked_method = null;
		foreach ( $actions_hooked[ $expected_priority ] as $action ) {
			$action_function = $action['function'];
			if ( is_array( $action_function ) ) {
				if ( $action_function[0] instanceof $class_type ) {
					$hooked_method = $action_function[1];
				}
			}
		}

		self::assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

		self::assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );
	}

	/**
	 * Verify action on save_post is added.
	 */
	public function test_action_save_post_update_cache_posts_list() {

		$action_name       = 'save_post';
		$expected_priority = 10;
		$class_type        = Post::class;
		$method_name       = 'update_cache_posts_list';

		global $wp_filter;

		self::assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

		$actions_hooked = $wp_filter[ $action_name ];

		self::assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

		$hooked_method = null;
		foreach ( $actions_hooked[ $expected_priority ] as $action ) {
			$action_function = $action['function'];
			if ( is_array( $action_function ) ) {
				if ( $action_function[0] instanceof $class_type ) {
					$hooked_method = $action_function[1];
				}
			}
		}

		self::assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

		self::assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );
	}

	/**
	 * Verify cron job action is hooked.
	 */
	public function test_action_cron() {

		$action_name       = 'bh_wp_update_calendar_caches';
		$expected_priority = 10;
		$class_type        = Cron::class;
		$method_name       = 'update_calendars_caches';

		global $wp_filter;

		self::assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

		$actions_hooked = $wp_filter[ $action_name ];

		self::assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

		$hooked_method = null;
		foreach ( $actions_hooked[ $expected_priority ] as $action ) {
			$action_function = $action['function'];
			if ( is_array( $action_function ) ) {
				if ( $action_function[0] instanceof $class_type ) {
					$hooked_method = $action_function[1];
				}
			}
		}

		self::assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

		self::assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );
	}
}
