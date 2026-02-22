<?php
/**
 * Image Block.
 *
 * This class is responsible for customizing
 * the Image block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Image extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not Image block.
		if ( empty( $block['name'] ) || 'core/image' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '', true );

		// Ensure missing URL attribute is captured for image blocks.
		preg_match( '/src="([^"]+)"/', $block['attributes']['content'] ?? '', $matches );
		$block['attributes']['url'] = esc_url( $matches[1] ?? '' );

		// If it's not same site, get remote image.
		if ( false === strpos( $block['attributes']['url'] ?? '', home_url() ) ) {
			$remote_image = $this->get_remote_file( $block['attributes']['url'] ?? '' );

			if ( ! is_wp_error( $remote_image ) ) {
				$block['attributes']['url'] = $remote_image;
			}
		}

		// Re-encode attributes correctly.
		$block['attributes'] = wp_json_encode( $block['attributes'] ?? [] );

		return $block;
	}

	/**
	 * Export Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Export Block.
	 * @return mixed[]
	 */
	public function export_block( $block ): array {
		// Bail out, if undefined OR not Image block.
		if ( empty( $block['name'] ) || 'core/image' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
