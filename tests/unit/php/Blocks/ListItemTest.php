<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\ListItem;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\ListItem::import_block
 * @covers \ConvertBlocksToJSON\Blocks\ListItem::export_block
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_clean_markup
 */
class ListItemTest extends WPMockTestCase {
	public ListItem $list_item;

	public function setUp(): void {
		parent::setUp();

		$this->list_item = new ListItem();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->list_item->import_block( $nameless_block );

		$this->assertSame( $response, $nameless_block );
	}

	public function test_import_block_returns_original_block_if_name_is_not_a_list_item() {
		$non_list_item_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->list_item->import_block( $non_list_item_block );

		$this->assertSame( $response, $non_list_item_block );
	}

	public function test_import_block_returns_modified_block_with_cleaned_content_attribute() {
		$list_block = [
			'name'        => 'core/list-item',
			'attributes'  => '{"content":"<li><li>Item 1<\/li><\/li>"}',
			'innerBlocks' => [],
		];

		$response = $this->list_item->import_block( $list_block );

		$this->assertSame(
			$response,
			[
				'name'        => 'core/list-item',
				'attributes'  => '{"content":"Item 1"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->list_item->export_block(
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

	public function test_export_block_returns_default_block_if_it_is_not_a_list_item_block() {
		$block = $this->list_item->export_block(
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with name</p>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with name</p>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_same_block_if_it_is_a_list_item() {
		$block = $this->list_item->export_block(
			[
				'name'        => 'core/list-item',
				'content'     => '<li>Block with name</li>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/list-item',
				'content'     => '<li>Block with name</li>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
