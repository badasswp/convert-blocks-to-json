<?php

namespace ConvertBlocksToJSON\Tests\Core;

use WP_Mock;
use Mockery;
use WP_Mock\Tools\TestCase;

use ConvertBlocksToJSON\Core\Container;
use ConvertBlocksToJSON\Services\Boot;
use ConvertBlocksToJSON\Services\Blocks;
use ConvertBlocksToJSON\Services\Routes;
use ConvertBlocksToJSON\Services\Scripts;

/**
 * @covers \ConvertBlocksToJSON\Core\Container::__construct
 * @covers \ConvertBlocksToJSON\Services\Boot::register
 * @covers \ConvertBlocksToJSON\Services\Blocks::register
 * @covers \ConvertBlocksToJSON\Services\Routes::register
 * @covers \ConvertBlocksToJSON\Services\Scripts::register
 */
class ContainerTest extends TestCase {
	public Container $container;

	public function setUp(): void {
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_container_contains_required_services() {
		$this->container = new Container();

		$this->assertTrue( in_array( Boot::class, Container::$services, true ) );
		$this->assertTrue( in_array( Blocks::class, Container::$services, true ) );
		$this->assertTrue( in_array( Routes::class, Container::$services, true ) );
		$this->assertTrue( in_array( Scripts::class, Container::$services, true ) );
	}
}
