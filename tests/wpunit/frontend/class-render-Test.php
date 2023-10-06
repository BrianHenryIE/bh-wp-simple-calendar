<?php
/**
 * Tests the widget.
 *
 * @package simple-google-calendar-widget
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\WP_Simple_Calendar\API\API;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Frontend\Renderer
 */
class Render_Test extends \Codeception\TestCase\WPTestCase {

	public function test_get_html() {

		$api = new class() extends API {
			public function __construct() {

				$url = $this->get_calendar_url( '3bpg24atqjbsmhdb00ilcdrj5c@group.calendar.google.com' );

				$cache_key = $this->get_calendar_cache_option_name( $url );

				delete_option( $cache_key );

			}
		};

		// add_filter( 'pre_http_request', function( $return_value, $parsed_args, $url ) {
		//
		// $test_data = file_get_contents( __DIR__ . '/../saba-events.ics' );
		//
		// return array('body'=>$test_data);
		// },10,3);

		$renderer = new Renderer( $api, 'simple-calendar', '1.0.0' );

		$html = $renderer->get_html( 'http://events.sacbike.org/calendar/subscribe', 150, 15 );

		$a = $html;

	}

}
