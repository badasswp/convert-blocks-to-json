<?php
/**
 * Container class.
 *
 * This class is responsible for registering the
 * plugin services.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Core;

use ConvertBlocksToJSON\Services\Boot;
use ConvertBlocksToJSON\Services\Routes;
use ConvertBlocksToJSON\Services\Scripts;
use ConvertBlocksToJSON\Interfaces\Kernel;

class Container implements Kernel {
	/**
	 * Services.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed[]
	 */
	public static array $services = [];

	/**
	 * Prepare Singletons.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		static::$services = [
			Boot::class,
			Routes::class,
			Scripts::class,
		];
	}

	/**
	 * Register Service.
	 *
	 * Establish singleton version for each Service
	 * concrete class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		foreach ( static::$services as $service ) {
			( $service::get_instance() )->register();
		}
	}
}
