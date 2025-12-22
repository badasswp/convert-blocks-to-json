<?php

namespace ConvertBlocksToJSON\Tests\Services;

use Mockery;
use WP_Mock;
use Badasswp\WPMockTC\WPMockTestCase;
use ConvertBlocksToJSON\Blocks\Image;
use ConvertBlocksToJSON\Abstracts\Block;
use ConvertBlocksToJSON\Services\Blocks;

/**
 * @covers \ConvertBlocksToJSON\Services\Blocks::__construct
 * @covers \ConvertBlocksToJSON\Services\Blocks::register
 * @covers \ConvertBlocksToJSON\Services\Blocks::get_blocks
 * @covers \ConvertBlocksToJSON\Abstracts\Block::init
 */
class BlocksTest extends WPMockTestCase {
	public Blocks $blocks;

	public function setUp(): void {
		parent::setUp();

		$this->blocks = new Blocks();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_class_properties_are_defined_by_default() {
		$this->assertSame(
			[
				Image::class,
			],
			$this->blocks->blocks
		);
	}

	public function test_register() {
		$block_classes = [
			new Image(),
		];

		$blocks = Mockery::mock( Blocks::class )->makePartial()
			->shouldAllowMockingProtectedMethods();

		$blocks->shouldReceive( 'get_blocks' )
			->andReturn( $block_classes );

		foreach ( $block_classes as $block_class ) {
			WP_Mock::expectFilterAdded(
				'cbtj_import_block',
				[ $block_class, 'import_block' ]
			);

			WP_Mock::expectFilterAdded(
				'cbtj_export_block',
				[ $block_class, 'export_block' ]
			);
		}

		$blocks->register();

		$this->assertConditionsMet();
	}

	public function test_get_blocks() {
		WP_Mock::expectFilter( 'cbtj_blocks', [ Image::class ] );

		$blocks = $this->blocks->get_blocks();

		foreach ( $blocks as $block ) {
			$this->assertInstanceOf( Block::class, $block );
		}
		$this->assertConditionsMet();
	}
}
