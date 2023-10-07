<?php
/**
 * Tests the Post class.
 *
 * @package simple-google-calendar-block
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar\Admin;

use BrianHenryIE\WP_Simple_Calendar\API\API;
use Codeception\Stub\Expected;
use stdClass;
use WP_Post;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Admin\Post
 */
class Post_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * We can't just iterate over the blocks since blocks can contain blocks,
	 * e.g. inside a column.
	 *
	 * In `inner-block.post.txt` there is a columns block, with two columns, and one has a calendar = 4.
	 */
	public function test_get_inner_block() {

		$post_tester = new class( null ) extends Post {
			public function test_flatten_blocks( $blocks ) {
				return $this->flatten_blocks( $blocks );
			}
		};

		$post_content = file_get_contents( __DIR__ . '/../../_data/inner-block.post.txt' );

		$blocks = parse_blocks( $post_content );

		$all_blocks = $post_tester->test_flatten_blocks( $blocks );

		$this->assertCount( 4, $all_blocks );
	}

	/**
	 * Happy path for when a calendar is added to the post.
	 *
	 * In simple.post.txt we have on single brianhenryie/simple-calendar block which should mean API
	 * is told to add it to its cache list.
	 */
	public function test_read_post_content_calendar_added() {

		$post_content = file_get_contents( __DIR__ . '/../../_data/simple.post.txt' );

		$mock_post = new class( $post_content ) {
			public $post_content;
			public function __construct( $post_content ) {
				$this->post_content = $post_content;
			}
		};

		$api_mock = $this->make(
			API::class,
			array(
				'add_post_ref_to_calendar_cache' => Expected::once(),
			)
		);

		$post = new Post( $api_mock, '', '' );

		$post->update_cache_posts_list( null, $mock_post, null );
	}


	/**
	 * Happy path for when a calendar is removed from the post.
	 */
	public function test_read_post_content_calendar_removed() {

		$mock_post = new class() {
			public $post_content = '';
		};

		$api_mock = $this->make(
			API::class,
			array(
				'remove_post_ref_from_calendar_cache' => Expected::once(),
			)
		);

		$post = new Post( $api_mock, '', '' );

		$post->update_cache_posts_list( null, $mock_post, null );
	}
}
