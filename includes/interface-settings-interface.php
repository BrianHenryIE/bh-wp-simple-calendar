<?php

namespace BrianHenryIE\WP_Simple_Calendar;

interface Settings_Interface {

	public function get_plugin_version(): string;

	public function get_log_level(): string;

	// This is used in admin notices.
	public function get_plugin_name(): string;

	// This is used in option names and URLs.
	public function get_plugin_slug(): string;

	// This is needed for the plugins.php logs link.
	public function get_plugin_basename(): string;
}
