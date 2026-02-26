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

		// Get table content.
		$table_content = $block['attributes']['content'] ?? '';

		// Set the table parts.
		$block['attributes']['head'] = $this->get_table_part( $table_content, 'thead', 'th' );
		$block['attributes']['body'] = $this->get_table_part( $table_content, 'tbody', 'td' );
		$block['attributes']['foot'] = $this->get_table_part( $table_content, 'tfoot', 'td' );

		// Set the table caption.
		$block['attributes']['caption'] = $this->get_tag_content( $table_content, 'figcaption' );

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
	 * Get Table part.
	 *
	 * @since 1.3.0
	 *
	 * @param string $content Table markup or innerHTML.
	 * @param string $part    Table part for e.g. 'tbody'.
	 * @param string $cell    Table cell name for e.g. 'td'.
	 *
	 * @return mixed[]
	 */
	protected function get_table_part( $content, $part, $cell ): array {
		$section = $this->get_tag_content( $content, $part );

		return array_map(
			function ( $tr ) use ( $cell ) {
				$cells = array_map(
					function ( $td ) use ( $cell ) {
						return [
							'content' => $td,
							'tag'     => $cell,
						];
					},
					$this->get_tag_content( $tr, $cell, false )
				);

				return [
					'cells' => $cells,
				];
			},
			$this->get_tag_content( $section, 'tr', false )
		);
	}
}
