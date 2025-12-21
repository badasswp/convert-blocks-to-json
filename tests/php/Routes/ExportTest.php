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
 * @covers \ConvertBlocksToJSON\Routes\Export::get_blocks_export
 * @covers \ConvertBlocksToJSON\Routes\Export::get_export
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
				'title'   => 'Hello World',
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

	public function test_get_blocks_export() {
		WP_Mock::userFunction( 'parse_blocks' )
			->with( 'Hello World' )
			->andReturn(
				[
					[
						'content'     => 'Block with no name',
						'innerHTML'   => '<p>Block with no name</p>',
						'attrs'       => [],
						'innerBlocks' => [],
					],
					[
						'blockName'   => 'core/paragraph',
						'innerHTML'   => '<p>Block with name</p>',
						'attrs'       => [],
						'innerBlocks' => [],
					],
				]
			);

		WP_Mock::expectFilter(
			'cbtj_export_block',
			[
				'name'        => '',
				'content'     => '<p>Block with no name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		WP_Mock::expectFilter(
			'cbtj_export_block',
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with name</p>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			[
				[
					'name'        => 'core/paragraph',
					'content'     => '<p>Block with name</p>',
					'filtered'    => 'Block with name',
					'attributes'  => [],
					'innerBlocks' => [],
				],
			],
			$this->export->get_blocks_export( 'Hello World' )
		);
	}

	public function test_get_export() {
		$expected = [
			'name'        => 'core/paragraph',
			'content'     => '<p>Block with name</p>',
			'filtered'    => 'Block with name',
			'attributes'  => [],
			'innerBlocks' => [],
		];

		WP_Mock::expectFilter( 'cbtj_export_block', $expected );

		$this->assertSame(
			$expected,
			$this->export->get_export(
				[
					'blockName'   => 'core/paragraph',
					'innerHTML'   => '<p>Block with name</p>',
					'attrs'       => [],
					'innerBlocks' => [],
				]
			)
		);
	}
}
