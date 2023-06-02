<?php
/**
 * Test Utils
 */

/**
 * Test Utils.
 *
 * @package LLS
 */
class Test_Utils extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();

		$wp_version = get_bloginfo( 'version' );
		$style_src = 'https://example.com/style.css';
		wp_enqueue_style( 'should-have-preload-link', $style_src, [], $wp_version );
		wp_style_add_data( 'should-have-preload-link', 'preload', true );

		wp_enqueue_style( 'should-not-have-preload-link', $style_src, [], $wp_version );

		$script_src = 'https://example.com/script.js';
		wp_enqueue_script( 'should-have-preload-js', $script_src, [], $wp_version );
		wp_script_add_data( 'should-have-preload-js', 'preload', true );
	}

	public function test_collect_entries() {
		$entries = HM\Lazy_Load_Scripts\collect_entries( 'styles', 'preload', '__return_true' );

		$this->assertIsArray( $entries );
		$this->assertArrayHasKey( 'should-have-preload-link', $entries );
		$this->assertArrayNotHasKey( 'should-not-have-preload-link', $entries );

		$entries = HM\Lazy_Load_Scripts\collect_entries( 'script', 'preload', '__return_true' );
		$this->assertIsArray( $entries );
		$this->assertArrayHasKey( 'should-have-preload-js', $entries );
	}

	public function test_prepare_asset_url() {
		$dir_path = '/wp-content/themes/sample/';

		$asset_url = HM\Lazy_Load_Scripts\prepare_asset_url( $dir_path );
		$this->assertEquals( home_url( $dir_path ), $asset_url );

	}
}
