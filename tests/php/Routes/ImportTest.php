<?php

namespace ConvertBlocksToJSON\Tests\Routes;

use WP_Mock;
use Mockery;
use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use ConvertBlocksToJSON\Routes\Import;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Routes\Import::rest_callback
 * @covers \ConvertBlocksToJSON\Routes\Import::get_blocks_import
 * @covers \ConvertBlocksToJSON\Routes\Import::get_import
 * @covers \ConvertBlocksToJSON\Abstracts\Route::get_400_response
 */
class ImportTest extends WPMockTestCase {
	public Import $import;

	public function setUp(): void {
		parent::setUp();

		$this->import = new Import();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function create_mock_file( $mock_file, $content = 'Hello World!' ) {
		file_put_contents( $mock_file, $content, FILE_APPEND );
	}

	public function destroy_mock_file( $mock_file ) {
		if ( file_exists( $mock_file ) ) {
			unlink( $mock_file );
		}
	}

	public function test_class_properties_are_defined_by_default() {
		$this->assertSame( 'POST', $this->import->method );
		$this->assertSame( '/import', $this->import->endpoint );
	}

	public function test_rest_callback_fails_when_no_post_ID_is_passed_and_returns_400_response() {
		$request = Mockery::mock( WP_REST_Request::class )
			->makePartial();

		$request->shouldReceive( 'get_json_params' )
			->andReturn( [] );

		WP_Mock::userFunction( 'get_attached_file' )
			->with( 0 )
			->andReturn( '' );

		$response = $this->import->rest_callback( $request );

		$this->assertInstanceOf( WP_Error::class, $response );
		$this->assertConditionsMet();
	}

	public function test_rest_callback_fails_if_imported_file_is_not_json_and_returns_400_response() {
		$mock_file = __DIR__ . '/sample.txt';

		$request = Mockery::mock( WP_REST_Request::class )
			->makePartial();

		$request->shouldReceive( 'get_json_params' )
			->andReturn( [ 'id' => '1' ] );

		WP_Mock::userFunction( 'get_attached_file' )
			->with( 1 )
			->andReturn( $mock_file );

		WP_Mock::userFunction( 'wp_check_filetype' )
			->with( $mock_file )
			->andReturn(
				[
					'ext'  => 'txt',
					'type' => 'text/plain',
				]
			);

		$this->create_mock_file( $mock_file );

		$response = $this->import->rest_callback( $request );

		$this->assertInstanceOf( WP_Error::class, $response );
		$this->assertConditionsMet();

		$this->destroy_mock_file( $mock_file );
	}

	public function test_rest_callback_passes_correctly() {
		$mock_file = __DIR__ . '/sample.txt';

		$content = json_encode(
			[
				'title'   => 'Hello World',
				'content' => [
					[],
					[
						'name'        => 'core/paragraph',
						'content'     => '<p>Block with name</p>',
						'filtered'    => 'Block with name',
						'attributes'  => [],
						'innerBlocks' => [],
					],
					[],
					[
						'content'     => '<p>Block with no name</p>',
						'filtered'    => 'Block with no name',
						'attributes'  => [],
						'innerBlocks' => [],
					],
				],
			]
		);

		$request = Mockery::mock( WP_REST_Request::class )
			->makePartial();

		$request->shouldReceive( 'get_json_params' )
			->andReturn( [ 'id' => '1' ] );

		WP_Mock::userFunction( 'get_attached_file' )
			->with( 1 )
			->andReturn( $mock_file );

		WP_Mock::userFunction( 'wp_check_filetype' )
			->with( $mock_file )
			->andReturn(
				[
					'ext'  => 'json',
					'type' => 'text/json',
				]
			);

		WP_Mock::expectFilter(
			'cbtj_import_block',
			[
				'name'            => 'core/paragraph',
				'originalContent' => '<p>Block with name</p>',
				'attributes'      => '{"content":"Block with name"}',
				'innerBlocks'     => [],
			]
		);

		WP_Mock::expectFilter(
			'cbtj_rest_import',
			[
				'title'   => 'Hello World',
				'content' => [
					'name'            => 'core/paragraph',
					'originalContent' => '<p>Block with name</p>',
					'attributes'      => '{"content":"Block with name"}',
					'innerBlocks'     => [],
				],
			],
			1
		);

		WP_Mock::userFunction( 'rest_ensure_response' )
			->andReturn( Mockery::mock( WP_REST_Response::class )->makePartial() );

		$this->create_mock_file( $mock_file, '{"title":"Hello World","content":[[],{"name":"core\/paragraph","content":"<p>Block with name<\/p>","filtered":"Block with name","attributes":[],"innerBlocks":[]},[],{"content":"<p>Block with no name<\/p>","filtered":"Block with no name","attributes":[],"innerBlocks":[]}]}' );

		$response = $this->import->rest_callback( $request );

		$this->assertInstanceOf( WP_REST_Response::class, $response );
		$this->assertConditionsMet();

		$this->destroy_mock_file( $mock_file );
	}

	public function test_get_blocks_import() {
		$blocks = [
			[],
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with name</p>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			],
			[],
			[
				'content'     => '<p>Block with no name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			],
		];

		WP_Mock::expectFilter(
			'cbtj_import_block',
			[
				'name'            => 'core/paragraph',
				'originalContent' => '<p>Block with name</p>',
				'attributes'      => '{"content":"Block with name"}',
				'innerBlocks'     => [],
			]
		);

		$response = $this->import->get_blocks_import( $blocks );

		$this->assertSame(
			$response,
			[
				[],
				[
					'name'            => 'core/paragraph',
					'originalContent' => '<p>Block with name</p>',
					'attributes'      => '{"content":"Block with name"}',
					'innerBlocks'     => [],
				],
				[],
				[],
			]
		);
		$this->assertConditionsMet();
	}

	public function test_get_import_returns_empty_block_if_no_name() {
		$block = [
			'content'     => '<p>Block with no name</p>',
			'filtered'    => 'Block with no name',
			'attributes'  => [],
			'innerBlocks' => [],
		];

		$response = $this->import->get_import( $block );

		$this->assertSame( $response, [] );
		$this->assertConditionsMet();
	}

	public function test_get_import_returns_filtered_block_correctly() {
		$block = [
			'name'        => 'core/paragraph',
			'content'     => '<p>Block with name</p>',
			'filtered'    => 'Block with name',
			'attributes'  => [],
			'innerBlocks' => [],
		];

		WP_Mock::expectFilter(
			'cbtj_import_block',
			[
				'name'            => 'core/paragraph',
				'originalContent' => '<p>Block with name</p>',
				'attributes'      => '{"content":"Block with name"}',
				'innerBlocks'     => [],
			]
		);

		$response = $this->import->get_import( $block );

		$this->assertSame(
			$response,
			[
				'name'            => 'core/paragraph',
				'originalContent' => '<p>Block with name</p>',
				'attributes'      => '{"content":"Block with name"}',
				'innerBlocks'     => [],
			]
		);
		$this->assertConditionsMet();
	}
}
