<?php

namespace BrianHenryIE\WP_Simple_Calendar\Frontend;

use BrianHenryIE\WP_Simple_Calendar\Unit_Testcase;
use WP_Mock;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\Frontend\Event_Field_Renderer
 */
class Event_Field_Renderer_Test extends Unit_Testcase {

	/**
	 * Helper to create a mock WP_Block with context.
	 *
	 * @param array<string, mixed> $context
	 * @return object
	 */
	private function make_block( array $context ): object {
		return (object) array( 'context' => $context );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_title(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventSummary' => 'Community Meetup',
				'simple-calendar/eventUrl'     => null,
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="wp-block-simple-calendar-event-title simple-calendar-event-title"' );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array( 'linkToUrl' => true ), 'title', 'h3' );

		$this->assertStringContainsString( 'Community Meetup', $result );
		$this->assertStringContainsString( '<h3', $result );
		$this->assertStringNotContainsString( '<a', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_title_with_link(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventSummary' => 'Linked Event',
				'simple-calendar/eventUrl'     => 'https://example.com/event',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-title"' );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		WP_Mock::userFunction( 'esc_url' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array( 'linkToUrl' => true ), 'title', 'h3' );

		$this->assertStringContainsString( '<a href="https://example.com/event">', $result );
		$this->assertStringContainsString( 'Linked Event', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_title_without_link(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventSummary' => 'No Link Event',
				'simple-calendar/eventUrl'     => 'https://example.com',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-title"' );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array( 'linkToUrl' => false ), 'title', 'h3' );

		$this->assertStringNotContainsString( '<a', $result );
		$this->assertStringContainsString( 'No Link Event', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_date(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventStartTime' => '2026-03-14T11:30:00-07:00',
				'simple-calendar/eventEndTime'   => '2026-03-14T14:00:00-07:00',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-date"' );

		WP_Mock::userFunction( 'wp_date' )
			->andReturnUsing( fn( $format, $ts ) => date( $format, $ts ) );

		$result = Event_Field_Renderer::render(
			$block,
			array(
				'dateFormat'  => 'l F j, H:i',
				'showEndTime' => false,
			),
			'date',
			'time'
		);

		$this->assertStringContainsString( '<time', $result );
		$this->assertStringContainsString( 'Saturday', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_date_with_end_time(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventStartTime' => '2026-03-14T11:30:00-07:00',
				'simple-calendar/eventEndTime'   => '2026-03-14T14:00:00-07:00',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-date"' );

		WP_Mock::userFunction( 'wp_date' )
			->andReturnUsing( fn( $format, $ts ) => date( $format, $ts ) );

		$result = Event_Field_Renderer::render(
			$block,
			array(
				'dateFormat'  => 'H:i',
				'showEndTime' => true,
			),
			'date',
			'time'
		);

		$this->assertStringContainsString( '–', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_description(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventDescription' => 'A <b>bold</b> description.',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-description"' );

		WP_Mock::userFunction( 'wp_kses_post' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array(), 'description', 'div' );

		$this->assertStringContainsString( 'A <b>bold</b> description.', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_location(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventLocation' => 'Old Sacramento, CA',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-location"' );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array(), 'location', 'div' );

		$this->assertStringContainsString( 'Old Sacramento, CA', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_status(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventStatus' => 'CANCELLED',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-status"' );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array(), 'status', 'div' );

		$this->assertStringContainsString( 'Cancelled', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_recurrence_when_recurring(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventIsRecurring'          => true,
				'simple-calendar/eventRecurrenceDescription' => 'Every week on Tuesday',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-recurrence"' );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array(), 'recurrence', 'div' );

		$this->assertStringContainsString( 'Every week on Tuesday', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_recurrence_when_not_recurring(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventIsRecurring'          => false,
				'simple-calendar/eventRecurrenceDescription' => null,
			)
		);

		$result = Event_Field_Renderer::render( $block, array(), 'recurrence', 'div' );

		$this->assertEmpty( $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_url(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventUrl' => 'https://example.com/event',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-url"' );

		WP_Mock::userFunction( 'esc_url' )
			->andReturnUsing( fn( $s ) => $s );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array(), 'url', 'div' );

		$this->assertStringContainsString( '<a href="https://example.com/event">', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_url_with_custom_link_text(): void {
		$block = $this->make_block(
			array(
				'simple-calendar/eventUrl' => 'https://example.com/event',
			)
		);

		WP_Mock::userFunction( 'get_block_wrapper_attributes' )
			->once()
			->andReturn( 'class="simple-calendar-event-url"' );

		WP_Mock::userFunction( 'esc_url' )
			->andReturnUsing( fn( $s ) => $s );

		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( fn( $s ) => $s );

		$result = Event_Field_Renderer::render( $block, array( 'linkText' => 'View Event' ), 'url', 'div' );

		$this->assertStringContainsString( '>View Event</a>', $result );
	}

	/**
	 * @covers ::render
	 */
	public function test_render_empty_context_returns_empty(): void {
		$block = $this->make_block( array() );

		$result = Event_Field_Renderer::render( $block, array(), 'title', 'h3' );

		$this->assertEmpty( $result );
	}
}
