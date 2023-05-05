<?php
/**
 * Add async attribute to scripts
 */

declare( strict_types = 1 );

namespace HM\Lazy_Load_Scripts\Async;

/**
 * Bootstrapper
 *
 * @since 0.0.1
 *
 * @return void
 */
function bootstrap(): void {
	add_filter( 'script_loader_tag', __NAMESPACE__ . '\\add_async_attribute', 10, 2 );
}

/**
 * Add async attribute to script tag
 *
 * @param string $tag    Original script tag.
 * @param string $handle Script handle.
 *
 * @return string Modified script tag if applicable. Otherwise, the original one.
 */
function add_async_attribute( string $tag, string $handle ): string {
	$wp_scripts = wp_scripts();
	$item = $wp_scripts->query( $handle );

	if ( ! empty( $item ) && isset( $item->extra['async'] ) && $item->extra['async'] === true ) {
		$tag = preg_replace( '#(<script)(.*></script>)#', '$1 async$2', $tag );
	}

	return $tag;
}
