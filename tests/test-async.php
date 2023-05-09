<?php
/**
 * Async Script Test
 */

use HM\Lazy_Load_Scripts\Async;

/**
 * Test Async Script.
 *
 * @package LLS
 */
class Test_Async_Script extends WP_UnitTestCase {

	/**
	 * Test Default, no async attribute
	 *
	 * @return void
	 */
	public function test_default() {
		$script_src = 'https://example.com/script.js';

		wp_register_script( 'should-not-have-async-attr', $script_src );

		$tag = sprintf(
			"<script type='text/javascript' src='%s' id='%s-js'></script>\n",
			$this->type_attr,
			$script_src,
			'should-not-have-async-attr'
		);

		$tag = Async\add_async_or_defer_attribute( $tag, 'should-not-have-async-attr' );

		$this->assertNotContains( 'async', $tag );
		$this->assertNotContains( 'defer', $tag );
	}

	/**
	 * Test add Async Atribute
	 *
	 * @return void
	 */
	public function test_add_async_attribute() {
		$script_src = 'https://example.com/script.js';

		wp_register_script( 'should-have-async-attr', $script_src );
		wp_script_add_data( 'should-have-async-attr', 'async', true );

		$tag = sprintf(
			"<script type='text/javascript' src='%s' id='%s-js'></script>\n",
			$this->type_attr,
			$script_src,
			'should-have-async-attr'
		);

		$tag = Async\add_async_or_defer_attribute( $tag, 'should-have-async-attr' );

		$this->assertContains( 'async', $tag );
	}

	/**
	 * Test add Defer Atribute
	 */
	public function test_add_defer_attribute() {
		$script_src = 'https://example.com/script.js';

		wp_register_script( 'should-have-defer-attr', $script_src );
		wp_script_add_data( 'should-have-defer-attr', 'defer', true );

		$tag = sprintf(
			"<script type='text/javascript' src='%s' id='%s-js'></script>\n",
			$this->type_attr,
			$script_src,
			'should-have-defer-attr'
		);

		$tag = Async\add_async_or_defer_attribute( $tag, 'should-have-defer-attr' );

		$this->assertContains( 'defer', $tag );
	}
}
