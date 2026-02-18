<?php
/**
 * Paragraph Block
 *
 * This class is responsible for customizing
 * the Paragraph block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Paragraph extends Block {
	/**
	 * Element name.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public string $tag = 'p';

	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not Paragraph block.
		if ( empty( $block['name'] ) || 'core/paragraph' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '{}', true );

		// Set the content.
		$block['attributes']['content'] = $this->get_clean_markup( $block['attributes']['content'] ?? '' );

		return [
			'name'        => $block['name'] ?? '',
			'attributes'  => wp_json_encode( $block['attributes'] ?? [] ),
			'innerBlocks' => $block['innerBlocks'] ?? [],
		];

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
		// Bail out, if undefined OR not paragraph block.
		if ( empty( $block['name'] ) || 'core/paragraph' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
