<?php
/**
 * Tests the widget.
 *
 * @package brianhenryie/bh-wp-simple-calendar
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Renderer;
use BrianHenryIE\WP_Simple_Calendar\Frontend\Widget;
use BrianHenryIE\WP_Simple_Calendar\WPUnit_Testcase;

/**
 * @coversDefaultClass Widget
 */
class Widget_Test extends WPUnit_Testcase {

	public function test_things_start(): void {

		$renderer = self::make( Renderer::class );
		$api      = self::makeEmpty( API_Interface::class );

		$widget = new Widget( $renderer, $api );

		self::markTestIncomplete();
	}

	protected function an_instance(): array {

		$instance = array(
			'title'             => '',
			'event_count'       => '5',
			'event_period'      => '366', // Days of events to show.
			'cache_time'        => '60', // Minutes until refresh.
			'calendar_id'       => '',
			'dateformat_lg'     => 'l jS \of F',
			'suffix_lg_class'   => '',
			'suffix_lgi_class'  => '',
			'suffix_lgia_class' => '',
		);

		return $instance;
	}

	public function test_widget_output(): void {

		// Pre-populate the transient for WordPress to pull.

		self::markTestIncomplete();
	}
}
