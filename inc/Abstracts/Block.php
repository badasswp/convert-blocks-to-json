<?php
/**
 * Block Abstraction.
 *
 * This base class describes the public methods accessible
 * to the Block classes for implementation ease.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Abstracts;

use WP_Error;
use Throwable;

/**
 * Block class.
 */
abstract class Block {
	/**
	 * Import Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	abstract public function import_block( $block ): array;

	/**
	 * Export Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Export Block.
	 * @return mixed[]
	 */
	abstract public function export_block( $block ): array;

	/**
	 * Subscribe to `cbtj_import_block`.
	 * Subsrcibe to `cbtj_export_block`.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'cbtj_import_block', [ $this, 'import_block' ] );
		add_filter( 'cbtj_export_block', [ $this, 'export_block' ] );
	}

	/**
	 * Get clean markup.
	 *
	 * This is useful for cleaning dirty markup
	 * returned from block content.
	 *
	 * For e.g. This markup:
	 * - <p><p>What a <span>wonderful</span> world!</p></p>
	 *
	 * cleans and returns:
	 * - What a <span>wonderful</span> world!
	 *
	 * @since 1.3.0
	 *
	 * @param string $markup Dirty markup.
	 * @return string
	 */
	public function get_clean_markup( $markup ) {
		return preg_replace( sprintf( '/<\/?%s\b[^>]*>/', $this->tag ?? '' ), '', $markup );
	}

	/**
	 * Get Tag content.
	 *
	 * This is useful for getting the text that is
	 * nested within a specific tag.
	 *
	 * For e.g. This markup:
	 * - <div><span> Hi </span><p>What a <span>wonderful</span> world!</p></div>
	 *
	 * for a `p` tag returns:
	 * - What a <span>wonderful</span> world!
	 *
	 * @since 1.3.0
	 *
	 * @param string $markup Block markup.
	 * @param string $tag    Specific tag.
	 *
	 * @return string
	 */
	public function get_tag_content( $markup, $tag ): string {
		preg_match( sprintf( '/<%1$s>(.*?)<\/%1$s>/', $tag ), $markup, $match );

		return $match[1] ?? '';
	}

	/**
	 * Get Remote File.
	 *
	 * This is specific to importing files from
	 * an external or remote website. Just to clarify,
	 * file could be an image, audio or video.
	 *
	 * @since 1.3.0
	 *
	 * @param string $file_url File URL.
	 * @return string|\WP_Error
	 */
	protected function get_remote_file( $file_url ) {
		if ( ! function_exists( 'download_url' ) || ! function_exists( 'wp_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		if ( ! function_exists( 'wp_read_audio_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		// Download the file to a temporary location.
		$tmp_file = download_url( sanitize_url( $file_url ) );

		// Bail out, if is WP_Error.
		if ( is_wp_error( $tmp_file ) ) {
			error_log(
				sprintf(
					'Import error: %s, %s',
					$tmp_file->get_error_message() ?? '',
					$file_url
				)
			);

			return $tmp_file;
		}

		// Get the filename + extension from the URL.
		$url_filename = basename( parse_url( $file_url, PHP_URL_PATH ) );

		// Get the file type.
		$filetype = wp_check_filetype( $url_filename );

		// Build an array that resembles a PHP file upload.
		$file = [
			'name'     => $url_filename,
			'type'     => $filetype['type'] ?? 'application/octet-stream',
			'tmp_name' => $tmp_file,
			'error'    => 0,
			'size'     => filesize( $tmp_file ),
		];

		// Let WordPress handle the upload correctly.
		$results = wp_handle_sideload(
			$file,
			[
				'test_form'   => false,
				'test_size'   => true,
				'test_upload' => true,
			]
		);

		// Bail out, if upload error.
		if ( isset( $results['error'] ) ) {
			@unlink( $tmp_file );
			$error_message = sprintf( 'Import error: %s', $results['error'] ?? '' );
			error_log( $error_message );

			return new WP_Error( 'cbtj-import-error', $error_message );
		}

		// The imported file url path.
		$imported_file_url = $results['file'] ?? '';

		// Now create attachment post for the image.
		$attach_id = wp_insert_attachment(
			[
				'post_mime_type' => $results['type'] ?? '',
				'post_title'     => sanitize_file_name( pathinfo( $url_filename, PATHINFO_FILENAME ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			],
			$imported_file_url
		);

		// Bail out, if is WP_Error.
		if ( is_wp_error( $attach_id ) ) {
			return $attach_id;
		}

		try {
			$metadata = wp_generate_attachment_metadata( $attach_id, $imported_file_url );
			wp_update_attachment_metadata( $attach_id, $metadata );
		} catch ( Throwable $e ) {
			error_log( 'Fatal caught: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );

			return new WP_Error( 'cbtj-metadata-error', $e->getMessage() );
		}

		return wp_get_attachment_url( $attach_id );
	}
}
