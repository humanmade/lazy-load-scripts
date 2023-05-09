<table width="100%" style="width: 100% !important;">
	<tr>
		<td align="left" width="70">
			<strong>Lazy Load Scripts</strong><br />
			Provides easy mechanism to load scripts asynchronously and on demand.
		</td>
		<td align="right" width="20%">
		</td>
	</tr>
	<tr>
		<td>
			A <strong><a href="https://hmn.md/">Human Made</a></strong> project. Maintained by @ivankristianto.
		</td>
		<td align="center">
			<img src="https://hmn.md/content/themes/hmnmd/assets/images/hm-logo.svg" width="100" />
		</td>
	</tr>
</table>

# Lazy Load Scripts

This plugin offers a straightforward method for loading scripts asynchronously and as needed, specifically when the relevant element comes into view. Additionally, it simplifies the process of preloading scripts and stylesheets.

Here are some scenarios where this plugin can be beneficial:

1. Lazily loading ad scripts placed below the fold.
2. Delaying the initialization of scripts for hidden elements, like hamburger menus.
3. Ensuring scripts load asynchronously or with defer attributes to avoid blocking rendering.
4. Preloading critical asset files like critical css or fonts.

## Lazy load scripts

Themes or plugins can register their lazy-loaded script via the usual `wp_enqueue_script` or `wp_register_script` with additional `wp_script_add_data` call:

```php
wp_enqueue_script( 'my-script-handle', '//cdn.example.com/script.js', [], false, true );
wp_script_add_data(
	'my-script-handle',
	'lazy',
	[
		// Element selector that trigger script to load when it shows in the viewport.
		'element_selectors' => '.container',

		// Or comma-separated list of element selectors.
		// 'element_selectors' => '.container,.site-header',

		// Optional. Uses same unit as CSS margin.
		'offset' => '100px',
		// Optional. When set to true, Add script async attribute tag.
		'script_async' => false,
	]
);
```

The element selector `.container` initiates the script when it appears within the viewport. If multiple element selectors are present, add them as a comma-separated list. The script will be triggered only once, when the first element selector is detected.

If the script requires data to function properly, it can be added exported to the page with `wp_add_inline_script`:

```php
wp_add_inline_script( 'my-script-handle', 'var myScriptData = "something";', 'before' );
```

## Async/Defer scripts

To load any _registered_ scripts asynchronously, add `async` data to the script entry:

```php
add_action( 'wp_enqueue_scripts', function () {
	// Make sure 'my-script-handle' is already registered first.
	wp_script_add_data( 'my-script-handle', 'async', true );
	wp_script_add_data( 'my-other-script-handle', 'defer', true );
} );
```

This will add the `async`/`defer` attribute to the printed script tag.

## Preload scripts and styles.

To preload any _registered_ scripts and styles, add `preload` data to the asset:

```php
add_action( 'wp_enqueue_scripts', function () {
	// Make sure 'my-script-handle' and 'my-style-handle' are already registered first.
	wp_script_add_data( 'my-script-handle', 'preload', true );
	wp_style_add_data( 'my-style-handle', 'preload', true );
	wp_style_add_data( 'wp-block-library', 'preload', true );
} );
```

This will add [preload link](https://developer.mozilla.org/en-US/docs/Web/HTML/Link_types/preload) for each script/style to the `<head/>`.

## Lazily invoke scripts

This functionality enables specific script execution to pause for user input, such as key presses, scrolling, mouse wheel movements, or mouse clicks. For instance, a concealed login popup or DOM alterations within a hamburger menu can be delayed until needed, rather than running on page load.

Utilizing one of the following events, the feature will execute: 'keydown', 'mousemove', 'wheel', 'touchmove', 'touchstart', 'touchend'. Execution occurs only once.

```javascript
window.lazyLoadScriptsCallback = window.lazyLoadScriptsCallback || [];
window.lazyLoadScriptsCallback.push( [
	'callback',
	function () {
		// Your function that need to execute.
		// init_hamburger_menu();
	},
] );
```
