<?php

namespace ConvertBlocksToJSON\Tests\Services;

use WP_Mock;
use Mockery;
use WP_Mock\Tools\TestCase;
use ConvertBlocksToJSON\Routes\Import;
use ConvertBlocksToJSON\Routes\Export;
use ConvertBlocksToJSON\Services\Routes;

/**
 * @covers \ConvertBlocksToJSON\Services\Routes::__construct
 * @covers \ConvertBlocksToJSON\Services\Routes::register
 * @covers \ConvertBlocksToJSON\Services\Routes::register_rest_routes
 * @covers \ConvertBlocksToJSON\Abstracts\Route::register_route
 */
class RoutesTest extends TestCase {
	public Routes $routes;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->routes = new Routes();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_class_properties_are_defined_by_default() {
		$this->assertSame(
			[
				Import::class,
				Export::class,
			],
			$this->routes->routes
		);
	}

	public function test_register() {
		WP_Mock::expectActionAdded( 'rest_api_init', [ $this->routes, 'register_rest_routes' ] );

		$this->routes->register();

		$this->assertConditionsMet();
	}

	public function test_register_rest_routes() {
		WP_Mock::expectFilter( 'cbtj_rest_namespace', 'cbtj/v1' );

		WP_Mock::onFilter( 'cbtj_rest_routes' )
			->with(
				[
					Import::class,
					Export::class,
				]
			)
			->reply(
				[
					Import::class,
				]
			);

		$import = new Import();

		WP_Mock::userFunction( 'register_rest_route' )
			->with(
				'cbtj/v1',
				'/import',
				[
					'methods'             => 'POST',
					'callback'            => [ $import, 'rest_callback' ],
					'permission_callback' => [ $import, 'is_user_permissible' ],
				]
			)
			->andReturn( null );

		$this->routes->register_rest_routes();

		$this->assertConditionsMet();
	}
}
