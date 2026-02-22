<?php
/**
 * MediaText Block.
 *
 * This class is responsible for customizing
 * the MediaText block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class MediaText extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not MediaText block.
		if ( empty( $block['name'] ) || 'core/media-text' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '{}', true );

		// Ensure missing URL attribute is captured for image blocks.
		preg_match( '/src="([^"]+)"/', $block['attributes']['content'] ?? '', $matches );
		$block['attributes']['mediaUrl'] = esc_url( $matches[1] ?? '' );

		// If it's not same site, get remote image.
		if ( false === strpos( $block['attributes']['mediaUrl'] ?? '', home_url() ) ) {
			$remote_image = $this->get_remote_image( $block['attributes']['mediaUrl'] ?? '' );

			if ( ! is_wp_error( $remote_image ) ) {
				$block['attributes']['mediaUrl'] = $remote_image;
			}
		}

		// Re-encode attributes correctly.
		$block['attributes'] = wp_json_encode( $block['attributes'] ?? [] );

		return $block;
	}

	/**
	 * Export Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $block Export Block.
	 * @return mixed[]
	 */
	public function export_block( $block ): array {
		// Bail out, if undefined OR not MediaText block.
		if ( empty( $block['name'] ) || 'core/media-text' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}

	/**
	 * Import Image.
	 *
	 * This is specific to importing images from
	 * an external or remote website.
	 *
	 * @since 1.3.0
	 *
	 * @param string $image_url Image URL.
	 * @return string|\WP_Error
	 */
	protected function get_remote_image( $image_url ) {
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! function_exists( 'wp_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		// Download the file to a temporary location.
		$tmp_file = download_url( sanitize_url( $image_url ) );

		// Bail out, if wp_error.
		if ( is_wp_error( $tmp_file ) ) {
			error_log(
				sprintf(
					'Import error: %s, %s',
					$tmp_file->get_error_message() ?? '',
					$image_url
				)
			);

			return $tmp_file;
		}

		// Get the filename + extension from the URL.
		$url_filename = basename( parse_url( $image_url, PHP_URL_PATH ) );

		// Build an array that resembles a PHP file upload.
		$filetype = wp_check_filetype( $url_filename );

		// Build an array that resembles a PHP file upload.
		$file = [
			'name'     => $url_filename,
			'type'     => $filetype['type'] ?? 'application/octet-stream',
			'tmp_name' => $tmp_file,
			'error'    => 0,
			'size'     => filesize( $tmp_file ),
		];

		// Let WordPress handle the sideload.
		$overrides = [
			'test_form'   => false,
			'test_size'   => true,
			'test_upload' => true,
		];

		$results = wp_handle_sideload( $file, $overrides );

		// Bail out, if upload error.
		if ( isset( $results['error'] ) ) {
			@unlink( $tmp_file );
			$error_message = sprintf( 'Import error: %s', $results['error'] ?? '' );
			error_log( $error_message );

			return new WP_Error( 'cbtj-import-error', $error_message );
		}

		// Now create attachment post for the image.
		$attachment = [
			'post_mime_type' => $results['type'],
			'post_title'     => sanitize_file_name( pathinfo( $url_filename, PATHINFO_FILENAME ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		];

		$attach_id = wp_insert_attachment( $attachment, $results['file'] );

		// Bail out, if wp_error.
		if ( is_wp_error( $attach_id ) ) {
			return $attach_id;
		}

		// Generate attachment metadata.
		$metadata = wp_generate_attachment_metadata( $attach_id, $results['file'] );
		wp_update_attachment_metadata( $attach_id, $metadata );

		return wp_get_attachment_url( $attach_id );
	}
}
