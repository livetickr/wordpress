<?php
/**
 * Plugin Name:       Livetickr
 * Plugin URI:        https://livetickr.io/?utm_source=wp_plugin&utm_medium=plugins_list&utm_campaign=livetickr_wp&utm_content=plugin_uri
 * Description:       Embed a Livetickr live ticker with a block or a shortcode. Paste the ticker ID, the feed does the rest.
 * Version:           0.1.2
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

define( 'LIVETICKR_VERSION', '0.1.2' );
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

/**
 * The plugin's own artwork on the update screens.
 *
 * WordPress does not go looking in the plugin folder for either of these.
 * Dashboard -> Updates reads `icons` off the update object in the
 * update_plugins transient, and the "View version details" modal reads
 * `banners` off the plugins_api response. So whatever tells WordPress that a
 * new version exists is also what has to hand over the artwork, and here that
 * is the update checker. Without this the update row shows the generic grey
 * plug and the modal has no header at all.
 *
 * Both URLs point into the installed plugin rather than at our CDN: no admin
 * page reaches out to us to draw them, nothing to log, and a site behind a
 * firewall still gets them.
 *
 * The icon is a single SVG, which covers every screen density on its own: core
 * prefers the `svg` key over `2x` and `1x`. The banner is a designed graphic
 * and comes as the two sizes wordpress.org defines, 772x250 and 1544x500, which
 * core maps to `low` and `high` and swaps at a device pixel ratio of 1.5.
 *
 * Before replacing a banner, know how core draws over it. It paints the file as
 * a CSS background with `background-size: cover` from the top left, so any crop
 * happens on the right and at the bottom. Then it writes the plugin name across
 * it: white, 30px, in a nearly opaque dark pill about 170px wide, occupying
 * y 174 to 224 of the 772x250 file (y 348 to 448 at double size). Anything drawn
 * in that band is covered. The current pair keeps all content between y 30 and
 * y 160, and within x 44 to 476, so it clears the pill and survives a narrow
 * modal. Measured, not assumed.
 */
$livetickr_update_checker->addFilter(
	'pre_inject_update',
	static function ( $update ) {
		if ( is_object( $update ) ) {
			$update->icons = array(
				'svg' => plugins_url( 'assets/icon.svg', LIVETICKR_FILE ),
			);
		}

		return $update;
	}
);

$livetickr_update_checker->addFilter(
	'pre_inject_info',
	static function ( $info ) {
		// Guarded because this can be false or null: the caller only checks for
		// that after the filter has run.
		if ( is_object( $info ) ) {
			$info->banners = array(
				'low'  => plugins_url( 'assets/banner-772x250.png', LIVETICKR_FILE ),
				'high' => plugins_url( 'assets/banner-1544x500.png', LIVETICKR_FILE ),
			);
		}

		return $info;
	}
);
