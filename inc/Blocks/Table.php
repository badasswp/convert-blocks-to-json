<?php
/**
 * Table Block
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

		// Get the block content.
		$content = $block['attributes']['content'] ?? '';

		// Set the body.
		$block['attributes']['body'] = $this->get_table_body( $content );

		error_log( wp_json_encode( $block['attributes']['body'] ) );

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
				preg_match_all( '/<td>(.*?)<\/td>/', $tr, $matches );

				$cells = array_map(
					function ( $td ) {
						return [
							'content' => $td,
							'tag'     => 'td',
						];
					},
					$matches[0] ?? []
				);

				return [
					'cells' => $cells,
				];
			},
			$this->get_table_rows( $tbody )
		);
	}

	/**
	 * Get Table Rows
	 *
	 * @since 1.3.0
	 *
	 * @param string $content Table markup nested in `tbody`.
	 * @return mixed[]
	 */
	protected function get_table_rows( $content ): array {
		preg_match_all( '/<tr>(.*?)<\/tr>/', $content, $matches );

		return $matches[0] ?? [];
	}
}
