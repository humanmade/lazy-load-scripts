<table style="width: 100% !important;">
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

This plugin provides easy mechanism to load scripts asynchronously and on demand, right when the element that requires them is in view. It also provides an easy way to preload scripts & styles.

## Async scripts

To load any _registered_ scripts asynchronously, add `async` data to the script entry:

```php
add_action( 'wp_enqueue_scripts', function () {
	// Make sure 'my-script-handle' is already registered first.
	wp_script_add_data( 'my-script-handle', 'async', true );
} );
```

This will add the `async` attribute to the printed script tag.

## Lazy load scripts

Themes or plugins can register their lazy-loaded script via the usual `wp_enqueue_script` or `wp_register_script` with additional `wp_script_add_data` call:

```php
wp_enqueue_script( 'my-script-handle', '//cdn.example.com/script.js', [], false, true );
wp_script_add_data(
	'my-script-handle',
	'lazy',
	[
		// Comma-separated list of element selectors.
		'element_selectors' => '.container',
		// Optional. Uses same unit as CSS margin.
		'offset' => '100px',
		// Optional. When set to true, Add script async attribute tag.
		'script_async' => false,
	]
);
```

> `.container` is the element selector that requires the script.

If the script requires data to function properly, it can be added exported to the page with `wp_add_inline_script`:

```php
wp_add_inline_script( 'my-script-handle', 'var myScriptData = "something";', 'before' );
```

## Lazily invoke scripts

This feature allows some scripts execution to wait for user interaction (ex: keydown, scroll, mousewheel, mousedown, etc.).
For example, a hidden login popup form or dom manipulation inside the hamburger menu, we do not need it to run during page load.

This feature will use one of these events to execute: 'keydown', 'mousemove', 'wheel', 'touchmove', 'touchstart', 'touchend'.
And it only executes once.

```javascript
window.lazyLoadScriptsCallback = window.lazyLoadScriptsCallback || [];
window.lazyLoadScriptsCallback.push( [
	'callback',
	function () {
		// Your function that need to execute.
	},
] );
```

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
