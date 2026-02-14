<?php

namespace BrianHenryIE\WP_Simple_Calendar;

use BrianHenryIE\WP_Simple_Calendar\Psr\Log\LoggerInterface;
use BrianHenryIE\WP_Simple_Calendar\Psr\Log\Test\TestLogger;
use BrianHenryIE\ColorLogger\ColorLogger;
use lucatume\WPBrowser\TestCase\WPTestCase;

class WPUnit_Testcase extends WPTestCase {

	/**
	 * @var LoggerInterface|TestLogger $logger
	 */
	protected LoggerInterface $logger;

	protected function setUp(): void {
		parent::setUp();
		$this->logger = new class() extends ColorLogger implements LoggerInterface {};
	}
}
