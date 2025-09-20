<?php
/**
 * Kernel Interface
 *
 * Establish base methods for Concrete classes
 * used across plugin.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Interfaces;

interface Kernel {
	/**
	 * Register logic.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void;
}
