<?php
/**
 * List Block.
 *
 * This class is responsible for customizing
 * the List block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Lists extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not List block.
		if ( empty( $block['name'] ) || 'core/list' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '{}', true );

		// Unset the content.
		unset( $block['attributes']['content'] );

		return [
			'name'        => $block['name'] ?? '',
			'attributes'  => wp_json_encode( $block['attributes'] ),
			'innerBlocks' => $block['innerBlocks'] ?? [],
		];
	}

	/**
	 * Export Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Export Block.
	 * @return mixed[]
	 */
	public function export_block( $block ): array {
		// Bail out, if undefined OR not List block.
		if ( empty( $block['name'] ) || 'core/list' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
