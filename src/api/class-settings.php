<?php
/**
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Trait;
use BrianHenryIE\WP_Simple_Calendar\Psr\Log\LogLevel;

class Settings implements Settings_Interface, Logger_Settings_Interface {
	use Logger_Settings_Trait;

	public function get_plugin_version(): string {
		return defined( 'BH_WP_SIMPLE_CALENDAR_VERSION' )
			? constant( 'BH_WP_SIMPLE_CALENDAR_VERSION' )
			: '3.0.1';
	}

	public function get_log_level(): string {
		return LogLevel::INFO;
	}

	/**
	 * This is used in admin notices.
	 */
	public function get_plugin_name(): string {
		return 'Simple Calendar';
	}

	/**
	 * This is used in option names and URLs.
	 */
	public function get_plugin_slug(): string {
		return 'bh-wp-simple-calendar';
	}

	/**
	 * This is needed for the plugins.php logs link.
	 */
	public function get_plugin_basename(): string {
		return defined( 'BH_WP_SIMPLE_CALENDAR_BASENAME' )
		? constant( 'BH_WP_SIMPLE_CALENDAR_BASENAME' )
		: 'bh-wp-simple-calendar/bh-wp-simple-calendar.php';
	}
}
