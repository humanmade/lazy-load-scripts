<?php
/**
 * Helper functions for Lazy Load Scripts
 */

declare( strict_types = 1 );

namespace HM\Lazy_Load_Scripts;

use _WP_Dependency;

/**
 * Collect entries to preload
 *
 * @since 0.0.1
 *
 * @param string   $type     Entries type, script or style (defaults to style).
 * @param string   $data_key Key to check in WP_Dependency object.
 * @param callable $callback Callback function to validate the data.
 *
 * @return array|null
 */
function collect_entries( string $type, string $data_key, callable $callback ): ?array {
	$assets = $type === 'script' ? wp_scripts() : wp_styles();
	$queued = $assets->queue;

	if ( empty( $queued ) ) {
		return null;
	}

	$entries = array_filter(
		$assets->registered,
		function ( _WP_Dependency $item ) use ( $queued, $data_key, $callback ): bool {
			return in_array( $item->handle, $queued, true )
				&& isset( $item->extra[ $data_key ] )
				&& $callback( $item->extra[ $data_key ], $item ) === true;
		}
	);

	return $entries;
}
