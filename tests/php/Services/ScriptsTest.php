<?php

namespace ConvertBlocksToJSON\Tests\Services;

use WP_Mock;
use Mockery;
use ReflectionClass;
use ConvertBlocksToJSON\Services\Scripts;
use ConvertBlocksToJSON\Abstracts\Service;
use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \ConvertBlocksToJSON\Services\Scripts::__construct
 * @covers \ConvertBlocksToJSON\Services\Scripts::register
 * @covers \ConvertBlocksToJSON\Services\Scripts::register_scripts
 * @covers \ConvertBlocksToJSON\Services\Scripts::register_translation
 * @covers \ConvertBlocksToJSON\Services\Scripts::get_assets
 */
class ScriptsTest extends WPMockTestCase {
	public Scripts $scripts;

	public function setUp(): void {
		parent::setUp();

		$this->scripts = new Scripts();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function create_mock_file( $mock_file, $content = 'Hello World!' ) {
		file_put_contents( $mock_file, $content, FILE_APPEND );
	}

	public function destroy_mock_file( $mock_file ) {
		if ( file_exists( $mock_file ) ) {
			unlink( $mock_file );
		}
	}

	public function test_class_properties_are_defined_by_default() {
		$this->assertSame( 'convert-blocks-to-json', Scripts::$slug );
	}

	public function test_register() {
		WP_Mock::expectActionAdded(
			'init',
			[ $this->scripts, 'register_translation' ]
		);

		WP_Mock::expectActionAdded(
			'enqueue_block_editor_assets',
			[ $this->scripts, 'register_scripts' ]
		);

		$this->scripts->register();

		$this->assertConditionsMet();
	}

	public function test_register_scripts() {
		$scripts = new ReflectionClass( Scripts::class );

		$mock_scripts = Mockery::mock( Scripts::class )->makePartial();
		$mock_scripts->shouldAllowMockingProtectedMethods();

		$mock_scripts->shouldReceive( 'get_assets' )
			->andReturn(
				[
					'dependencies' => [
						'wp-i18n',
						'wp-element',
						'wp-blocks',
						'wp-components',
						'wp-editor',
						'wp-hooks',
						'wp-compose',
						'wp-plugins',
						'wp-edit-post',
					],
					'version'      => '1750321560',
				]
			);

		WP_Mock::userFunction( 'plugins_url' )
			->andReturnUsing(
				function ( $arg ) {
					return sprintf( 'https://example.com/wp-content/plugins/%s', $arg );
				}
			);

		WP_Mock::userFunction( 'plugin_dir_path' )
			->with( $scripts->getFileName() )
			->andReturn( '/var/www/wp-content/plugins/convert-blocks-to-json/inc/Services/' );

		WP_Mock::userFunction( 'wp_enqueue_script' )
			->with(
				'convert-blocks-to-json',
				'https://example.com/wp-content/plugins/convert-blocks-to-json/dist/app.js',
				[
					'wp-i18n',
					'wp-element',
					'wp-blocks',
					'wp-components',
					'wp-editor',
					'wp-hooks',
					'wp-compose',
					'wp-plugins',
					'wp-edit-post',
				],
				'1750321560',
				false,
			);

		WP_Mock::userFunction( 'wp_enqueue_media' )
			->andReturn( null );

		WP_Mock::userFunction( 'wp_set_script_translations' )
			->with(
				'convert-blocks-to-json',
				'convert-blocks-to-json',
				'/var/www/wp-content/plugins/convert-blocks-to-json/inc/Services/../../languages',
			);

		WP_Mock::userFunction( 'get_home_url' )
			->andReturn( 'https://example.com' );

		WP_Mock::expectFilter( 'cbtj_rest_namespace', 'cbtj/v1' );

		WP_Mock::userFunction( 'wp_localize_script' )
			->with(
				'convert-blocks-to-json',
				'cbtj',
				[
					'baseUrl'   => 'https://example.com',
					'namespace' => 'cbtj/v1',
				]
			)
			->andReturn( null );

		$mock_scripts->register_scripts();

		$this->assertConditionsMet();
	}

	public function test_register_translation() {
		$scripts = new ReflectionClass( Scripts::class );

		WP_Mock::userFunction( 'plugin_basename' )
			->once()
			->with( $scripts->getFileName() )
			->andReturn( '/inc/Services/Scripts.php' );

		WP_Mock::userFunction( 'load_plugin_textdomain' )
			->once()
			->with(
				'convert-blocks-to-json',
				false,
				'/inc/Services/../../languages'
			);

		$this->scripts->register_translation();

		$this->assertConditionsMet();
	}

	public function test_get_assets_returns_empty_dependencies() {
		$assets = $this->scripts->get_assets( '' );
		$time   = time();

		$this->assertSame(
			$assets,
			[
				'version'      => (string) $time,
				'dependencies' => [],
			]
		);
	}

	public function test_get_assets_returns_webpack_generated_dependencies() {
		$assets_file = __DIR__ . '/assets_mock.php';

		$this->create_mock_file( $assets_file, "<?php return array('dependencies' => array('react', 'wp-api-fetch', 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-data', 'wp-edit-post', 'wp-editor', 'wp-element', 'wp-i18n', 'wp-notices', 'wp-plugins'), 'version' => '4b03b61374bf5be15fd7');" );

		$assets = $this->scripts->get_assets( $assets_file );

		$this->assertSame(
			$assets,
			[
				'dependencies' => [
					'react',
					'wp-api-fetch',
					'wp-block-editor',
					'wp-blocks',
					'wp-components',
					'wp-data',
					'wp-edit-post',
					'wp-editor',
					'wp-element',
					'wp-i18n',
					'wp-notices',
					'wp-plugins',
				],
				'version'      => '4b03b61374bf5be15fd7',
			]
		);

		$this->destroy_mock_file( $assets_file );
	}
}
