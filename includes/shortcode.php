<?php
/**
 * The [livetickr] shortcode.
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders [livetickr].
 *
 * Accepts the ID under whichever attribute name someone reaches for, and
 * positionally as [livetickr Ab3xY9kLmN01], because being strict about the
 * attribute name only produces support questions.
 *
 * @param array|string $atts Shortcode attributes.
 * @return string
 */
function livetickr_shortcode( $atts ) {
	$atts       = (array) $atts;
	$positional = isset( $atts[0] ) ? $atts[0] : '';

	$atts = shortcode_atts(
		array(
			'id'     => '',
			'ticker' => '',
			'key'    => '',
		),
		$atts,
		'livetickr'
	);

	$candidates = array( $atts['id'], $atts['ticker'], $atts['key'], $positional );
	$input      = '';

	foreach ( $candidates as $candidate ) {
		if ( '' !== trim( (string) $candidate ) ) {
			$input = $candidate;
			break;
		}
	}

	return livetickr_render( $input );
}
add_shortcode( 'livetickr', 'livetickr_shortcode' );
