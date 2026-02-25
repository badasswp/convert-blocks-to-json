<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Pullquote;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Pullquote::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Pullquote::export_block
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_tag_content
 */
class PullquoteTest extends WPMockTestCase {
	public Pullquote $pullquote;

	public function setUp(): void {
		parent::setUp();

		$this->pullquote = new Pullquote();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->pullquote->import_block( $nameless_block );

		$this->assertsame( $response, $nameless_block );
	}

	public function test_import_block_returns_original_block_if_name_is_not_pullquote() {
		$non_pullquote_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->pullquote->import_block( $non_pullquote_block );

		$this->assertsame( $response, $non_pullquote_block );
	}

	public function test_import_block_returns_modified_block_with_value_and_citation_attribute() {
		$pullquote_block = [
			'name'        => 'core/pullquote',
			'attributes'  => '{"content":"<p>This content should be returned<\/p><cite>John Doe<\/cite>"}',
			'innerBlocks' => [],
		];

		$response = $this->pullquote->import_block( $pullquote_block );

		$this->assertsame(
			$response,
			[
				'name'        => 'core/pullquote',
				'attributes'  => '{"content":"<p>This content should be returned<\/p><cite>John Doe<\/cite>","value":"This content should be returned","citation":"John Doe"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'content'     => '',
			'attributes'  => '{}',
			'filtered'    => '',
			'innerBlocks' => [],
		];

		$response = $this->pullquote->export_block( $nameless_block );

		$this->assertsame( $response, $nameless_block );
	}

	public function test_export_block_returns_original_block_if_name_is_not_pullquote() {
		$non_pullquote_block = [
			'name'        => 'core/paragraph',
			'content'     => '',
			'attributes'  => '{}',
			'filtered'    => '',
			'innerBlocks' => [],
		];

		$response = $this->pullquote->export_block( $non_pullquote_block );

		$this->assertsame( $response, $non_pullquote_block );
	}

	public function test_export_block_returns_same_block_if_it_is_a_pullquote() {
		$pullquote_block = [
			'name'        => 'core/pullquote',
			'content'     => '<p>This content should be returned<\/p><cite>John Doe<\/cite>',
			'filtered'    => 'This content should be returnedJohn Doe',
			'attributes'  => [],
			'innerBlocks' => [],
		];

		$response = $this->pullquote->export_block( $pullquote_block );

		$this->assertsame( $response, $pullquote_block );
	}
}
