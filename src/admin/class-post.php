<?php
/**
 * When a post is saved, if it has a calendar in it, add that calendar to the list to be
 * cached by the cron job.
 *
 * If a calendar has just been removed, remove it from the cache list.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 */

namespace BrianHenryIE\WP_Simple_Calendar\Admin;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Block;
use WP_Post;

/**
 * Hooked on post save, include calendars on the list of calendars to cache.
 */
class Post {

	/**
	 * Constructor
	 *
	 * @param API_Interface $api Cache functions.
	 */
	public function __construct(
		protected API_Interface $api,
	) {
	}

	/**
	 * When a post is saved, check does it have a calendar that should be added to the caching list,
	 * otherwise remove it from any cache list.
	 *
	 * TODO: This doesn't detect when a single calendar is removed from a page containing multiple.
	 *
	 * This function parses the post_content, checking each block to see is it a calendar, updates the cache
	 * as appropriate, then schedules a cron to fetch the calendar.
	 *
	 * @hooked save_post
	 *
	 * @param int     $post_id Post ID which will be saved/removed from the cache list.
	 * @param WP_Post $post    Post with post content which will be checked.
	 * @param bool    $update  Updated flag which is not needed here.
	 */
	public function update_cache_posts_list( int $post_id, WP_Post $post, bool $update ): void {

		// TODO: Get ids of all posts with calendars, return early if this post is not one of them.

		$blocks = parse_blocks( $post->post_content );

		$all_blocks = $this->flatten_blocks( $blocks );

		$calendars_to_cache = array();

		foreach ( $all_blocks as $block ) {
			if ( Block::BLOCK_TYPE_NAME === $block['blockName'] ) {
				$calendars_to_cache[] = $block['attrs']['calendarId'];
			}
		}

		// TODO Check if the post contains this shortcode.

		if ( count( $calendars_to_cache ) > 0 ) {

			$calendars_to_cache = array_unique( $calendars_to_cache );

			foreach ( $calendars_to_cache as $calendar_url ) {
				$this->api->add_post_ref_to_calendar_cache( $calendar_url, $post_id );
			}
		} else {
			$this->api->remove_post_ref_from_calendar_cache( $post_id );
		}
	}

	/**
	 * Recursive function to return a flat array of all blocks on a page, i.e. to remove the hierarchy.
	 *
	 * @param array<array{innerBlocks?:array, blockName?:string, attrs:array<string,string>, innerHTML:string, innerContent:array<int,string>}> $blocks An array of blocks (which themselves are arrays).
	 *
	 * @return array<array{blockName:string, attrs:array<string,string>, innerHTML:string, innerContent:array<int,string>}> A flat array with all the blocks
	 */
	protected function flatten_blocks( array $blocks ): array {

		return array_reduce(
			$blocks,
			function ( array $carry, array $item ): array {
				if ( isset( $item['innerBlocks'] ) && ! empty( $item['innerBlocks'] ) ) {
					$carry = array_merge( $carry, $this->flatten_blocks( $item['innerBlocks'] ) );
				}
				if ( ! isset( $item['blockName'] ) ) {
					return $carry;
				}
				$carry[] = $item;
				return $carry;
			},
			array()
		);
	}
}
