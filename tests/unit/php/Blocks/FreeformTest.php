<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Freeform;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Freeform::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Freeform::export_block
 */
class FreeformTest extends WPMockTestCase {
	public Freeform $freeform;

	public function setUp(): void {
		parent::setUp();

		$this->freeform = new Freeform();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_default_block_if_name_is_undefined() {
		$block = $this->freeform->import_block(
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

	public function test_import_block_returns_default_block_if_block_is_not_freeform() {
		$block = $this->freeform->import_block(
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

	public function test_import_block_returns_same_block_if_it_is_a_freeform() {
		$block = $this->freeform->import_block(
			[
				'name'        => 'core/freeform',
				'attributes'  => '{"content":"<h1>This is heading<\/h1><p>This is a paragraph...<\/p>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/freeform',
				'attributes'  => '{"content":"<h1>This is heading<\/h1><p>This is a paragraph...<\/p>"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined_and_content_is_empty() {
		$block = $this->freeform->export_block(
			[
				'content'     => '',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'content'     => '',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_defined_and_content_is_not_empty() {
		$block = $this->freeform->export_block(
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with no name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/paragraph',
				'content'     => '<p>Block with no name</p>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_modified_block_if_name_is_undefined_and_content_is_not_empty() {
		$block = $this->freeform->export_block(
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
				'name'        => 'core/freeform',
			]
		);
	}
}
