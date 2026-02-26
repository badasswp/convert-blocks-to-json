<?php

namespace ConvertBlocksToJSON\Tests\Blocks;

use WP_Mock;
use Mockery;
use ConvertBlocksToJSON\Blocks\Audio;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Blocks\Audio::import_block
 * @covers \ConvertBlocksToJSON\Blocks\Audio::export_block
 */
class AudioTest extends WPMockTestCase {
	public Audio $audio;

	public function setUp(): void {
		parent::override( [ 'is_wp_error' ] );
		parent::setUp();

		$this->audio = new Audio();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_import_block_returns_original_block_if_name_is_undefined() {
		$nameless_block = [
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->audio->import_block( $nameless_block );

		$this->assertsame( $response, $nameless_block );
	}

	public function test_import_block_returns_original_block_if_name_is_not_audio() {
		$non_audio_block = [
			'name'        => 'core/paragraph',
			'attributes'  => '{}',
			'innerBlocks' => [],
		];

		$response = $this->audio->import_block( $non_audio_block );

		$this->assertsame( $response, $non_audio_block );
	}

	public function test_import_block_returns_modified_block_with_src_attribute() {
		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		$audio_block = [
			'name'        => 'core/audio',
			'attributes'  => '{"content":"<figure><audio controls src=\"https:\/\/www.example.com\/wp-content\/audio.mp3\"\/><\/figure>"}',
			'innerBlocks' => [],
		];

		$response = $this->audio->import_block( $audio_block );

		$this->assertsame(
			$response,
			[
				'name'        => 'core/audio',
				'attributes'  => '{"content":"<figure><audio controls src=\"https:\/\/www.example.com\/wp-content\/audio.mp3\"\/><\/figure>","src":"https:\/\/www.example.com\/wp-content\/audio.mp3"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_added_src_url_referencing_old_site_if_is_wp_error() {
		$audio = Mockery::mock( Audio::class )->makePartial();
		$audio->shouldAllowMockingProtectedMethods();

		$wp_error = Mockery::mock( WP_Error::class )->makePartial();
		$wp_error->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( true );

		$audio->shouldReceive( 'get_remote_file' )
			->with( 'https://www.johndoe.com/wp-content/audio.mp3' )
			->andReturn( $wp_error );

		$block = $audio->import_block(
			[
				'name'        => 'core/audio',
				'attributes'  => '{"content":"<body><audio controls src=\"https:\/\/www.johndoe.com\/wp-content\/audio.mp3\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/audio',
				'attributes'  => '{"content":"<body><audio controls src=\"https:\/\/www.johndoe.com\/wp-content\/audio.mp3\"\/><\/body>","src":"https:\/\/www.johndoe.com\/wp-content\/audio.mp3"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_import_block_returns_modified_block_with_newly_imported_audio_from_remote_site() {
		$audio = Mockery::mock( Audio::class )->makePartial();
		$audio->shouldAllowMockingProtectedMethods();

		WP_Mock::userFunction( 'home_url' )
			->andReturn( 'https://www.example.com' );

		WP_Mock::userFunction( 'is_wp_error' )
			->andReturn( false );

		$audio->shouldReceive( 'get_remote_file' )
			->with( 'https://www.johndoe.com/wp-content/audio.mp3' )
			->andReturn( 'https://www.example.com/wp-content/imported-audio.mp3' );

		$block = $audio->import_block(
			[
				'name'        => 'core/audio',
				'attributes'  => '{"content":"<body><audio controls src=\"https:\/\/www.johndoe.com\/wp-content\/audio.mp3\"\/><\/body>"}',
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/audio',
				'attributes'  => '{"content":"<body><audio controls src=\"https:\/\/www.johndoe.com\/wp-content\/audio.mp3\"\/><\/body>","src":"https:\/\/www.example.com\/wp-content\/imported-audio.mp3"}',
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_name_is_undefined() {
		$block = $this->audio->export_block(
			[
				'content'     => '<audio controls src="https://www.example.com/wp-content/audio.mp3"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'content'     => '<audio controls src="https://www.example.com/wp-content/audio.mp3"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_default_block_if_it_is_not_an_audio_block() {
		$block = $this->audio->export_block(
			[
				'name'        => 'core/paragraph',
				'content'     => '<audio controls src="https://www.example.com/wp-content/audio.mp3"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/paragraph',
				'content'     => '<audio controls src="https://www.example.com/wp-content/audio.mp3"/>',
				'filtered'    => 'Block with no name',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}

	public function test_export_block_returns_same_block_if_it_is_an_audio() {
		$block = $this->audio->export_block(
			[
				'name'        => 'core/audio',
				'content'     => '<audio controls src="https://example.com/wp-content/audio.mp3"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);

		$this->assertSame(
			$block,
			[
				'name'        => 'core/audio',
				'content'     => '<audio controls src="https://example.com/wp-content/audio.mp3"/>',
				'filtered'    => '',
				'attributes'  => [],
				'innerBlocks' => [],
			]
		);
	}
}
