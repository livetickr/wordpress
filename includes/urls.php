<?php
/**
 * The two Livetickr origins, kept deliberately apart.
 *
 * They are not interchangeable, and today they only look like they might be:
 *
 * - The **CDN** serves embed.js. It is the only origin this plugin names, and
 *   it is also — for now — where the reader's browser fetches the feed from,
 *   because embed.js derives the feed URL from its own script src rather than
 *   being told one. That coupling is upstream's, not ours.
 * - The **application** is the API itself. Nothing here calls it yet: the
 *   plugin makes no request of its own, it prints a tag and the browser does
 *   the rest.
 *
 * The second one exists anyway, because the moment something server-side does
 * need the API, reaching for "the base URL" would silently send it to a CDN
 * edge that has no API on it — and a request going to a cache instead of the
 * application is the kind of wrong that looks like it works.
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

/**
 * The origin serving embed.js.
 *
 * @return string Origin without a trailing slash.
 */
function livetickr_cdn_url() {
	/**
	 * Filters the origin the embed loader is served from.
	 *
	 * Whichever host serves embed.js also receives the feed requests it makes,
	 * so this one value moves the entire reader surface — which is what makes
	 * it the right knob for pointing an install at staging, at the application
	 * directly, or at a customer's own CNAME.
	 *
	 * @param string $cdn_url Origin without a trailing slash.
	 */
	$cdn_url = apply_filters( 'livetickr_cdn_url', LIVETICKR_DEFAULT_CDN_URL );

	return untrailingslashit( trim( (string) $cdn_url ) );
}

/**
 * The application's own origin, where the API lives.
 *
 * Reserved: no caller yet. See the file comment for why it is defined before
 * anything needs it.
 *
 * @return string Origin without a trailing slash.
 */
function livetickr_app_url() {
	/**
	 * Filters the origin of the Livetickr application.
	 *
	 * Distinct from the CDN on purpose — a self-hosted or staging install
	 * moves both, but they are not the same host in production.
	 *
	 * @param string $app_url Origin without a trailing slash.
	 */
	$app_url = apply_filters( 'livetickr_app_url', LIVETICKR_DEFAULT_APP_URL );

	return untrailingslashit( trim( (string) $app_url ) );
}
