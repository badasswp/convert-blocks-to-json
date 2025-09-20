<?php
/**
 * Scripts Service.
 *
 * Handle loading of JS scripts
 * and translations.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Services;

use ConvertBlocksToJSON\Abstracts\Route;
use ConvertBlocksToJSON\Abstracts\Service;
use ConvertBlocksToJSON\Interfaces\Kernel;

class Scripts extends Service implements Kernel {
	/**
	 * Slug.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public static $slug = 'convert-blocks-to-json';

	/**
	 * Bind to WP.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'register_translation' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_scripts' ] );
	}

	/**
	 * Register Scripts.
	 *
	 * @since 1.1.0
	 *
	 * @wp-hook 'enqueue_block_editor_assets'
	 */
	public function register_scripts() {
		$assets = $this->get_assets( plugin_dir_path( __FILE__ ) . '/../../dist/app.asset.php' );

		wp_enqueue_script(
			static::$slug,
			plugins_url( sprintf( '%s/dist/app.js', static::$slug ) ),
			$assets['dependencies'],
			$assets['version'],
			false,
		);

		// Handle undefined (reading 'limitExceeded') issue.
		wp_enqueue_media();

		wp_set_script_translations(
			static::$slug,
			static::$slug,
			plugin_dir_path( __FILE__ ) . '../../languages'
		);

		wp_localize_script(
			static::$slug,
			'cbtj',
			[
				'baseUrl'   => get_home_url(),
				'namespace' => Route::get_rest_namespace(),
			]
		);
	}

	/**
	 * Add Plugin text translation.
	 *
	 * @since 1.1.0
	 *
	 * @wp-hook 'init'
	 */
	public function register_translation() {
		load_plugin_textdomain(
			static::$slug,
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/../../languages'
		);
	}

	/**
	 * Get Asset dependencies.
	 *
	 * @since 1.1.0
	 *
	 * @param string $path Path to webpack generated PHP asset file.
	 * @return array
	 */
	protected function get_assets( string $path ): array {
		$assets = [
			'version'      => strval( time() ),
			'dependencies' => [],
		];

		if ( ! file_exists( $path ) ) {
			return $assets;
		}

		// phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		$assets = require_once $path;

		return $assets;
	}
}
