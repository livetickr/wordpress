<?php
/**
 * Plugin Name:       Livetickr
 * Plugin URI:        https://livetickr.com
 * Description:       Embed a Livetickr live ticker with a block or a shortcode. Paste the ticker ID, the feed does the rest.
 * Version:           0.1.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Livetickr
 * Author URI:        https://livetickr.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       livetickr
 * Domain Path:       /languages
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

define( 'LIVETICKR_VERSION', '0.1.0' );
define( 'LIVETICKR_FILE', __FILE__ );
define( 'LIVETICKR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The origin serving embed.js.
 *
 * Whichever host serves the loader also receives the feed requests it makes:
 * embed.js derives the feed URL from its own script src rather than being told
 * one, so this single value moves the whole reader surface. Override it with
 * the `livetickr_cdn_url` filter rather than editing this file.
 */
define( 'LIVETICKR_DEFAULT_CDN_URL', 'https://cdn.livetickr.io' );

require_once LIVETICKR_PATH . 'includes/ticker-id.php';
require_once LIVETICKR_PATH . 'includes/compat.php';
require_once LIVETICKR_PATH . 'includes/render.php';
require_once LIVETICKR_PATH . 'includes/shortcode.php';
require_once LIVETICKR_PATH . 'includes/block.php';
