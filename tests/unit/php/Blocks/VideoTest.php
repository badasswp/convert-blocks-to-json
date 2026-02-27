<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Video;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Video::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Video::export_block
 */
class VideoTest extends WPMockTestCase {
	public Video $video;

	public function setUp(): void {
		parent::override( [ 'is_wp_error' ] );
		parent::setUp();

		$this->video = new video();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->video->import_block( $nameless_block );

		$this->assertsame( $response, $nameless_block );
	}

	public function test_import_block_returns_original_block_if_name_is_not_video() {
		$non_video_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->video->import_block( $non_video_block );

		$this->assertsame( $response, $non_video_block );
	}

	public function test_import_block_returns_modified_block_with_src_attribute() {
		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		$video_block = [
			'name'        => 'core/video',
			'attributes'  => '{"content":"<figure><video controls src=\"https:\/\/www.example.com\/wp-content\/video.mp4\"\/><\/figure>"}',
			'innerBlocks' => [],
		];

		$response = $this->video->import_block( $video_block );

		$this->assertsame(
			$response,
			[
				'name'        => 'core/video',
				'attributes'  => '{"content":"<figure><video controls src=\"https:\/\/www.example.com\/wp-content\/video.mp4\"\/><\/figure>","src":"https:\/\/www.example.com\/wp-content\/video.mp4"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_added_src_url_referencing_old_site_if_is_wp_error() {
		$video = Mockery::mock( Video::class )->makePartial();
		$video->shouldAllowMockingProtectedMethods();

		$wp_error = Mockery::mock( WP_Error::class )->makePartial();
		$wp_error->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( true );

		$video->shouldReceive( 'get_remote_file' )
			->with( 'https://www.johndoe.com/wp-content/video.mp4' )
			->andReturn( $wp_error );

		$block = $video->import_block(
			[
				'name'        => 'core/video',
				'attributes'  => '{"content":"<body><video controls src=\"https:\/\/www.johndoe.com\/wp-content\/video.mp4\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/video',
				'attributes'  => '{"content":"<body><video controls src=\"https:\/\/www.johndoe.com\/wp-content\/video.mp4\"\/><\/body>","src":"https:\/\/www.johndoe.com\/wp-content\/video.mp4"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_newly_imported_video_from_remote_site() {
		$video = Mockery::mock( Video::class )->makePartial();
		$video->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( false );

		$video->shouldReceive( 'get_remote_file' )
			->with( 'https://www.johndoe.com/wp-content/video.mp4' )
			->andReturn( 'https://www.example.com/wp-content/imported-video.mp4' );

		$block = $video->import_block(
			[
				'name'        => 'core/video',
				'attributes'  => '{"content":"<body><video controls src=\"https:\/\/www.johndoe.com\/wp-content\/video.mp4\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/video',
				'attributes'  => '{"content":"<body><video controls src=\"https:\/\/www.johndoe.com\/wp-content\/video.mp4\"\/><\/body>","src":"https:\/\/www.example.com\/wp-content\/imported-video.mp4"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->video->export_block(
			[
				'content'     => '<video controls src="https://www.example.com/wp-content/video.mp4"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'content'     => '<video controls src="https://www.example.com/wp-content/video.mp4"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_it_is_not_a_video_block() {
		$block = $this->video->export_block(
			[
				'name'        => 'core/paragraph',
				'content'     => '<video controls src="https://www.example.com/wp-content/video.mp4"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/paragraph',
				'content'     => '<video controls src="https://www.example.com/wp-content/video.mp4"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_same_block_if_it_is_a_video() {
		$block = $this->video->export_block(
			[
				'name'        => 'core/video',
				'content'     => '<video controls src="https://example.com/wp-content/video.mp4"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/video',
				'content'     => '<video controls src="https://example.com/wp-content/video.mp4"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
