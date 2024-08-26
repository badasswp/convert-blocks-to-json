<?php
/**
 * Plugin Name: Convert Blocks to JSON
 * Plugin URI:  https://github.com/badasswp/convert-blocks-to-json
 * Description: Convert your WP blocks to JSON.
 * Version:     1.0.0
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
		'1.0.0',
		false,
	);

	wp_set_script_translations(
		'convert-blocks-to-json',
		'convert-blocks-to-json',
		plugin_dir_path( __FILE__ ) . 'languages'
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
 *
 * @wp-hook 'rest_api_init'
 */
add_action( 'rest_api_init', function() {
	register_rest_route(
		'cbtj/v1',
		'/(?P<id>\d+)',
		[
			'methods' => 'GET',
			'callback' => __NAMESPACE__ . '\get_rest_response',
			'permission_callback' => '__return_true'
		],
	);
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
			return ! is_null ( $block['name'] );
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
		'name'       => $block['blockName'],
		'content'    => $block['innerHTML'],
		'filtered'   => wp_strip_all_tags( $block['innerHTML'] ),
		'attributes' => $block['attrs'],
		'children'   => $children
	];
}
