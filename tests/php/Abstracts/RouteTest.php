<?php

namespace ConvertBlocksToJSON\Tests\Abstracts;

use WP_Mock;
use Mockery;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_Mock\Tools\TestCase;
use ConvertBlocksToJSON\Abstracts\Route;

/**
 * @covers \ConvertBlocksToJSON\Abstracts\Route::rest_callback
 * @covers \ConvertBlocksToJSON\Abstracts\Route::get_permission_callback
 * @covers \ConvertBlocksToJSON\Abstracts\Route::get_rest_namespace
 */
class RouteTest extends TestCase {
	public Route $route;

	public function setUp(): void {
		WP_Mock::setUp();

		$this->route = new ConcreteRoute();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_rest_callback() {
		$request = Mockery::mock( WP_REST_Request::class )
			->makePartial();

		$response = Mockery::mock( WP_REST_Response::class )
			->makePartial();

		WP_Mock::userFunction( 'rest_ensure_response' )
			->with( [] )
			->andReturn( $response );

		$this->assertInstanceOf( WP_REST_Response::class, $this->route->rest_callback( $request ) );
		$this->assertConditionsMet();
	}

	public function test_get_permission_callback_returns_string_with_return_true_value() {
		$private_route = Mockery::mock( ConcreteRoute::class )->makePartial()
			->shouldAllowMockingProtectedMethods();

		$private_route->shouldReceive( 'get_http_verb' )
			->andReturn( 'GET' );

		$this->assertSame(
			'__return_true',
			$private_route->get_permission_callback()
		);
	}

	public function test_get_permission_callback_returns_array() {
		$private_route = Mockery::mock( ConcreteRoute::class )->makePartial()
			->shouldAllowMockingProtectedMethods();

		$private_route->shouldReceive( 'get_http_verb' )
			->andReturn( 'POST' );

		$this->assertSame(
			[
				$private_route,
				'is_user_permissible'
			],
			$private_route->get_permission_callback()
		);
	}

	public function test_get_rest_namespace() {
		WP_Mock::expectFilter( 'cbtj_rest_namespace', 'cbtj/v1' );

		$namespace = ConcreteRoute::get_rest_namespace();

		$this->assertConditionsMet();
	}

	public function test_get_rest_namespace_filter_returns_new_namespace() {
		WP_Mock::onFilter( 'cbtj_rest_namespace' )
			->with( 'cbtj/v1' )
			->reply( 'your-namespace/v2' );

		$namespace = ConcreteRoute::get_rest_namespace();

		$this->assertSame( 'your-namespace/v2', $namespace );
		$this->assertConditionsMet();
	}

	public function test_register_route() {
		$route = new RegisterRoute();

		WP_Mock::userFunction( 'register_rest_route' )
			->with(
				'register-route/v1',
				'/register',
				[
					'methods'             => 'GET',
					'callback'            => [ $route, 'rest_callback' ],
					'permission_callback' => '__return_true',
				]
			)
			->andReturn( null );

		$route->register_route();

		$this->assertConditionsMet();
	}

	public function test_get_400_response() {
		$request = Mockery::mock( WP_REST_Request::class )
			->makePartial();

		$request->shouldReceive( 'get_json_params' )
			->andReturn(
				[
					'ID' => null,
				]
			);

		// Just mock this, so that WP_Error exists.
		$wp_error = Mockery::mock( WP_Error::class )->makePartial();

		$this->route->request = $request;

		$error_response = $this->route->get_400_response( 'Post ID not found.' );

		$this->assertInstanceOf( WP_Error::class, $error_response );
		$this->assertConditionsMet();
	}

	public function test_is_user_permissible_returns_error_if_not_administrator() {
		WP_Mock::userFunction( 'rest_authorization_required_code' )
			->andReturn( 403 );

		WP_Mock::userFunction( 'current_user_can' )
			->with( 'administrator' )
			->andReturn( false );

		$request = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		Mockery::mock( WP_Error::class )->makePartial();

		$this->assertInstanceOf(
			WP_Error::class,
			$this->route->is_user_permissible( $request )
		);
		$this->assertConditionsMet();
	}

	public function test_is_user_permissible_returns_error_if_nonce_fails() {
		WP_Mock::userFunction( 'rest_authorization_required_code' )
			->andReturn( 403 );

		WP_Mock::userFunction( 'current_user_can' )
			->with( 'administrator' )
			->andReturn( true );

		WP_Mock::userFunction( 'wp_verify_nonce' )
			->with( 'a8ceg59jeqwvk', 'wp_rest' )
			->andReturn( false );

		$request = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		$request->shouldReceive( 'get_header' )
			->with( 'X-WP-Nonce' )
			->andReturn( 'a8ceg59jeqwvk' );

		Mockery::mock( WP_Error::class )->makePartial();

		$this->assertInstanceOf(
			WP_Error::class,
			$this->route->is_user_permissible( $request )
		);
		$this->assertConditionsMet();
	}

	public function test_is_user_permissible_passes_correctly() {
		WP_Mock::userFunction( 'rest_authorization_required_code' )
			->andReturn( 403 );

		WP_Mock::userFunction( 'current_user_can' )
			->with( 'administrator' )
			->andReturn( true );

		$request = Mockery::mock( WP_REST_Request::class )->makePartial();
		$request->shouldAllowMockingProtectedMethods();

		$request->shouldReceive( 'get_header' )
			->with( 'X-WP-Nonce' )
			->andReturn( 'a8ceg59jeqwvk' );

		WP_Mock::userFunction( 'wp_verify_nonce' )
			->with( 'a8ceg59jeqwvk', 'wp_rest' )
			->andReturn( true );

		$this->assertTrue( $this->route->is_user_permissible( $request ) );
		$this->assertConditionsMet();
	}
}

class ConcreteRoute extends Route {
	public string $method = 'GET';
	public string $endpoint = '/concrete';
	public WP_REST_Request $request;

	public function rest_callback( $request ) {
		return rest_ensure_response( [] );
	}
}

class RegisterRoute extends Route {
	public string $method = 'GET';
	public string $endpoint = '/register';
	public WP_REST_Request $request;

	public static function get_rest_namespace(): string {
		return 'register-route/v1';
	}

	public function get_permission_callback() {
		return '__return_true';
	}

	public function rest_callback( $request ) {
		return rest_ensure_response( [] );
	}
}
