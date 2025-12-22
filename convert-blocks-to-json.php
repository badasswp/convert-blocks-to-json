<?php
/**
 * Plugin Name: Convert Blocks to JSON
 * Plugin URI:  https://github.com/badasswp/convert-blocks-to-json
 * Description: Convert your WP blocks to JSON.
 * Version:     1.2.0
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

define( 'CBTJ_AUTOLOAD', __DIR__ . '/vendor/autoload.php' );

// Composer Check.
if ( ! file_exists( CBTJ_AUTOLOAD ) ) {
	add_action(
		'admin_notices',
		function () {
			vprintf(
				/* translators: Plugin directory path. */
				esc_html__( 'Fatal Error: Composer not setup in %s', 'convert-blocks-to-json' ),
				[ __DIR__ ]
			);
		}
	);

	return;
}

// Run Plugin.
require_once CBTJ_AUTOLOAD;
( \ConvertBlocksToJSON\Plugin::get_instance() )->run();

/**
 * Flush Permalinks.
 *
 * @since 1.0.1
 * @since 1.0.4 Update permalink structure if empty.
 *
 * @wp-hook 'register_activation_hook'
 */
register_activation_hook(
	__FILE__,
	function () {
		if ( empty( get_option( 'permalink_structure' ) ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
			update_option( 'cbtj_flush_rewrite_rules', true );
		}
	}
);
