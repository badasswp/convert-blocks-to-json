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
			$remote_image = $this->get_remote_file( $block['attributes']['mediaUrl'] ?? '' );

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
}
