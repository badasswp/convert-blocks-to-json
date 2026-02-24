<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Paragraph;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Paragraph::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Paragraph::export_block
 * @covers \ConvertBlocksToJSON\Abstracts\Block::get_clean_markup
 */
class ParagraphTest extends WPMockTestCase {
	public Paragraph $paragraph;

	public function setUp(): void {
		parent::setUp();

		$this->paragraph = new Paragraph();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->paragraph->import_block( $nameless_block );

		$this->assertsame( $response, $nameless_block );
	}

	public function test_import_block_returns_original_block_if_name_is_not_paragraph() {
		$non_paragraph_block = [
			'name'        => 'core/list',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->paragraph->import_block( $non_paragraph_block );

		$this->assertsame( $response, $non_paragraph_block );
	}

	public function test_import_block_returns_modified_block_with_cleaned_content_attribute() {
		$paragraph_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{"content":"<p>This content should be returned without the tags.</p>"}',
			'innerBlocks' => [],
		];

		$response = $this->paragraph->import_block( $paragraph_block );

		$this->assertsame(
			$response,
			[
				'name'        => 'core/paragraph',
				'attributes'  => '{"content":"This content should be returned without the tags."}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->paragraph->export_block( $nameless_block );

		$this->assertsame( $response, $nameless_block );
	}

	public function test_export_block_returns_original_block_if_name_is_not_paragraph() {
		$non_paragraph_block = [
			'name'        => 'core/list',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->paragraph->export_block( $non_paragraph_block );

		$this->assertsame( $response, $non_paragraph_block );
	}

	public function test_export_block_returns_same_block_if_it_is_a_paragraph() {
		$paragraph_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{"content":"<p>This content should be returned without the tags.</p>"}',
			'innerBlocks' => [],
		];

		$response = $this->paragraph->export_block( $paragraph_block );

		$this->assertsame(
			$response,
			[
				'name'        => 'core/paragraph',
				'attributes'  => '{"content":"<p>This content should be returned without the tags.</p>"}',
				'innerBlocks' => [],
			]
		);
	}
}
