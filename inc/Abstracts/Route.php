<?php
/**
 * Route abstraction.
 *
 * This abstract class defines a foundation for creating
 * route classes which act as WP REST end points.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Abstracts;

use ConvertBlocksToJSON\Interfaces\Router;

/**
 * Route class.
 */
abstract class Route implements Router {
	/**
	 * REST Callback.
	 *
	 * Also known as the Request Callback. This method is
	 * responsible for getting the $request data and passing it along
	 * to the response method.
	 *
	 * @since 1.1.0
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	abstract public function rest_callback( $request );

	/**
	 * Permission Callback.
	 *
	 * @since 1.1.0
	 *
	 * @return string|array
	 */
	public function get_permission_callback() {
		if ( \WP_REST_Server::READABLE === $this->method ) {
			return '__return_true';
		}

		return [ $this, 'is_user_permissible' ];
	}

	/**
	 * Register REST Route.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_route(): void {
		/**
		 * Filter REST namespace.
		 *
		 * Provide users the ability to customize the
		 * default REST namespace.
		 *
		 * @since 1.1.0
		 *
		 * @return string
		 */
		$rest_namespace = (string) apply_filters( 'cbtj_rest_namespace', 'cbtj/v1' );

		register_rest_route(
			$rest_namespace,
			$this->endpoint,
			[
				'methods'             => $this->method,
				'callback'            => [ $this, 'rest_callback' ],
				'permission_callback' => $this->get_permission_callback(),
			]
		);
	}

	/**
	 * Get 400 Response.
	 *
	 * This method returns a 400 response for Bad
	 * requests submitted.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message Error Msg.
	 * @return \WP_Error
	 */
	public function get_400_response( $message ): \WP_Error {
		$args = $this->request->get_json_params();

		return new \WP_Error(
			'cbtj-bad-request',
			sprintf(
				'Fatal Error: Bad Request, %s',
				$message
			),
			[
				'status'  => 400,
				'request' => $args,
			]
		);
	}

	/**
	 * Is User Permissible?
	 *
	 * Validate that User has Admin capabilities
	 * and Nonce is set correctly.
	 *
	 * @since 1.0.2
	 *
	 * @wp-hook 'rest_api_init'
	 *
	 * @param \WP_REST_Request $request Request Object.
	 * @return bool|\WP_Error
	 */
	public function is_user_permissible( $request ) {
		$http_error = rest_authorization_required_code();

		if ( ! current_user_can( 'administrator' ) ) {
			return new \WP_Error(
				'cbtj-rest-forbidden',
				sprintf( 'Invalid User. Error: %s', $http_error ),
				[ 'status' => $http_error ]
			);
		}

		if ( ! wp_verify_nonce( $request->get_header( 'X-WP-Nonce' ), 'wp_rest' ) ) {
			return new \WP_Error(
				'cbtj-rest-forbidden',
				sprintf( 'Invalid Nonce. Error: %s', $http_error ),
				[ 'status' => $http_error ]
			);
		}

		return true;
	}
}
