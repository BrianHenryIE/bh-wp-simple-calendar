<?php

namespace BrianHenryIE\WP_Simple_Calendar\WP_Includes;

use BrianHenryIE\WP_Simple_Calendar\API_Interface;
use BrianHenryIE\WP_Simple_Calendar\Unit_Testcase;
use WP_Error;
use WP_Mock;

/**
 * @coversDefaultClass \BrianHenryIE\WP_Simple_Calendar\WP_Includes\REST_API
 */
class REST_API_Test extends Unit_Testcase {

	/**
	 * @covers ::permissions_check
	 */
	public function test_permissions_check_allowed(): void {
		$api = $this->makeEmpty( API_Interface::class );

		$sut = new REST_API( $api );

		WP_Mock::userFunction( 'current_user_can' )
			->once()
			->with( 'edit_posts' )
			->andReturn( true );

		$result = $sut->permissions_check();

		$this->assertTrue( $result );
	}

	/**
	 * Denied check returns WP_Error — tested in wpunit suite since WP_Error requires WordPress.
	 *
	 * @covers ::permissions_check
	 */
	public function test_permissions_check_denied_returns_non_true(): void {
		// WP_Error is not available in unit tests. Verify the method doesn't return true.
		if ( ! class_exists( WP_Error::class ) ) {
			$this->markTestSkipped( 'WP_Error not available in unit tests — covered by wpunit.' );
		}

		$api = $this->makeEmpty( API_Interface::class );

		$sut = new REST_API( $api );

		WP_Mock::userFunction( 'current_user_can' )
			->once()
			->with( 'edit_posts' )
			->andReturn( false );

		$result = $sut->permissions_check();

		$this->assertInstanceOf( WP_Error::class, $result );
	}

	/**
	 * @covers ::refresh_cache
	 */
	public function test_refresh_cache_returns_200_on_success(): void {
		WP_Mock::userFunction( 'absint' )
			->with( 200 )
			->andReturn( 200 );

		$api = $this->makeEmpty(
			API_Interface::class,
			array(
				'refresh_calendar_cache' => true,
			),
		);

		$sut = new REST_API( $api );

		$request = $this->makeEmpty(
			\WP_REST_Request::class,
			array(
				'get_param' => 'https://example.org/calendar.ics',
			),
		);

		$response = $sut->refresh_cache( $request );

		$this->assertInstanceOf( \WP_REST_Response::class, $response );
		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( array( 'success' => true ), $response->get_data() );
	}

	/**
	 * @covers ::refresh_cache
	 */
	public function test_refresh_cache_returns_502_on_failure(): void {
		WP_Mock::userFunction( 'absint' )
			->with( 502 )
			->andReturn( 502 );

		$api = $this->makeEmpty(
			API_Interface::class,
			array(
				'refresh_calendar_cache' => false,
			),
		);

		$sut = new REST_API( $api );

		$request = $this->makeEmpty(
			\WP_REST_Request::class,
			array(
				'get_param' => 'https://example.org/calendar.ics',
			),
		);

		$response = $sut->refresh_cache( $request );

		$this->assertInstanceOf( \WP_REST_Response::class, $response );
		$this->assertSame( 502, $response->get_status() );
		$this->assertSame( array( 'success' => false ), $response->get_data() );
	}
}
