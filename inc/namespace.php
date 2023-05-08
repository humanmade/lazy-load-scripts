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

/**
 * Prepare asset url
 *
 * @author Justin Slamka <jslamka5685@gmail.com>
 *
 * @param string $dir Asset directory.
 *
 * @return string
 */
function prepare_asset_url( string $dir ) {
	$url = content_url( str_replace( WP_CONTENT_DIR, '', $dir ) );
	$url_matches_pattern = preg_match( '/(?<address>http(?:s?):\/\/.*\/)(?<fullPath>wp-content(?<removablePath>\/.*)\/(?:plugins|themes)\/.*)/', $url, $url_parts );

	if ( $url_matches_pattern === 0 ) {
		return $url;
	}

	['address' => $address, 'fullPath' => $full_path, 'removablePath' => $removable_path] = $url_parts;

	return sprintf( '%s%s', $address, str_replace( $removable_path, '', $full_path ) );
}
