<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\MediaText;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\MediaText::import_block
 * @covers \ConvertBlocksToJSON\Blocks\MediaText::export_block
 */
class MediaTextTest extends WPMockTestCase {
	public MediaText $mediatext;

	public function setUp(): void {
		parent::override( [ 'is_wp_error' ] );
		parent::setUp();

		$this->mediatext = new MediaText();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->mediatext->import_block( $nameless_block );

		$this->assertsame( $response, $nameless_block );
	}

	public function test_import_block_returns_original_block_if_name_is_not_media_text() {
		$non_mediatext_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->mediatext->import_block( $non_mediatext_block );

		$this->assertsame( $response, $non_mediatext_block );
	}

	public function test_import_block_returns_modified_block_with_mediaUrl_attribute() {
		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		$mediatext_block = [
			'name'        => 'core/media-text',
			'attributes'  => '{"content":"<figure><img src=\"https:\/\/www.example.com\/wp-content\/image.jpg\"\/><\/figure>"}',
			'innerBlocks' => [],
		];

		$response = $this->mediatext->import_block( $mediatext_block );

		$this->assertsame(
			$response,
			[
				'name'        => 'core/media-text',
				'attributes'  => '{"content":"<figure><img src=\"https:\/\/www.example.com\/wp-content\/image.jpg\"\/><\/figure>","mediaUrl":"https:\/\/www.example.com\/wp-content\/image.jpg"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_added_media_url_referencing_old_site_if_is_wp_error() {
		$MediaText = Mockery::mock( MediaText::class )->makePartial();
		$MediaText->shouldAllowMockingProtectedMethods();

		$wp_error = Mockery::mock( WP_Error::class )->makePartial();
		$wp_error->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( true );

		$MediaText->shouldReceive( 'get_remote_file' )
			->with( 'https://www.johndoe.com/wp-content/image.jpg' )
			->andReturn( $wp_error );

		$block = $MediaText->import_block(
			[
				'name'        => 'core/media-text',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/media-text',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>","mediaUrl":"https:\/\/www.johndoe.com\/wp-content\/image.jpg"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_newly_imported_media_from_remote_site() {
		$MediaText = Mockery::mock( MediaText::class )->makePartial();
		$MediaText->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( false );

		$MediaText->shouldReceive( 'get_remote_file' )
			->with( 'https://www.johndoe.com/wp-content/image.jpg' )
			->andReturn( 'https://www.example.com/wp-content/imported-image.jpg' );

		$block = $MediaText->import_block(
			[
				'name'        => 'core/media-text',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/media-text',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>","mediaUrl":"https:\/\/www.example.com\/wp-content\/imported-image.jpg"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->mediatext->export_block(
			[
				'content'     => '<img src="https://www.example.com/wp-content/image.jpg"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'content'     => '<img src="https://www.example.com/wp-content/image.jpg"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_it_is_not_a_media_text_block() {
		$block = $this->mediatext->export_block(
			[
				'name'        => 'core/paragraph',
				'content'     => '<img src="https://www.example.com/wp-content/image.jpg"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/paragraph',
				'content'     => '<img src="https://www.example.com/wp-content/image.jpg"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_same_block_if_it_is_media_text() {
		$block = $this->mediatext->export_block(
			[
				'name'        => 'core/media-text',
				'content'     => '<img src="https://example.com/wp-content/image.jpeg"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/media-text',
				'content'     => '<img src="https://example.com/wp-content/image.jpeg"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
