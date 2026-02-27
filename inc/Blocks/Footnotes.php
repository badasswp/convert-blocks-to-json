<?php
/**
 * Footnotes Block.
 *
 * This class is responsible for customizing
 * the Footnotes block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Footnotes extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not Footnotes block.
		if ( empty( $block['name'] ) || 'core/footnotes' !== $block['name'] ) {
			return $block;
		}

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
		// Bail out, if undefined OR not Footnotes block.
		if ( empty( $block['name'] ) || 'core/footnotes' !== $block['name'] ) {
			return $block;
		}

		// Get Post ID.
		$post_id = absint( basename( wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) ) );

		// Set the content.
		$block['attributes']['footnotes'] = get_post_meta( $post_id, 'footnotes', true );

		return $block;
	}
}
