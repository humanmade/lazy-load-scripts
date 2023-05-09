<?php
/**
 * Test Preload
 */

 use HM\Lazy_Load_Scripts\Preload;

/**
 * Test Preload.
 *
 * @package LLS
 */
class Test_Preload extends WP_UnitTestCase {

	public function test_print_head_preload_links_style() {
		$wp_version = get_bloginfo( 'version' );
		$style_src = 'https://example.com/style.css';

		wp_enqueue_style( 'should-have-preload-link', $style_src, [], $wp_version );
		wp_style_add_data( 'should-have-preload-link', 'preload', true );

		ob_start();
		Preload\print_head_preload_links();

		$content = ob_get_contents();
		ob_end_clean();

		$this->assertContains( '<link rel="preload" href="' . $style_src . '" as="style" />', $content );

		wp_dequeue_style( 'should-have-preload-link' );
	}

	public function test_print_head_preload_links_script() {
		$wp_version = get_bloginfo( 'version' );
		$script_src = 'https://example.com/script.js';

		wp_enqueue_script( 'should-have-preload-js', $script_src, [], $wp_version );
		wp_script_add_data( 'should-have-preload-js', 'preload', true );

		ob_start();
		Preload\print_head_preload_js();

		$content = ob_get_contents();
		ob_end_clean();

		$this->assertContains( '<link rel="preload" href="' . $script_src . '" as="script" />', $content );

		wp_dequeue_script( 'should-have-preload-js' );
	}

}
