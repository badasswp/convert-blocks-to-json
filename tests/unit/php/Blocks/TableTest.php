<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Table;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Table::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Table::export_block
 * @covers \ConvertBlocksToJSON\Blocks\Table::get_table_part
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_tag_content
 */
class TableTest extends WPMockTestCase {
	public Table $table;

	public function setUp(): void {
		parent::setUp();

		$this->table = new Table();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_default_block_if_name_is_undefined() {
		$block = $this->table->import_block(
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

	public function test_import_block_returns_default_block_if_block_is_not_a_table() {
		$block = $this->table->import_block(
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

	public function test_import_block_returns_modified_block_with_added_body_attribute() {
		$block = $this->table->import_block(
			[
				'name'        => 'core/table',
				'attributes'  => '{"content":"<table><tbody><tr><td>Cell 1a<\/td><td>Cell 1b<\/td><\/tr><tr><td>Cell 2a<\/td><td>Cell 2b<\/td><\/tr><\/tbody><\/table>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/table',
				'attributes'  => '{"content":"<table><tbody><tr><td>Cell 1a<\/td><td>Cell 1b<\/td><\/tr><tr><td>Cell 2a<\/td><td>Cell 2b<\/td><\/tr><\/tbody><\/table>","head":[],"body":[{"cells":[{"content":"Cell 1a","tag":"td"},{"content":"Cell 1b","tag":"td"}]},{"cells":[{"content":"Cell 2a","tag":"td"},{"content":"Cell 2b","tag":"td"}]}],"foot":[],"caption":""}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->table->export_block(
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

	public function test_export_block_returns_default_block_if_it_is_not_a_table_block() {
		$block = $this->table->export_block(
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

	public function test_export_block_returns_same_block_if_it_is_a_table() {
		$block = $this->table->export_block(
			[
				'name'        => 'core/table',
				'content'     => '<table><tbody><tr><td>Cell 1a</td><td>Cell 1b</td></tr><tr><td>Cell 2a</td><td>Cell 2b</td></tr></tbody></table>',
				'filtered'    => 'Cell1aCell1bCell2aCell2b',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/table',
				'content'     => '<table><tbody><tr><td>Cell 1a</td><td>Cell 1b</td></tr><tr><td>Cell 2a</td><td>Cell 2b</td></tr></tbody></table>',
				'filtered'    => 'Cell1aCell1bCell2aCell2b',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
