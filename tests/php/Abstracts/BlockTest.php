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

	public function test_init_subscribes_to_cbtj_import_block_hook() {
		$block = new ConcreteBlock();

		WP_Mock::expectFilterAdded(
			'cbtj_import_block',
			[ $block, 'modify_block' ]
		);

		$block->init();

		$this->assertConditionsMet();
	}
}

class ConcreteBlock extends Block {
	public function modify_block( $block ): array {
		return $block;
	}
}
