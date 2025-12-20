<?php

namespace ConvertBlocksToJSON\Tests\Routes;

use WP_Mock;
use Mockery;
use WP_REST_Server;
use WP_REST_Request;
use ConvertBlocksToJSON\Routes\Export;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Routes\Export::rest_callback
 */
class ExportTest extends WPMockTestCase {
	public Export $export;

	public function setUp(): void {
		parent::setUp();

		Mockery::mock( WP_REST_Server::class )
			->makePartial();

		$this->export = new Export();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_rest_callback() {
		$export = Mockery::mock( Export::class )
			->makePartial();

		$request = Mockery::mock( WP_REST_Request::class )
			->makePartial();

		$export->shouldReceive( 'get_blocks_export' )
			->with( 'What a Wonderful World!' )
			->andReturn( [] );

		$request->shouldReceive( 'get_param' )
			->andReturn( '1' );

		WP_Mock::userFunction( 'get_the_title' )
			->with( 1 )
			->andReturn( 'Hello World' );

		WP_Mock::userFunction( 'get_post_field' )
			->with( 'post_content', 1 )
			->andReturn( 'What a Wonderful World!' );

		WP_Mock::expectFilter(
			'cbtj_rest_export',
			[
				'title' => 'Hello World',
				'content' => [],
			],
			1
		);

		WP_Mock::userFunction( 'rest_ensure_response' )
			->andReturn( Mockery::mock( WP_REST_Response::class )->makePartial() );

		$response = $export->rest_callback( $request );

		$this->assertInstanceOf( WP_REST_Response::class, $response );
		$this->assertConditionsMet();
	}
}
