<?php
/**
 * Details Block
 *
 * This class is responsible for customizing
 * the Details block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Details extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		//Bail out, if undefined OR not Details block.
		if ( empty( $block['name'] ) || 'core/details' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '{}', true );

		// Get the block content.
		$content = $block['attributes']['content'] ?? '';

		// Set the summary.
		$block['attributes']['summary'] = $this->get_tag_content( $content, 'summary' );

		return [
			'name'        => $block['name'] ?? '',
			'attributes'  => wp_json_encode( $block['attributes'] ?? [] ),
			'innerBlocks' => $block['innerBlocks'] ?? [],
		];

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
		//Bail out, if undefined OR not Details block.
		if ( empty( $block['name'] ) || 'core/details' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
