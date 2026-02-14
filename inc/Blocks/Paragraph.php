<?php
/**
 * Paragraph Block
 *
 * This class is responsible for customizing
 * the paragraph block output
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Paragraph extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		//Bail out, if undefined OR not Paragraph block.
		if ( empty( $block['name'] ) || 'core/paragraph' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}

	/**
	 * Export Block
	 *
	 * @since 1.3.0
	 *
	 * @param mixed $block Export Block.
	 * @return mixed[]
	 */
	public function export_block( $block ): array {
		//Bail out, if undefined OR not paragraph block.
		if ( empty( $block['name'] ) || 'core/paragraph' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
