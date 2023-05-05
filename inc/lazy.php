<?php
/**
 * Lazy load scripts
 */

declare ( strict_types = 1 );

namespace HM\Lazy_Load_Scripts\Lazy;

use HM\Lazy_Load_Scripts as LLS;
use HM\Lazy_Load_Scripts\Vite;

const SCRIPT_HANDLE = 'lazy-load-scripts';

/**
 * Bootstrapper
 *
 * @since 0.0.1
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\register_script' );
	// This needs to happen between 'wp_enqueue_scripts' and 'wp_print_scripts'.
	add_action( 'wp_head', __NAMESPACE__ . '\\collect_entries', 8 );
	// This collects entries that are registered between wp_head and wp_print_footer_scripts.
	add_action( 'wp_footer', __NAMESPACE__ . '\\collect_entries', 19 );
	add_action( 'wp_print_footer_scripts', __NAMESPACE__ . '\\print_script' );
}

/**
 * Get entries
 *
 * @since 0.0.1
 *
 * @return array
 */
function get_entries(): array {
	/**
	 * Filter entries to load lazily
	 *
	 * @param array $entries Array of entries.
	 */
	$scripts = apply_filters( 'lazy_load_scripts.entries', [] );

	return $scripts;
}

/**
 * Register our own script
 *
 * @since 0.0.1
 *
 * @return void
 */
function register_script(): void {
	Vite\register_asset(
		dirname( __DIR__ ) . '/assets/dist/',
		'assets/src/index.js',
		[ 'handle' => SCRIPT_HANDLE ]
	);
	wp_script_add_data( SCRIPT_HANDLE, 'async', true );
}

/**
 * Add entry
 *
 * @param array $entry Entry to lazy load.
 *
 * @return void
 */
function add_entry( array $entry ): void {
	add_filter(
		'lazy_load_scripts.entries',
		function ( array $entries ) use ( $entry ): array {
			return array_merge( $entries, [ $entry ] );
		}
	);
}

/**
 * Validate entry data
 *
 * @param mixed $value Entry data to validate.
 *
 * @return bool
 */
function validate_data( $value ): bool {
	return is_array( $value ) && ! empty( $value['element_selectors'] );
}

/**
 * Collect registered scripts that are marked for lazy loading.
 *
 * @return void
 */
function collect_entries(): void {
	$entries = LLS\collect_entries( 'script', 'lazy', __NAMESPACE__ . '\\validate_data' );

	if ( empty( $entries ) ) {
		return;
	}

	$before_script = '';

	foreach ( $entries as $script ) {
		// TODO: Process the dependencies.
		// TODO: Collect the after scripts.

		$config = wp_parse_args(
			$script->extra['lazy'],
			[
				'offset' => '0px',
				'script_id' => "{$script->handle}-js",
				'script_src' => $script->src,
			]
		);

		if ( ! empty( $script->extra['before'] ) ) {
			$before_script .= join( "\n", array_filter( $script->extra['before'] ) );
		}

		add_entry( $config );
		wp_dequeue_script( $script->handle );
	}

	if ( ! empty( $before_script ) ) {
		printf(
			'<script id="lazy-load-scripts-%s-before">%s</script>',
			esc_attr( current_action() === 'wp_head' ? 'head' : 'footer' ),
			// phpcs:ignore
			$before_script
		);
	}
}

/**
 * Parse entry config
 *
 * @param array $entry Entry config array.
 *
 * @return array|null
 */
function parse_entry_config( array $entry ): ?array {
	$defaults = [
		'element_selectors' => '',
		'offset' => '0px',
		'script_id' => '',
		'script_src' => '',
		'script_async' => false,
	];

	$parsed = wp_parse_args( $entry, $defaults );

	if ( empty( $parsed['element_selectors'] ) || empty( $parsed['script_src'] ) ) {
		return null;
	}

	return $parsed;
}

/**
 * Print script
 *
 * @since 0.0.1
 *
 * @return void
 */
function print_script(): void {
	$entries = array_map( __NAMESPACE__ . '\\parse_entry_config', get_entries() );
	$entries = array_filter( $entries );

	if ( empty( $entries ) ) {
		return;
	}

	wp_add_inline_script(
		SCRIPT_HANDLE,
		sprintf( 'var lazyLoadScriptsEntries = %s;', json_encode( $entries ) ),
		'before'
	);

	wp_print_scripts( [ SCRIPT_HANDLE ] );
}
