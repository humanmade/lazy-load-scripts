<?php
/**
 * Preload assets
 */

declare( strict_types = 1 );

namespace HM\Lazy_Load_Scripts\Preload;

use HM\Lazy_Load_Scripts as LLS;

/**
 * Bootstrapper
 *
 * @since 0.0.1
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'wp_head', __NAMESPACE__ . '\\print_head_preload_links', 5 );
	add_action( 'wp_head', __NAMESPACE__ . '\\print_head_preload_js', 5 );
}

/**
 * Validate data
 *
 * @since 0.0.1
 *
 * @param mixed $data Data to validate.
 *
 * @return bool
 */
function validate_data( $data ): bool {
	return $data === true;
}

/**
 * Print preload links on document head
 *
 * @since 0.0.1
 *
 * @return void
 */
function print_head_preload_links(): void {
	$entries = LLS\collect_entries( 'styles', 'preload', __NAMESPACE__ . '\\validate_data' );
	$assets = wp_styles();

	if ( empty( $entries ) ) {
		return;
	}

	$wp_version = get_bloginfo( 'version' );

	foreach ( $entries as $handle => $entry ) {
		if ( empty( $entry->src ) ) {
			continue;
		}

		$src = $entry->src;

		if ( strpos( $src, '/' ) === 0 ) {
			$src = home_url( $src );
		}

		if ( $entry->ver === false ) {
			$src = add_query_arg( 'ver', $wp_version, $src );
		}

		$defer = $assets->get_data( $handle, 'defer' );

		if ( ! $defer ) {
			printf( '<link rel="preload" href="%s" as="style" />%s', esc_url_raw( $src ), "\n" );
			continue;
		}

		printf( '<link rel="preload" id="%s" href="%s" as="style" onload="this.media=\'all\';this.rel=\'stylesheet\';this.onload=null;"/>%s', esc_attr( $entry->handle ), esc_url_raw( $src ), "\n" );
		printf( '<noscript><link rel="stylesheet" href="%s"></noscript>', esc_url_raw( $src ), "\n" );

		$inline_style = $assets->get_data( $handle, 'after' );

		if ( $inline_style ) {
			$assets->print_inline_style( $handle, true );
		}

		// Disable style enqueue from the core.
		wp_dequeue_style( $handle );
	}
}

/**
 * Print preload js on document head
 *
 * @since 0.1.3
 *
 * @return void
 */
function print_head_preload_js(): void {
	$entries = LLS\collect_entries( 'script', 'preload', __NAMESPACE__ . '\\validate_data' );

	if ( empty( $entries ) ) {
		return;
	}

	$wp_version = get_bloginfo( 'version' );

	foreach ( $entries as $handle => $entry ) {
		if ( empty( $entry->src ) ) {
			continue;
		}

		$src = $entry->src;

		if ( strpos( $src, '/' ) === 0 ) {
			$src = home_url( $src );
		}

		if ( $entry->ver === false ) {
			$src = add_query_arg( 'ver', $wp_version, $src );
		}

		printf( '<link rel="preload" href="%s" as="script" />%s', esc_url_raw( $src ), "\n" );
	}
}
