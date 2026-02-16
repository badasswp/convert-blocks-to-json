<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Image;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Image::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Image::export_block
 */
class ImageTest extends WPMockTestCase {
	public Image $image;

	public function setUp(): void {
		parent::override( [ 'is_wp_error' ] );
		parent::setUp();

		$this->image = new Image();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_default_block_if_name_is_undefined() {
		$block = $this->image->import_block(
			[
				'attributes'  => '{"content":""}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'attributes'  => '{"content":""}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_default_block_if_block_is_not_paragraph() {
		$block = $this->image->import_block(
			[
				'name'        => 'core/paragraph',
				'attributes'  => '{"content":""}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/paragraph',
				'attributes'  => '{"content":""}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_added_image_attribute() {
		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		$block = $this->image->import_block(
			[
				'name'        => 'core/image',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.example.com\/wp-content\/image.jpg\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/image',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.example.com\/wp-content\/image.jpg\"\/><\/body>","url":"https:\/\/www.example.com\/wp-content\/image.jpg"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_added_image_url_referencing_old_site_if_is_wp_error() {
		$image = Mockery::mock( Image::class )->makePartial();
		$image->shouldAllowMockingProtectedMethods();

		$wp_error = Mockery::mock( WP_Error::class )->makePartial();
		$wp_error->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( true );

		$image->shouldReceive( 'get_remote_image' )
			->with( 'https://www.johndoe.com/wp-content/image.jpg' )
			->andReturn( $wp_error );

		$block = $image->import_block(
			[
				'name'        => 'core/image',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/image',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>","url":"https:\/\/www.johndoe.com\/wp-content\/image.jpg"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_newly_imported_image_from_remote_site() {
		$image = Mockery::mock( Image::class )->makePartial();
		$image->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( false );

		$image->shouldReceive( 'get_remote_image' )
			->with( 'https://www.johndoe.com/wp-content/image.jpg' )
			->andReturn( 'https://www.example.com/wp-content/imported-image.jpg' );

		$block = $image->import_block(
			[
				'name'        => 'core/image',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/image',
				'attributes'  => '{"content":"<body><img src=\"https:\/\/www.johndoe.com\/wp-content\/image.jpg\"\/><\/body>","url":"https:\/\/www.example.com\/wp-content\/imported-image.jpg"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->image->export_block(
			[
				'content'     => '<p>Block with no name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'content'     => '<p>Block with no name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_it_is_not_an_image_block() {
		$block = $this->image->export_block(
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_same_block_if_it_is_image() {
		$block = $this->image->export_block(
			[
				'name'        => 'core/image',
				'content'     => '<img src="https://example.com/wp-content/image.jpeg"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/image',
				'content'     => '<img src="https://example.com/wp-content/image.jpeg"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
