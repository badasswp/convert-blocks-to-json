<?php
/**
 * Block Abstraction.
 *
 * This base class describes the public methods accessible
 * to the Block classes for implementation ease.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Abstracts;

/**
 * Block class.
 */
abstract class Block {
	/**
	 * Import Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	abstract public function import_block( $block ): array;

	/**
	 * Export Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Export Block.
	 * @return mixed[]
	 */
	abstract public function export_block( $block ): array;

	/**
	 * Subscribe to `cbtj_import_block`.
	 * Subsrcibe to `cbtj_export_block`.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'cbtj_import_block', [ $this, 'import_block' ] );
		add_filter( 'cbtj_export_block', [ $this, 'export_block' ] );
	}
}
