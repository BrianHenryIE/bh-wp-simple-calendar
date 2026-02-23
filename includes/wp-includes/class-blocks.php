<?php
/**
 * Register all blocks from the `build/` directory.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;

/**
 * Register each block using its block.json metadata.
 */
class Blocks {

	/**
	 * Constructor.
	 *
	 * @param Settings_Interface $settings The plugin settings.
	 * @param API_Interface      $api      The plugin API (for the render filter).
	 */
	public function __construct(
		protected Settings_Interface $settings,
		protected API_Interface $api,
	) {
	}

	/**
	 * Registers all blocks found in build/ subdirectories.
	 *
	 * @hooked init
	 */
	public function register_block(): void {

		$build_dir = constant( 'WP_PLUGIN_DIR' ) . '/' . dirname( $this->settings->get_plugin_basename() ) . '/build';

		if ( ! is_dir( $build_dir ) ) {
			return;
		}

		$block_dirs = glob( $build_dir . '/*/block.json' ) ?: array();

		foreach ( $block_dirs as $block_json ) {
			register_block_type( dirname( $block_json ) );
		}

		// Provide the API instance to the calendar render.php via filter.
		add_filter( 'simple_calendar_api_instance', fn() => $this->api );
	}
}
