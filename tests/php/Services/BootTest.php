<?php

namespace ConvertBlocksToJSON\Tests\Services;

use Mockery;
use WP_Mock;
use Badasswp\WPMockTC\WPMockTestCase;
use ConvertBlocksToJSON\Services\Boot;

/**
 * @covers \ConvertBlocksToJSON\Services\Boot::__construct
 * @covers \ConvertBlocksToJSON\Services\Boot::register
 * @covers \ConvertBlocksToJSON\Services\Boot::get_blocks
 */
class BootTest extends WPMockTestCase {
	public Boot $boot;

	public function setUp(): void {
		parent::setUp();

		$this->boot = new Boot();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_register() {
		WP_Mock::expectActionAdded( 'admin_init', [ $this->boot, 'flush_permalinks' ] );
		WP_Mock::expectFilterAdded( 'upload_mimes', [ $this->boot, 'register_json_mime' ] );

		$this->boot->register();

		$this->assertConditionsMet();
	}

	public function test_flush_permalinks_fails_if_cbtj_flush_rewrite_rules_is_not_set() {
		WP_Mock::userFunction( 'get_option' )
			->with( 'cbtj_flush_rewrite_rules' )
			->andReturn( '' );

		$this->boot->flush_permalinks();

		$this->assertConditionsMet();
	}

	public function test_flush_permalinks_passes_correctly() {
		WP_Mock::userFunction( 'get_option' )
			->with( 'cbtj_flush_rewrite_rules' )
			->andReturn( true );

		WP_Mock::userFunction( 'flush_rewrite_rules' )
			->once();

		WP_Mock::userFunction( 'delete_option' )
			->once()
			->with( 'cbtj_flush_rewrite_rules' );

		$this->boot->flush_permalinks();

		$this->assertConditionsMet();
	}

	public function test_register_json_mime_returns_default_mime_if_json_key_is_set() {
		$mimes = $this->boot->register_json_mime(
			[
				'json' => 'application/json',
				'txt'  => 'text/plain',
			]
		);

		$this->assertSame(
			$mimes,
			[
				'json' => 'application/json',
				'txt'  => 'text/plain',
			]
		);
		$this->assertConditionsMet();
	}

	public function test_register_json_mime_correctly() {
		$mimes = $this->boot->register_json_mime(
			[
				'txt' => 'text/plain',
			]
		);

		$this->assertSame(
			$mimes,
			[
				'txt'  => 'text/plain',
				'json' => 'application/json',
			]
		);
		$this->assertConditionsMet();
	}
}
