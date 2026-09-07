<?php
/**
 * Keeping optimisation plugins away from the loader.
 *
 * embed.js finds itself with `document.currentScript` and inserts its feed
 * container as its own next sibling. That makes the tag's position in the DOM
 * functional, not cosmetic: a plugin that moves the script to the footer,
 * concatenates it into a bundle or inlines it does not slow the ticker down,
 * it stops it from rendering in the right place — or at all.
 *
 * Two layers, because the plugins disagree on which they honour: attributes on
 * the tag itself, and filters for the ones that only read their own option
 * lists. Every filter here is additive and inert when its plugin is absent.
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attributes that ask optimisation plugins to leave the loader alone.
 *
 * @return array<string, string|bool> Attribute name => value, or true for a valueless attribute.
 */
function livetickr_optimizer_attributes() {
	$attributes = array(
		'data-cfasync'     => 'false', // Cloudflare Rocket Loader.
		'data-no-optimize' => '1',     // Autoptimize.
		'data-no-defer'    => '1',     // Autoptimize, LiteSpeed Cache.
		'data-no-minify'   => '1',     // LiteSpeed Cache, SG Optimizer.
		'data-nowprocket'  => true,    // WP Rocket.
	);

	/**
	 * Filters the optimiser opt-out attributes put on the loader tag.
	 *
	 * @param array<string, string|bool> $attributes Attribute name => value.
	 */
	return apply_filters( 'livetickr_optimizer_attributes', $attributes );
}

/**
 * The substring that identifies the loader in an exclusion list.
 *
 * Deliberately the filename rather than the full URL: the base URL is
 * filterable, and every one of these plugins matches on a substring.
 *
 * @return string
 */
function livetickr_excluded_path() {
	return 'embed.js';
}

/**
 * Adds the loader to an array-shaped exclusion list.
 *
 * @param mixed $excluded Existing exclusions, expected to be an array.
 * @return array
 */
function livetickr_exclude_from_array( $excluded ) {
	$excluded   = is_array( $excluded ) ? $excluded : array();
	$excluded[] = livetickr_excluded_path();

	return $excluded;
}

/**
 * Adds the loader to a comma-separated exclusion list.
 *
 * @param mixed $excluded Existing exclusions, expected to be a string.
 * @return string
 */
function livetickr_exclude_from_string( $excluded ) {
	$excluded = is_string( $excluded ) ? trim( $excluded ) : '';

	if ( '' === $excluded ) {
		return livetickr_excluded_path();
	}

	return $excluded . ',' . livetickr_excluded_path();
}

// WP Rocket: minification of external files, and delayed JavaScript.
add_filter( 'rocket_exclude_js', 'livetickr_exclude_from_array' );
add_filter( 'rocket_delay_js_exclusions', 'livetickr_exclude_from_array' );
add_filter( 'rocket_exclude_defer_js', 'livetickr_exclude_from_array' );

// Autoptimize takes one comma-separated string.
add_filter( 'autoptimize_filter_js_exclude', 'livetickr_exclude_from_string' );

// LiteSpeed Cache.
add_filter( 'litespeed_optm_js_defer_exc', 'livetickr_exclude_from_array' );
add_filter( 'litespeed_optm_gm_js_exc', 'livetickr_exclude_from_array' );

// SG Optimizer.
add_filter( 'sgo_javascript_combine_exclude', 'livetickr_exclude_from_array' );
add_filter( 'sgo_js_minify_exclude', 'livetickr_exclude_from_array' );

// Perfmatters.
add_filter( 'perfmatters_delay_js_exclusions', 'livetickr_exclude_from_array' );
