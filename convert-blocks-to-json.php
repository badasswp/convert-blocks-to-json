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

	return rest_ensure_response( $response );
}

/**
 * Get Blocks.
 *
 * This method is responsible for getting WP
 * valid blocks.
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
