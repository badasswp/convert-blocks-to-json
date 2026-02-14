<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Details;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Details::get_summary
 */
class DetailsTest extends WPMockTestCase {
	public Details $details;

	public function setUp(): void {
		parent::setUp();

		$this->details = new Details();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_get_summary() {
		$summary = $this->details->get_summary( '<details class="wp-block-details"><summary>A wonderful set of details to add to this page...</summary></details>' );

		$this->assertSame( $summary, 'A wonderful set of details to add to this page...' );
	}
}
