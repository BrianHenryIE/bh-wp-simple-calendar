<?php

namespace BrianHenryIE\WP_Simple_Calendar\API;

use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\WP_Logger\Logger_Settings_Trait;
use Psr\Log\LogLevel;

class Settings implements Settings_Interface, Logger_Settings_Interface {
	use Logger_Settings_Trait;

	public function get_version(): string {
		return '2.0.0';
	}

	public function get_log_level(): string {
		return LogLevel::INFO;
	}

	// This is used in admin notices.
	public function get_plugin_name(): string {
		return 'Simple Calendar';
	}

	// This is used in option names and URLs.
	public function get_plugin_slug(): string {
		return 'bh-wp-simple-calendar';
	}

	// This is needed for the plugins.php logs link.
	public function get_plugin_basename(): string {
		return 'bh-wp-simple-calendar/bh-wp-simple-calendar.php';
	}
}
