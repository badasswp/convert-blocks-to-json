<?php

namespace ConvertBlocksToJSON\Tests\Abstracts;

use WP_Mock;
use Mockery;
use WP_Mock\Tools\TestCase;
use ConvertBlocksToJSON\Abstracts\Block;

/**
 * @covers \ConvertBlocksToJSON\Abstracts\Block::init
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_clean_markup
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_tag_content
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

	public function test_get_clean_markup_removes_dirty_markup_with_no_attributes() {
		$block      = new ConcreteBlock();
		$block->tag = 'p';

		$expected = $block->get_clean_markup( '<p>The <mark style="background-color:rgba(0, 0, 0, 0);color:#ff6900" class="has-inline-color">rough</mark> driver...</p>' );

		$this->assertSame(
			$expected,
			'The <mark style="background-color:rgba(0, 0, 0, 0);color:#ff6900" class="has-inline-color">rough</mark> driver...'
		);
	}

	public function test_get_clean_markup_removes_dirty_markup_with_attributes() {
		$block      = new ConcreteBlock();
		$block->tag = 'h2';

		$expected = $block->get_clean_markup( '<h2 class="wp-block-heading"><h2 class="wp-block-heading">The <mark style="background-color:rgba(0, 0, 0, 0);color:#ff6900" class="has-inline-color">rough</mark> driver...</h2></h2>' );

		$this->assertSame(
			$expected,
			'The <mark style="background-color:rgba(0, 0, 0, 0);color:#ff6900" class="has-inline-color">rough</mark> driver...'
		);
	}

	public function test_get_clean_markup_removes_dirty_markup_with_attributes_using_regexp_as_tag() {
		$block      = new ConcreteBlock();
		$block->tag = '(h1|h2|h3|h4|h5|h6)';

		$expected = $block->get_clean_markup( '<h2 class="wp-block-heading"><h2 class="wp-block-heading">The <mark style="background-color:rgba(0, 0, 0, 0);color:#ff6900" class="has-inline-color">rough</mark> driver...</h2></h2>' );

		$this->assertSame(
			$expected,
			'The <mark style="background-color:rgba(0, 0, 0, 0);color:#ff6900" class="has-inline-color">rough</mark> driver...'
		);
	}

	public function test_tag_content_returns_content_nested_within_tag() {
		$block = new ConcreteBlock();

		$summary = $block->get_tag_content( '<details class="wp-block-details"><summary>A wonderful set of details to add to this page...</summary></details>', 'summary' );

		$this->assertSame( $summary, 'A wonderful set of details to add to this page...' );
	}
}

class ConcreteBlock extends Block {
	public string $tag;

	public function import_block( $block ): array {
		return $block;
	}

	public function export_block( $block ): array {
		return $block;
	}
}
