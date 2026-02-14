<?php
/**
 * Tests the widget.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Simple_Calendar\API\API;
use BrianHenryIE\WP_Simple_Calendar\Settings_Interface;
use BrianHenryIE\WP_Simple_Calendar\WPUnit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Frontend\Renderer
 */
class Render_Test extends WPUnit_Testcase {

	public function test_get_html(): void {

		$api = new class($this->logger) extends API {
			public function __construct( $logger ) {
				parent::__construct( $logger );

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

		$settings = self::makeEmpty( Settings_Interface::class );

		$renderer = new Renderer( $api, $settings );

		$html = $renderer->get_html( 'http://events.sacbike.org/calendar/subscribe', 150, 15 );

		self::markTestIncomplete();
	}
}
