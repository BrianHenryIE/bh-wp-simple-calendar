<?php

namespace BrianHenryIE\WP_Simple_Calendar;

class Unit_Testcase extends \Codeception\Test\Unit {
	protected function setUp(): void {
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::_tearDown();
		\WP_Mock::tearDown();
		\Patchwork\restoreAll();
	}
}
