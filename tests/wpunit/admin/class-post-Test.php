<?php
/**
 * Tests the Post class.
 *
 * @package simple-google-calendar-block
 */

namespace BrianHenryIE\WP_Simple_Calendar\Admin;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use Codeception\Stub\Expected;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Admin\Post
 */
class Post_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * We can't just iterate over the blocks since blocks can contain blocks,
	 * e.g. inside a column.
	 *
	 * In `inner-block.post.txt` there is a columns block, with two columns, and one has a calendar = 4.
	 *
	 * @covers ::flatten_blocks
	 */
	public function test_flatten_blocks(): void {

		$reflection_class  = new \ReflectionClass( Post::class );
		$reflection_method = $reflection_class->getMethod( 'flatten_blocks' );
		$reflection_method->setAccessible( true );

		$api = self::makeEmpty( API_Interface::class );
		$sut = new Post( $api );

		$post_content = file_get_contents( __DIR__ . '/../../_data/inner-block.post.txt' ) ?: '';
		$blocks       = parse_blocks( $post_content );

		$result = $reflection_method->invokeArgs( $sut, array( $blocks ) );

		self::assertCount( 4, $result );
	}

	/**
	 * Happy path for when a calendar is added to the post.
	 *
	 * In simple.post.txt we have on single brianhenryie/simple-calendar block which should mean API
	 * is told to add it to its cache list.
	 */
	public function test_read_post_content_calendar_added(): void {

		$post_content = file_get_contents( __DIR__ . '/../../_data/simple.post.txt' ) ?: '';

		$mock_post               = new \WP_Post( new \stdClass() );
		$mock_post->post_content = $post_content;

		$api_mock = self::makeEmpty(
			API_Interface::class,
			array(
				'add_post_ref_to_calendar_cache' => Expected::once(),
			)
		);

		$post = new Post( $api_mock );

		$post->update_cache_posts_list( 1, $mock_post, true );
	}

	/**
	 * Happy path for when a calendar is removed from the post.
	 */
	public function test_read_post_content_calendar_removed(): void {

		$mock_post = new \WP_Post( new \stdClass() );

		$api_mock = self::makeEmpty(
			API_Interface::class,
			array(
				'remove_post_ref_from_calendar_cache' => Expected::once( true ),
			)
		);

		$post = new Post( $api_mock );

		$post->update_cache_posts_list( 2, $mock_post, true );
	}
}
