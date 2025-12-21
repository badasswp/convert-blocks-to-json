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
		parent::setUp();

		$this->image = new Image();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_default_block_if_name_is_undefined() {
		$block = $this->image->import_block(
			[
				'attributes'      => '{}',
				'originalContent' => '',
				'innerBlocks'     => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'attributes'      => '{}',
				'originalContent' => '',
				'innerBlocks'     => [],
			]
		);
	}

	public function test_import_block_returns_default_block_if_block_is_not_paragraph() {
		$block = $this->image->import_block(
			[
				'name'            => 'core/paragraph',
				'attributes'      => '{}',
				'originalContent' => '',
				'innerBlocks'     => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'            => 'core/paragraph',
				'attributes'      => '{}',
				'originalContent' => '',
				'innerBlocks'     => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_added_image_attribute() {
		$block = $this->image->import_block(
			[
				'name'            => 'core/image',
				'originalContent' => '<body><img src="https://www.example.com/wp-content/image.jpg"/></body>',
				'attributes'      => '{}',
				'innerBlocks'     => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'            => 'core/image',
				'originalContent' => '<body><img src="https://www.example.com/wp-content/image.jpg"/></body>',
				'attributes'      => '{"url":"https:\/\/www.example.com\/wp-content\/image.jpg"}',
				'innerBlocks'     => [],
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
