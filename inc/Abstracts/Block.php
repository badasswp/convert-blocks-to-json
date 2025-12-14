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
	 * Modify Block.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed[] $block Import Block.
	 * @return mixed[]
	 */
	abstract public function modify_block( $block ): array;

	/**
	 * Subscribe to `cbtj_import_block`.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'cbtj_import_block', [ $this, 'modify_block' ] );
	}
}
