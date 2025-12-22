<?php
/**
 * Boot Service.
 *
 * Handle permalink flush and mime
 * config settings.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Services;

use ConvertBlocksToJSON\Abstracts\Service;
use ConvertBlocksToJSON\Interfaces\Kernel;

class Boot extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', [ $this, 'flush_permalinks' ] );
		add_filter( 'upload_mimes', [ $this, 'register_json_mime' ] );
	}

	/**
	 * Flush Permalinks.
	 *
	 * We need to flush permalinks to ensure
	 * that permalinks is correctly set.
	 *
	 * @since 1.0.4 Flush Permalinks.
	 *
	 * @wp-hook 'admin_init'
	 */
	public function flush_permalinks() {
		if ( get_option( 'cbtj_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			delete_option( 'cbtj_flush_rewrite_rules' );
		}
	}

	/**
	 * Register Mimes.
	 *
	 * This enables us upload JSON files using
	 * the WP media upload modal.
	 *
	 * @since 1.0.1
	 *
	 * @wp-hook 'upload_mimes'
	 */
	public function register_json_mime( $mimes ) {
		if ( ! isset( $mimes['json'] ) ) {
			$mimes['json'] = 'application/json';
			return $mimes;
		}

		return $mimes;
	}
}
