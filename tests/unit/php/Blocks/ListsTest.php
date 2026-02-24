<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Lists;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Lists::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Lists::export_block
 */
class ListsTest extends WPMockTestCase {
	public Lists $lists;

	public function setUp(): void {
		parent::setUp();

		$this->lists = new Lists();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->lists->import_block( $nameless_block );

		$this->assertSame( $response, $nameless_block );
	}

	public function test_import_block_returns_original_block_if_name_is_not_a_list() {
		$non_list_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->lists->import_block( $non_list_block );

		$this->assertSame( $response, $non_list_block );
	}

	public function test_import_block_removes_content_property_from_attribute() {
		$list_block = [
			'name'        => 'core/list',
			'attributes'  => '{"content":"The list content"}',
			'innerBlocks' => [],
		];

		$response = $this->lists->import_block( $list_block );

		$this->assertSame(
			$response,
			[
				'name'        => 'core/list',
				'attributes'  => '[]',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->lists->export_block(
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

	public function test_export_block_returns_default_block_if_it_is_not_a_list_block() {
		$block = $this->lists->export_block(
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

	public function test_export_block_returns_same_block_if_it_is_a_list() {
		$block = $this->lists->export_block(
			[
				'name'        => 'core/list',
				'content'     => '<ul><li>Block with name</li></ul>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/list',
				'content'     => '<ul><li>Block with name</li></ul>',
				'filtered'    => 'Block with name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
