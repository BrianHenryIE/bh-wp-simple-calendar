<?php

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\WP_Simple_Calendar\WPUnit_Testcase;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Frontend\Event_Field_Renderer
 */
class Event_Field_Renderer_Wpunit_Test extends WPUnit_Testcase {

	private string $orignal_timezone_option;

	/**
	 * Helper to create a mock WP_Block with context.
	 *
	 * @param array<string, mixed> $context
	 * @return object
	 */
	private function make_block( array $context ): object {
		return (object) array( 'context' => $context );
	}

	protected function setUp(): void {
		parent::setUp();
		$this->orignal_timezone_option = get_option( 'timezone_string', 'America/Los_Angeles' );
		update_option( 'timezone_string', 'America/Los_Angeles' );
		// get_block_wrapper_attributes() reads WP_Block_Supports::$block_to_render, which is
		// only set inside WordPress's block render pipeline. Prime it so the function works
		// when render() is called directly in tests.
		\WP_Block_Supports::$block_to_render = array(
			'blockName' => '',
			'attrs'     => array(),
		);
	}

	protected function tearDown(): void {
		\WP_Block_Supports::$block_to_render = null;
		update_option( 'timezone_string', $this->orignal_timezone_option );
		parent::tearDown();
	}

	/**
	 * All-day events are anchored to midnight in the site timezone and flagged via block context,
	 * so the calendar date (Saturday March 14) renders directly.
	 *
	 * @covers ::render
	 */
	public function test_render_date_all_day_shows_correct_day(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventStartTime' => '2026-03-14T00:00:00-07:00',
				'simple-calendar/eventEndTime'   => '2026-03-15T00:00:00-07:00',
				'simple-calendar/eventIsAllDay'  => true,
			)
		);

		$result = Event_Field_Renderer::render(
			$block,
			array(
				'dateFormat'  => 'l F j',
				'timeFormat'  => 'H:i',
				'showEndTime' => true,
			),
			'date',
			'time'
		);

		$this->assertStringContainsString( 'Saturday', $result );
		$this->assertStringNotContainsString( 'Friday', $result );
		$this->assertStringNotContainsString( '–', $result );
	}

	/**
	 * All-day event on a UTC site.
	 *
	 * @covers ::render
	 */
	public function test_render_date_all_day_on_utc_site_shows_correct_day(): void {
		update_option( 'timezone_string', 'UTC' );

		$block = $this->make_block(
			array(
				'simple-calendar/eventStartTime' => '2026-03-14T00:00:00+00:00',
				'simple-calendar/eventEndTime'   => '2026-03-15T00:00:00+00:00',
				'simple-calendar/eventIsAllDay'  => true,
			)
		);

		$result = Event_Field_Renderer::render(
			$block,
			array(
				'dateFormat'  => 'l F j',
				'timeFormat'  => 'H:i',
				'showEndTime' => true,
			),
			'date',
			'time'
		);

		$this->assertStringContainsString( 'Saturday', $result );
		$this->assertStringNotContainsString( '–', $result );
	}

	/**
	 * Multi-day all-day event shows start date en-dash end date when showEndTime is true.
	 * A 3-day event from March 13–15 is stored as DTSTART=March 13, DTEND=March 16 (exclusive).
	 *
	 * @covers ::render
	 */
	public function test_render_date_multi_day_all_day_shows_date_range(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventStartTime' => '2026-03-13T00:00:00-07:00',
				'simple-calendar/eventEndTime'   => '2026-03-16T00:00:00-07:00',
				'simple-calendar/eventIsAllDay'  => true,
			)
		);

		$result = Event_Field_Renderer::render(
			$block,
			array(
				'dateFormat'  => 'l F j',
				'timeFormat'  => 'H:i',
				'showEndTime' => true,
			),
			'date',
			'time'
		);

		$this->assertStringContainsString( 'Friday', $result );
		$this->assertStringContainsString( 'Sunday', $result );
		$this->assertStringContainsString( '–', $result );
	}

	/**
	 * Multi-day all-day event without showEndTime shows only the start date.
	 *
	 * @covers ::render
	 */
	public function test_render_date_multi_day_all_day_without_show_end_omits_end_date(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventStartTime' => '2026-03-13T00:00:00-07:00',
				'simple-calendar/eventEndTime'   => '2026-03-16T00:00:00-07:00',
				'simple-calendar/eventIsAllDay'  => true,
			)
		);

		$result = Event_Field_Renderer::render(
			$block,
			array(
				'dateFormat'  => 'l F j',
				'timeFormat'  => 'H:i',
				'showEndTime' => false,
			),
			'date',
			'time'
		);

		$this->assertStringContainsString( 'Friday', $result );
		$this->assertStringNotContainsString( '–', $result );
	}

	/**
	 * A timed event lasting exactly 24 hours is not an all-day event; it must still show its time.
	 * The previous duration-modulo heuristic misidentified these.
	 *
	 * @covers ::render
	 */
	public function test_render_date_timed_event_lasting_exactly_one_day_shows_time(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventStartTime' => '2026-03-13T09:30:00-07:00',
				'simple-calendar/eventEndTime'   => '2026-03-14T09:30:00-07:00',
				'simple-calendar/eventIsAllDay'  => false,
			)
		);

		$result = Event_Field_Renderer::render(
			$block,
			array(
				'dateFormat'  => 'l F j',
				'timeFormat'  => 'H:i',
				'showEndTime' => false,
			),
			'date',
			'time'
		);

		$this->assertStringContainsString( 'Friday March 13', $result );
		$this->assertStringContainsString( '09:30', $result );
	}
}
