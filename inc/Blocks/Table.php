<?php
/**
 * Table Block.
 *
 * This class is responsible for customizing
 * the Table block output.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Blocks;

use ConvertBlocksToJSON\Abstracts\Block;

class Table extends Block {
	/**
	 * Import Block.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	public function import_block( $block ): array {
		// Bail out, if undefined OR not Table block.
		if ( empty( $block['name'] ) || 'core/table' !== $block['name'] ) {
			return $block;
		}

		// Decode attributes correctly.
		$block['attributes'] = json_decode( $block['attributes'] ?? '{}', true );

		// Set the body.
		$block['attributes']['body'] = $this->get_table_body( $block['attributes']['content'] ?? '' );

		// Re-encode attributes correctly.
		$block['attributes'] = wp_json_encode( $block['attributes'] ?? [] );

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
		// Bail out, if undefined OR not Table block.
		if ( empty( $block['name'] ) || 'core/table' !== $block['name'] ) {
			return $block;
		}

		return $block;
	}

	/**
	 * Get Table body.
	 *
	 * @since 1.3.0
	 *
	 * @param string $content Table markup or innerHTML.
	 * @return mixed[]
	 */
	protected function get_table_body( $content ): array {
		$tbody = $this->get_tag_content( $content, 'tbody' );

		return array_map(
			function ( $tr ) {
				$cells = array_map(
					function ( $td ) {
						return [
							'content' => $td,
							'tag'     => 'td',
						];
					},
					$this->get_tag_content( $tr, 'td', false )
				);

				return [
					'cells' => $cells,
				];
			},
			$this->get_tag_content( $tbody, 'tr', false )
		);
	}
}
