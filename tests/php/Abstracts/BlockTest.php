<?php

namespace ConvertBlocksToJSON\Tests\Abstracts;

use WP_Mock;
use Mockery;
use WP_Mock\Tools\TestCase;
use ConvertBlocksToJSON\Abstracts\Block;

/**
 * @covers \ConvertBlocksToJSON\Abstracts\Block::init
 */
class BlockTest extends TestCase {
	public function setUp(): void {
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function test_init_subscribes_to_cbtj_import_and_export_block_hooks() {
		$block = new ConcreteBlock();

		WP_Mock::expectFilterAdded(
			'cbtj_import_block',
			[ $block, 'import_block' ]
		);

		WP_Mock::expectFilterAdded(
			'cbtj_export_block',
			[ $block, 'export_block' ]
		);

		$block->init();

		$this->assertConditionsMet();
	}
}

class ConcreteBlock extends Block {
	public function import_block( $block ): array {
		return $block;
	}

	public function export_block( $block ): array {
		return $block;
	}
}
