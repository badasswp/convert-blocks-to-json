<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Image;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Image::modify_block
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

	public function test_modify_block_returns_default_block_if_name_is_undefined() {
		$block = $this->image->modify_block(
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

	public function test_modify_block_returns_default_block_if_block_is_not_paragraph() {
		$block = $this->image->modify_block(
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

	public function test_modify_block_returns_modified_block_with_added_image_attribute() {
		$block = $this->image->modify_block(
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
}
