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
}
