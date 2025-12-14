<?php
/**
 * Export Service.
 *
 * Handle Export REST route registration
 * & WP hook bindings.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Routes;

use ConvertBlocksToJSON\Abstracts\Route;
use ConvertBlocksToJSON\Interfaces\Router;

/**
 * Export class.
 */
class Export extends Route implements Router {
	/**
	 * Get, Post, Put, Patch, Delete.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public string $method = \WP_REST_Server::READABLE;

	/**
	 * Endpoint.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public string $endpoint = '/(?P<id>\d+)';

	/**
	 * Get REST Response.
	 *
	 * This method gets exportable JSON data
	 * for the blocks.
	 *
	 * @since 1.0.0
	 *
	 * @wp-hook 'rest_api_init'
	 *
	 * @param \WP_REST_Request $request Request Object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function rest_callback( $request ) {
		$post_id      = (int) $request->get_param( 'id' );
		$post_content = get_post_field( 'post_content', $post_id );

		$export = [
			'title'   => get_the_title( $post_id ),
			'content' => $this->get_blocks_export( $post_content ),
		];

		/**
		 * Filter Export.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed[] $export  Export data.
		 * @param integer $post_id Post ID.
		 *
		 * @return mixed[]
		 */
		$export = (array) apply_filters( 'cbtj_rest_export', $export, $post_id );

		return rest_ensure_response( $export );
	}

	/**
	 * Get Blocks Export.
	 *
	 * This method is responsible for getting WP
	 * valid blocks.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_content Post Content.
	 * @return mixed[]
	 */
	public function get_blocks_export( $post_content ): array {
		$export_blocks = array_map(
			[ $this, 'get_export' ],
			parse_blocks( $post_content )
		);

		// Some blocks turn up nullable, let's remove them.
		$valid_blocks = array_filter(
			$export_blocks,
			function ( $block ) {
				return ! empty( $block['name'] );
			}
		);

		return array_values( $valid_blocks );
	}

	/**
	 * Get Export Content.
	 *
	 * Loop through the JSON blocks and recursively
	 * add children blocks.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $block Block array.
	 * @return mixed[]
	 */
	public function get_export( $block ): array {
		$children = [];

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $child_block ) {
				$children[] = $this->get_export( $child_block );
			}
		}

		return [
			'name'        => $block['blockName'] ?? '',
			'content'     => $block['innerHTML'] ?? '',
			'filtered'    => wp_strip_all_tags( $block['innerHTML'] ?? '' ),
			'attributes'  => $block['attrs'] ?? [],
			'innerBlocks' => $children,
		];
	}
}
