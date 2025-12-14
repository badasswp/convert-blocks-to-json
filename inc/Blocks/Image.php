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
	 * Modify Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function modify_block( $block ): array {
		// Bail out, if undefined OR not Image block.
		if ( empty( $block['name'] ) || 'core/image' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '', true );

		// Ensure missing URL attribute is captured for image blocks.
		preg_match( '/src="([^"]+)"/', $block['originalContent'] ?? '', $matches );
		$block['attributes']['url'] = esc_url( $matches[1] ?? '' );

		// Re-encode attributes correctly.
		$block['attributes'] = wp_json_encode( $block['attributes'] );

		return $block;
	}
}
