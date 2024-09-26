<?php
/**
 * Plugin Name: Convert Blocks to JSON
 * Plugin URI:  https://github.com/badasswp/convert-blocks-to-json
 * Description: Convert your WP blocks to JSON.
 * Version:     1.0.3
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: convert-blocks-to-json
 * Domain Path: /languages
 *
 * @package ConvertBlocksToJSON
 */

namespace badasswp\ConvertBlocksToJSON;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Load Scripts.
 *
 * @since 1.0.0
 *
 * @wp-hook 'enqueue_block_editor_assets'
 */
add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_script(
		'convert-blocks-to-json',
		trailingslashit( plugin_dir_url( __FILE__ ) ) . 'dist/app.js',
		[
			'wp-i18n',
			'wp-element',
			'wp-blocks',
			'wp-components',
			'wp-editor',
			'wp-hooks',
			'wp-compose',
			'wp-plugins',
			'wp-edit-post',
			'wp-edit-site',
		],
		'1.0.3',
		false,
	);

	// Handle undefined (reading 'limitExceeded') issue.
	wp_enqueue_media();

	wp_set_script_translations(
		'convert-blocks-to-json',
		'convert-blocks-to-json',
		plugin_dir_path( __FILE__ ) . 'languages'
	);

	wp_localize_script(
		'convert-blocks-to-json',
		'cbtj',
		[
			'url' => get_home_url(),
		]
	);
} );

/**
 * Add Translation.
 *
 * @since 1.0.0
 *
 * @wp-hook 'init'
 */
add_action( 'init', function() {
	load_plugin_textdomain(
		'convert-blocks-to-json',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
} );

/**
 * Setup REST routes.
 *
 * @since 1.0.0
 * @since 1.0.1 Added `import` REST endpoint.
 * @since 1.0.2 Serve is_user_permissible for permissions.
 *
 * @wp-hook 'rest_api_init'
 */
add_action( 'rest_api_init', function() {
	register_rest_route(
		'cbtj/v1',
		'/(?P<id>\d+)',
		[
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => __NAMESPACE__ . '\get_rest_response',
			'permission_callback' => '__return_true',
		],
	);

	register_rest_route(
		'cbtj/v1',
		'/import',
		[
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => __NAMESPACE__ . '\get_json_import',
			'permission_callback' => __NAMESPACE__ . '\is_user_permissible',
		],
	);
} );

/**
 * Register Mimes.
 *
 * @since 1.0.1
 *
 * @wp-hook 'upload_mimes'
 */
add_filter( 'upload_mimes', function( $mimes ) {
	return wp_parse_args(
		[
			'json' => 'application/json',
		],
		$mimes
	);
} );

/**
 * Flush Permalinks.
 *
 * @since 1.0.1
 *
 * @wp-hook 'register_activation_hook'
 */
register_activation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );

/**
 * Get REST Response.
 *
 * This method grabs the REST Response needed
 * for generating the JSON.
 *
 * @since 1.0.0
 *
 * @param \WP_REST_Request $request Request Object.
 * @return \WP_REST_Response
 *
 * @wp-hook 'rest_api_init'
 */
function get_rest_response( $request ): \WP_REST_Response {
	$post_id      = (int) $request->get_param( 'id' );
	$post_content = get_post_field( 'post_content', $post_id );

	$response = [
		'title'   => get_the_title( $post_id ),
		'content' => get_blocks( $post_content ),
	];

	/**
	 * Filter JSON Response.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $response Response Object.
	 * @param integer $post_id  Post ID.
	 *
	 * @return mixed[]
	 */
	$response = (array) apply_filters( 'cbtj_rest_response', $response, $post_id );

	return rest_ensure_response( $response );
}

/**
 * Get Blocks.
 *
 * This method is responsible for getting WP
 * valid blocks.
 *
 * @since 1.0.0
 *
 * @param string $post_content Post Content.
 * @return mixed[]
 */
function get_blocks( $post_content ): array {
	$all_blocks = array_map(
		__NAMESPACE__ . '\get_json',
		parse_blocks( $post_content )
	);

	$valid_blocks = array_filter(
		$all_blocks,
		function( $block ) {
			return ! empty ( $block['name'] );
		}
	);

	return array_values( $valid_blocks );
}

/**
 * Get JSON.
 *
 * Get all JSON block arrays and recursively
 * add children.
 *
 * @since 1.0.0
 *
 * @param mixed[] $block WP Blocks.
 * @return mixed[]
 */
function get_json( $block ): array {
	$children = [];

	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach( $block['innerBlocks'] as $child_block ) {
			$children[] = get_json( $child_block );
		}
	}

	return [
		'name'        => $block['blockName'] ?? '',
		'content'     => $block['innerHTML'] ?? '',
		'filtered'    => wp_strip_all_tags( $block['innerHTML'] ?? '' ),
		'attributes'  => $block['attrs'] ?? [],
		'innerBlocks' => $children
	];
}

/**
 * Get REST Response.
 *
 * This method grabs the JSON attachment
 * that has been imported.
 *
 * @since 1.0.1
 *
 * @param \WP_REST_Request $request Request Object.
 * @return \WP_REST_Response
 *
 * @wp-hook 'rest_api_init'
 */
function get_json_import( $request ): \WP_REST_Response {
	$args = $request->get_json_params();

	// Get Post ID & JSON file.
	$post_id   = (int) ( $args['id'] ?? '' );
	$json_file = get_attached_file( $post_id );

	//Bail out, if it does NOT exists.
	if ( ! file_exists( $json_file ) ) {
		return new \WP_Error(
			'cbtj-bad-request',
			sprintf(
				'Fatal Error: Bad Request, File does not exists for ID: %s',
				$post_id
			),
			[
				'status'  => 400,
				'request' => $args,
			]
		);
	}

	//Bail out, if it is not JSON.
	if ( 'json' !== wp_check_filetype( $json_file )['ext'] ?? '' ) {
		return new \WP_Error(
			'cbtj-bad-request',
			sprintf(
				'Fatal Error: Wrong file type: %s',
				$args['filename'] ?? ''
			),
			[
				'status'  => 400,
				'request' => $args,
			]
		);
	}

	$json   = file_get_contents( $json_file );
	$import = get_json_content( json_decode( $json, true ), $post_id );

	return new \WP_REST_Response( $import );
}

/**
 * Get JSON Content.
 *
 * Loop through the JSON array of blocks
 * and render as string.
 *
 * @param 1.0.1
 *
 * @param array   $json    JSON Array of Blocks.
 * @param integer $post_id Post ID.
 *
 * @return mixed[]
 */
function get_json_content( $json, $post_id ): array {
	$import = array_map(
		__NAMESPACE__ . '\get_content',
		$json['content'] ?? []
	);

	/**
	 * Filter JSON Import.
	 *
	 * @since 1.0.1
	 *
	 * @param mixed[] $response Import Blocks.
	 * @param integer $post_id  Post ID.
	 *
	 * @return mixed[]
	 */
	return (array) apply_filters( 'cbtj_rest_import', $import, $post_id );
}

/**
 * Get Import Content.
 *
 * Loop through the JSON blocks and format
 * correctly for use on JS.
 *
 * @since 1.0.1
 *
 * @param mixed[] $block Block array.
 * @return mixed[]
 */
function get_content( $block ): array {
	$children = [];

	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach( $block['innerBlocks'] as $child_block ) {
			$children[] = get_content( $child_block );
		}
	}

	$block['attributes']['content'] = $block['filtered'];

	return [
		'name'        => $block['name'] ?? '',
		'attributes'  => wp_json_encode( $block['attributes'] ?? [] ),
		'innerBlocks' => $children ?? [],
	];
}

/**
 * Is User Permissible?
 *
 * Validate that User has Admin capabilities
 * and Nonce is set correctly.
 *
 * @since 1.0.2
 *
 * @param \WP_REST_Request $request Request Object.
 * @return bool|\WP_Error
 *
 * @wp-hook 'rest_api_init'
 */
function is_user_permissible( $request ) {
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
