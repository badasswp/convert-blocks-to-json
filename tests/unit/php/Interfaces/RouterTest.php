<?php

namespace ConvertBlocksToJSON\Tests\Interfaces;

use WP_Mock;
use Mockery;
use WP_Mock\Tools\TestCase;
use ConvertBlocksToJSON\Interfaces\Router;

/**
 * @covers \ConvertBlocksToJSON\Interfaces\Router::rest_callback
 */
class RouterTest extends TestCase {
	public Router $router;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->router = $this->getMockForAbstractClass( Router::class );
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_rest_callback() {
		$this->router->expects( $this->once() )
			->method( 'rest_callback' );

		$request = Mockery::mock( \WP_REST_Request::class )->makePartial();

		$this->router->rest_callback( $request );

		$this->assertConditionsMet();
	}
}
