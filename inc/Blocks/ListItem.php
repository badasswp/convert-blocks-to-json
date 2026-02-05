<?php
/**
 * ListItem Block.
 *
 * This class is responsible for customizing
 * the ListItem block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class ListItem extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not ListItem block.
		if ( empty( $block['name'] ) || 'core/list-item' !== $block['name'] ) {
			return $block;
		}

		return [
			'name'        => $block['name'] ?? '',
			'attributes'  => $block['attributes'] ?? '{}',
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
		// Bail out, if undefined OR not ListItem block.
		if ( empty( $block['name'] ) || 'core/list-item' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
