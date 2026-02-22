<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Heading;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Heading::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Heading::export_block
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_clean_markup
 */
class HeadingTest extends WPMockTestCase {
	public Heading $heading;

	public function setUp(): void {
		parent::setUp();

		$this->heading = new Heading();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_default_block_if_name_is_undefined() {
		$block = $this->heading->import_block(
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

	public function test_import_block_returns_default_block_if_block_is_not_heading() {
		$block = $this->heading->import_block(
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

	public function test_import_block_returns_modified_block_with_added_content_attribute() {
		$block = $this->heading->import_block(
			[
				'name'        => 'core/heading',
				'attributes'  => '{"content":"<h1 class=\"wp-block\"><h1 class=\"wp-block\">We have a heading...<\/h1><\/h1>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/heading',
				'attributes'  => '{"content":"We have a heading..."}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->heading->export_block(
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

	public function test_export_block_returns_default_block_if_it_is_not_a_heading_block() {
		$block = $this->heading->export_block(
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

	public function test_export_block_returns_same_block_if_it_is_a_heading() {
		$block = $this->heading->export_block(
			[
				'name'        => 'core/heading',
				'content'     => '<h1>Block with name</h1>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/heading',
				'content'     => '<h1>Block with name</h1>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
