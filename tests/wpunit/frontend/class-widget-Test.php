<?php
/**
 * Tests the widget.
 *
 * @package simple-google-calendar-widget
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

use BrianHenryIE\WP_Simple_Calendar\Frontend\Renderer;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Frontend\Widget
 */
class Widget_Test extends \Codeception\TestCase\WPTestCase {

	public function test_things_start(): void {

		$renderer = self::make( Renderer::class );

		$widget = new \BrianHenryIE\WP_Simple_Calendar\Frontend\Widget( $renderer );

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
