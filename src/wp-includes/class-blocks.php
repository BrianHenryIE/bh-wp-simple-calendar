<?php


namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

class Blocks {
	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function create_block_bh_wp_simple_calendar_block_init() {
		register_block_type( __DIR__ . '/build' );
	}
}
