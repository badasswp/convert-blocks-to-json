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
			'callback' => function( $request ) {
				$post_id      = (int) $request->get_param( 'id' );
				$post_content = get_post_field( 'post_content', $post_id );

				$response = [
					'title'   => get_the_title( $post_id ),
					'content' => get_blocks( $post_content ),
				];

				return rest_ensure_response( $response );
			},
			'permission_callback' => '__return_true'
		],
	);
} );
