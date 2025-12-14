<?php
/**
 * Blocks Service.
 *
 * This service is responsible for binding Block
 * import customizations to WordPress.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Services;

use ConvertBlocksToJSON\Blocks\Image;
use ConvertBlocksToJSON\Abstracts\Block;
use ConvertBlocksToJSON\Abstracts\Service;
use ConvertBlocksToJSON\Interfaces\Kernel;

class Blocks extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function register(): void {
		$blocks = [
			Image::class
		];

		/**
		 * Filter Block classes.
		 *
		 * This provides a way to filter the block
		 * classes before they are called.
		 *
		 * @since 1.2.0
		 *
		 * @param Block[] $blocks Block classes.
		 * @return Block[]
		 */
		$blocks = apply_filters( 'cbtj_blocks', $blocks );

		/**
		 * Run `init` to filter Block types.
		 *
		 * @since 1.2.0
		 *
		 * @var Block $block
		 */
		foreach ( $blocks as $block ) {
			( new $block() )->init();
		}
	}
}
