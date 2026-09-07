<?php
/**
 * Building the loader tag.
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders a ticker: a wrapper with the loader tag inside it.
 *
 * The loader is printed inline instead of enqueued, which is unusual enough to
 * say why: embed.js reads `document.currentScript` and inserts the feed as its
 * own next sibling, so the tag has to sit exactly where the ticker belongs.
 * Enqueueing it would move it to the head or footer and the feed would render
 * there. It is also why the block is dynamic — a script tag saved into post
 * content would be stripped for any author without `unfiltered_html`.
 *
 * @param string      $input              Ticker ID, or anything it can be read from.
 * @param string|null $wrapper_attributes Pre-rendered wrapper attributes. Defaults to a plain class.
 * @return string HTML, or an empty string when there is nothing to show.
 */
function livetickr_render( $input, $wrapper_attributes = null ) {
	$ticker_id = livetickr_ticker_id_from_input( $input );

	if ( is_wp_error( $ticker_id ) ) {
		return livetickr_render_problem( $ticker_id );
	}

	if ( null === $wrapper_attributes ) {
		$wrapper_attributes = 'class="livetickr"';
	}

	return sprintf(
		'<div %1$s data-livetickr-ticker="%2$s">%3$s</div>',
		$wrapper_attributes,
		esc_attr( $ticker_id ),
		livetickr_script_tag( $ticker_id )
	);
}

/**
 * Builds the loader script tag for one ticker.
 *
 * @param string $ticker_id A validated ticker ID.
 * @return string
 */
function livetickr_script_tag( $ticker_id ) {
	static $instances = 0;

	$attributes = array(
		'defer' => true,
		'src'   => livetickr_cdn_url() . '/embed.js',
	);

	// The canonical snippet carries `id="livetickr-feed"`. Nothing reads it
	// today — the script finds itself through document.currentScript — but it
	// is kept upstream as a stable anchor, so the tag we emit matches the one
	// the dashboard hands out. Only on the first ticker though: a second one
	// on the same page would just be a duplicate ID, and pages with a single
	// ticker (nearly all of them) stay identical to the snippet.
	if ( 0 === $instances ) {
		$attributes['id'] = 'livetickr-feed';
	}

	$attributes['data-ticker-id'] = $ticker_id;

	$attributes = array_merge( $attributes, livetickr_optimizer_attributes() );

	/**
	 * Filters the loader tag's attributes.
	 *
	 * @param array<string, string|bool> $attributes Attribute name => value, or true for a valueless attribute.
	 * @param string                     $ticker_id  The ticker being embedded.
	 */
	$attributes = apply_filters( 'livetickr_script_attributes', $attributes, $ticker_id );

	++$instances;

	return '<script' . livetickr_attributes_to_html( $attributes ) . '></script>';
}

/**
 * Renders an attribute map as HTML.
 *
 * @param array<string, string|bool> $attributes Attribute name => value. True renders a valueless attribute, false and null skip it.
 * @return string Leading space included when non-empty.
 */
function livetickr_attributes_to_html( $attributes ) {
	$html = '';

	foreach ( (array) $attributes as $name => $value ) {
		if ( false === $value || null === $value ) {
			continue;
		}

		$name = strtolower( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $name ) );

		if ( '' === $name ) {
			continue;
		}

		if ( true === $value ) {
			$html .= ' ' . $name;
			continue;
		}

		$value = ( 'src' === $name ) ? esc_url( $value ) : esc_attr( $value );
		$html .= ' ' . $name . '="' . $value . '"';
	}

	return $html;
}

/**
 * Explains a bad ticker ID to someone who can fix it.
 *
 * Readers get nothing: a visitor cannot act on "that is not a ticker ID", and
 * an error message in the middle of an article is worse than a gap.
 *
 * @param WP_Error $error The problem with the input.
 * @return string
 */
function livetickr_render_problem( $error ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return '';
	}

	return sprintf(
		'<p class="livetickr-problem"><strong>%1$s</strong> %2$s</p>',
		esc_html__( 'Livetickr:', 'livetickr' ),
		esc_html( $error->get_error_message() )
	);
}
