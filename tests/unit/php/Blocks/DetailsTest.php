<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Details;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Details::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Details::export_block
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_tag_content
 */
class DetailsTest extends WPMockTestCase {
	public Details $details;

	public function setUp(): void {
		parent::setUp();

		$this->details = new Details();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_default_block_if_name_is_undefined() {
		$block = $this->details->import_block(
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

	public function test_import_block_returns_default_block_if_block_is_not_details() {
		$block = $this->details->import_block(
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

	public function test_import_block_returns_modified_block_with_added_summary_attribute() {
		$block = $this->details->import_block(
			[
				'name'        => 'core/details',
				'attributes'  => '{"content":"<details class=\"wp-block\"><span>1<\/span><summary>We have a summary...<\/summary><\/details>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/details',
				'attributes'  => '{"content":"<details class=\"wp-block\"><span>1<\/span><summary>We have a summary...<\/summary><\/details>","summary":"We have a summary..."}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->details->export_block(
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

	public function test_export_block_returns_default_block_if_it_is_not_a_details_block() {
		$block = $this->details->export_block(
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

	public function test_export_block_returns_same_block_if_it_is_a_details() {
		$block = $this->details->export_block(
			[
				'name'        => 'core/details',
				'content'     => '<details>Block with name</details>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/details',
				'content'     => '<details>Block with name</details>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
