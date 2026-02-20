<?php
/**
 * Freeform Block.
 *
 * This class is responsible for customizing
 * the Freeform block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Freeform extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not Freeform block.
		if ( empty( $block['name'] ) || 'core/freeform' !== $block['name'] ) {
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
		// Treat as Freeform block.
		if ( empty( $block['name'] ) && ! empty( $block['content'] ) ) {
			$block['name'] = 'core/freeform';
		}

		return $block;
	}
}
