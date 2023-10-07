<?php
/**
 * Tests the widget.
 *
 * @package simple-google-calendar-widget
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */


/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Frontend\Widget
 */
class Widget_Test extends \Codeception\TestCase\WPTestCase {

	public function test_things_start() {

		$api = null;

		$widget = new \BrianHenryIE\WP_Simple_Calendar\Frontend\Widget( $api );

		echo 'widget id: ' . $widget->id;

		$this->assertNotNull( $widget );
	}

	private function an_instance() {

		$instance = array(
			'title'             => '',
			'event_count'       => '5',
			'event_period'      => '366', // Days of events to show
			'cache_time'        => '60', // Minutes until refresh
			'calendar_id'       => '',
			'dateformat_lg'     => 'l jS \of F',
			'suffix_lg_class'   => '',
			'suffix_lgi_class'  => '',
			'suffix_lgia_class' => '',
		);

		return $instance;
	}

	public function test_widget_output() {

		// Pre-populate the transient for WordPress to pull
	}
}
