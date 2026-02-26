<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Footnotes;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Footnotes::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Footnotes::export_block
 */
class FootnotesTest extends WPMockTestCase {
	public Footnotes $footnotes;

	public function setUp(): void {
		parent::setUp();

		$this->footnotes = new Footnotes();
	}

	public function tearDown(): void {
		unset( $_SERVER['REQUEST_URI'] );
		parent::tearDown();
	}

	public function test_import_block_returns_default_block_if_name_is_undefined() {
		$block = $this->footnotes->import_block(
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

	public function test_import_block_returns_default_block_if_block_is_not_footnotes() {
		$block = $this->footnotes->import_block(
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

	public function test_import_block_returns_same_block_if_block_is_footnotes() {
		$block = $this->footnotes->import_block(
			[
				'name'        => 'core/footnotes',
				'attributes'  => '{"content":"Mr Zeks","id":"466e6cde-714f-412f-b95b-2a1852ff3471"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/footnotes',
				'attributes'  => '{"content":"Mr Zeks","id":"466e6cde-714f-412f-b95b-2a1852ff3471"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->footnotes->export_block(
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

	public function test_export_block_returns_default_block_if_block_is_not_footnotes() {
		$block = $this->footnotes->export_block(
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

	public function test_export_block_returns_same_block_if_block_is_footnotes() {
		$_SERVER['REQUEST_URI'] = '/wp-json/wp/v2/posts/42';

		WP_Mock::userFunction( 'wp_parse_url' )
			->with( '/wp-json/wp/v2/posts/42', PHP_URL_PATH )
			->andReturn( '/wp-json/wp/v2/posts/42' );

		WP_Mock::userFunction( 'absint' )
			->with( '42' )
			->andReturn( 42 );

		WP_Mock::userFunction( 'get_post_meta' )
			->with( 42, 'footnotes', true )
			->andReturn( '[{"content":"Mr Zeks","id":"466e6cde-714f-412f-b95b-2a1852ff3471"}]' );

		$block = $this->footnotes->export_block(
			[
				'name'        => 'core/footnotes',
				'content'     => '',
				'filtered'    => [],
				'attributes'  => [
					'content' => 'Mr Zeks',
					'id'      => '466e6cde-714f-412f-b95b-2a1852ff3471',
				],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/footnotes',
				'content'     => '',
				'filtered'    => [],
				'attributes'  => [
					'content'   => 'Mr Zeks',
					'id'        => '466e6cde-714f-412f-b95b-2a1852ff3471',
					'footnotes' => '[{"content":"Mr Zeks","id":"466e6cde-714f-412f-b95b-2a1852ff3471"}]',
				],
				'innerBlocks' => [],
			]
		);
	}
}
