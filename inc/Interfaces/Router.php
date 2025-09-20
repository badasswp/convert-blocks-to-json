<?php
/**
 * Router Interface.
 *
 * This interface defines a contract for routes
 * and defines common methods that derived classes
 * should implement.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Interfaces;

interface Router {
	/**
	 * REST Callback.
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function rest_callback( $request );
}
