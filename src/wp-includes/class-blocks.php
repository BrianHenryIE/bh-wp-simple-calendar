<?php
/**
 * Register the React block with WordPress.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

/**
 * Register the block from the `/build` directory.
 */
class Blocks {
	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function register_block(): void {
		register_block_type( __DIR__ . '/build' );
	}
}
