<?php
/**
 * Import Service.
 *
 * Handle Import REST route registration
 * & WP hook bindings.
 *
 * @package ConvertBlocksToJSON
 */

namespace ConvertBlocksToJSON\Routes;

use ConvertBlocksToJSON\Abstracts\Route;
use ConvertBlocksToJSON\Interfaces\Router;

/**
 * Import class.
 */
class Import extends Route implements Router {
	/**
	 * Get, Post, Put, Patch, Delete.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public string $method = \WP_REST_Server::CREATABLE;

	/**
	 * Endpoint.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public string $endpoint = '/import';

	/**
	 * Get REST Response.
	 *
	 * This method grabs the JSON attachment
	 * that has been imported.
	 *
	 * @since 1.0.1
	 *
	 * @wp-hook 'rest_api_init'
	 *
	 * @param \WP_REST_Request $request Request Object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function rest_callback( $request ) {
		$args = $request->get_json_params();

		// Get Post ID & JSON file.
		$post_id   = (int) ( $args['id'] ?? '' );
		$json_file = get_attached_file( $post_id );

		// Bail out, if it does NOT exists.
		if ( ! file_exists( $json_file ) ) {
			return $this->get_400_response(
				sprintf(
					'File does not exists for ID: %s',
					$post_id
				)
			);
		}

		// Bail out, if it is not JSON.
		if ( 'json' !== wp_check_filetype( $json_file )['ext'] ?? '' ) {
			return $this->get_400_response(
				sprintf(
					'Fatal Error: Wrong file type: %s',
					$args['filename'] ?? ''
				)
			);
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$json   = file_get_contents( $json_file );
		$import = $this->get_blocks_import( json_decode( $json, true ), $post_id );

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
		$import = (array) apply_filters( 'cbtj_rest_import', $import, $post_id );

		return rest_ensure_response( $import );
	}

	/**
	 * Get Blocks Import.
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
	public function get_blocks_import( $json, $post_id ): array {
		$import_blocks = array_map(
			[ $this, 'get_import' ],
			$json['content'] ?? []
		);

		return $import_blocks;
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
	public function get_import( $block ): array {
		$children = [];

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $child_block ) {
				$children[] = $this->get_import( $child_block );
			}
		}

		$block['attributes']['content'] = $block['filtered'] ?? '';

		return [
			'name'        => $block['name'] ?? '',
			'attributes'  => wp_json_encode( $block['attributes'] ?? [] ),
			'innerBlocks' => $children ?? [],
		];
	}
}
