<?php
/**
 * AccordionHeading Block.
 *
 * This class is responsible for customizing
 * the AccordionHeading block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class AccordionHeading extends Block {

	/**
	 * Import Block.
	 *
	 * @since 1.4.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	// public function import_block( $block ): array {
	//  if ( empty( $block['name'] ) || 'core/accordion-heading' !== $block['name'] ) {
	//      return $block;
	//  }

	//  // Title lives in `filtered`. Put it into attributes so JS can read it.
	//  $block['attributes'] = wp_json_encode( [
	//      'content' => $block['filtered'] ?? '',
	//  ] );

	//  return $block;
	// }
	public function import_block( $block ): array {
		// Bail out, if undefined OR not accordionHeading block.
		if ( empty( $block['name'] ) || 'core/accordion-heading' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '{}', true );

		$block['attributes']['content'] = $this->get_tag_content( $block['content'] ?? '', 'span' );

		// Re-encode attributes correctly.
		$block['attributes'] = wp_json_encode( $block['attributes'] ?? [] );

		return $block;
	}

	/**
	 * Export Block.
	 *
	 * @since 1.4.0
	 *
	 * @param mixed $block Export Block.
	 * @return mixed[]
	 */
	public function export_block( $block ): array {
		// Bail out, if undefined OR not accordionHeading block.
		if ( empty( $block['name'] ) || 'core/accordion-heading' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}
}
