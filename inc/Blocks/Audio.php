<?php
/**
 * Audio Block.
 *
 * This class is responsible for customizing
 * the Audio block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Audio extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not Audio block.
		if ( empty( $block['name'] ) || 'core/audio' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '', true );

		// Ensure missing SRC attribute is captured for audio blocks.
		preg_match( '/src="([^"]+)"/', $block['attributes']['content'] ?? '', $matches );
		$block['attributes']['src'] = esc_url( $matches[1] ?? '' );

		// If it's not same site, get remote file.
		if ( false === strpos( $block['attributes']['src'] ?? '', home_url() ) ) {
			$remote_file = $this->get_remote_file( $block['attributes']['src'] ?? '' );

			if ( ! is_wp_error( $remote_file ) ) {
				$block['attributes']['src'] = $remote_file;
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
		// Bail out, if undefined OR not Audio block.
		if ( empty( $block['name'] ) || 'core/audio' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
