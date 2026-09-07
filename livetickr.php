<?php
/**
 * Plugin Name:       Livetickr
 * Plugin URI:        https://livetickr.io/?utm_source=wp_plugin&utm_medium=plugins_list&utm_campaign=livetickr_wp&utm_content=plugin_uri
 * Description:       Embed a Livetickr live ticker with a block or a shortcode. Paste the ticker ID, the feed does the rest.
 * Version:           0.1.1
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Livetickr
 * Author URI:        https://livetickr.io/?utm_source=wp_plugin&utm_medium=plugins_list&utm_campaign=livetickr_wp&utm_content=author_uri
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       livetickr
 * Domain Path:       /languages
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

define( 'LIVETICKR_VERSION', '0.1.1' );
define( 'LIVETICKR_FILE', __FILE__ );
define( 'LIVETICKR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The origin serving embed.js.
 *
 * Whichever host serves the loader also receives the feed requests it makes:
 * embed.js derives the feed URL from its own script src rather than being told
 * one, so this single value moves the whole reader surface.
 *
 * Guarded so a site can set it in wp-config.php, which is the point of having
 * it as a constant at all — the plugin has no settings screen, and one line in
 * wp-config is a good deal less to ask than a filter in a mu-plugin. The
 * `livetickr_cdn_url` filter still applies on top, for anything that needs to
 * decide per request.
 */
if ( ! defined( 'LIVETICKR_CDN_URL' ) ) {
	define( 'LIVETICKR_CDN_URL', 'https://cdn.livetickr.io' );
}

require_once LIVETICKR_PATH . 'vendor/autoload.php';

require_once LIVETICKR_PATH . 'includes/ticker-id.php';
require_once LIVETICKR_PATH . 'includes/compat.php';
require_once LIVETICKR_PATH . 'includes/render.php';
require_once LIVETICKR_PATH . 'includes/shortcode.php';
require_once LIVETICKR_PATH . 'includes/block.php';
require_once LIVETICKR_PATH . 'includes/admin.php';

/**
 * Updates, served from this plugin's own GitHub releases.
 *
 * The plugin is not on wordpress.org, so without this nothing would ever tell a
 * site that a new version exists: core only asks the directory about plugins
 * the directory knows. Plugin Update Checker asks the repository instead and
 * hands the answer to core in the shape it already understands, so an update
 * shows up on the Plugins screen and installs with the same button as any
 * other. Nothing to configure on the site, and no update server of ours to keep
 * running.
 *
 * The third argument is the plugin slug, and it has to be spelled out here
 * because the repository is called `wordpress`, not `livetickr`. Left to
 * default, an update would unpack into a folder named after the repository: the
 * site would end up with the plugin installed twice, and the text domain would
 * stop resolving against `/languages`, taking the German translation with it.
 *
 * Release assets are preferred over GitHub's generated source archive, so an
 * update installs the exact `livetickr.zip` that CI built and that a human
 * downloading it by hand would get. A release that carries no such asset falls
 * back to the source archive, which `.gitattributes` keeps free of the
 * development files.
 */
$livetickr_update_checker = PucFactory::buildUpdateChecker(
	'https://github.com/livetickr/wordpress',
	LIVETICKR_FILE,
	'livetickr'
);

$livetickr_update_checker->getVcsApi()->enableReleaseAssets( '/livetickr\.zip/' );
