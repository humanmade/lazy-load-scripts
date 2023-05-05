<?php
/**
 * Plugin Name: Lazy Load Scripts
 * Description: Provides easy mechanism to load scripts asynchronously and on demand.
 * Author: Human Made
 * Author URI: https://humanmade.com
 * Version: 0.1.0
 */

declare( strict_types=1 );

namespace HM\Lazy_Load_Scripts;

require_once __DIR__ . '/inc/async.php';
require_once __DIR__ . '/inc/lazy.php';
require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/preload.php';
require_once __DIR__ . '/inc/Vite/namespace.php';

Async\bootstrap();
Lazy\bootstrap();
Preload\bootstrap();
