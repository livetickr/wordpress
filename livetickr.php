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
 * The origin that serves embed.js.
 *
 * Whichever host serves the loader also serves the feed it fetches: embed.js
 * derives everything else from its own script src. So this one value decides
 * where a reader's browser goes, and it is the only thing that needs changing
 * to point an install at staging or at a self-hosted app. Override it with the
 * `livetickr_base_url` filter rather than editing this file.
 */
define( 'LIVETICKR_DEFAULT_BASE_URL', 'https://cdn.livetickr.io' );

require_once LIVETICKR_PATH . 'includes/ticker-id.php';
require_once LIVETICKR_PATH . 'includes/compat.php';
require_once LIVETICKR_PATH . 'includes/render.php';
require_once LIVETICKR_PATH . 'includes/shortcode.php';
require_once LIVETICKR_PATH . 'includes/block.php';
