<?php

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_Simple_Calendar\Psr\Log\LoggerInterface;
use BrianHenryIE\WP_Simple_Calendar\Psr\Log\Test\TestLogger;
use Codeception\Test\Unit;
use WP_Mock;
use function Patchwork\restoreAll;

class Unit_Testcase extends Unit {

	/**
	 * @var LoggerInterface|TestLogger $logger
	 */
	protected LoggerInterface $logger;

	protected function setUp(): void {
		\WP_Mock::setUp();
		parent::setUp();
		$this->logger = new class() extends ColorLogger implements LoggerInterface {};
	}

	protected function tearDown(): void {
		parent::_tearDown();
		WP_Mock::tearDown();
		restoreAll();
	}
}
