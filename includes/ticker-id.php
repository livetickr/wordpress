<?php
/**
 * Turning whatever someone pasted into a ticker ID.
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reads a ticker ID out of user input.
 *
 * A ticker's public ID is 12 case-sensitive alphanumerics. Rather than insist
 * on those 12 characters, this accepts every shape the ID plausibly arrives in
 * — most importantly the whole embed snippet, because that is what the copy
 * button in the ticker's Embed dialog puts on the clipboard.
 *
 * Kept free of WordPress dependencies apart from WP_Error so it stays a plain,
 * testable function.
 *
 * @param string $input Raw user input.
 * @return string|WP_Error The ticker ID, or an error explaining what was wrong.
 */
function livetickr_ticker_id_from_input( $input ) {
	$input = trim( (string) $input );

	if ( '' === $input ) {
		return new WP_Error( 'livetickr_empty', __( 'No ticker ID set yet.', 'livetickr' ) );
	}

	// The ID on its own.
	if ( preg_match( '/^[A-Za-z0-9]{12}$/', $input ) ) {
		return $input;
	}

	// The whole snippet from the Embed dialog — the common case.
	if ( preg_match( '/data-ticker-id\s*=\s*(["\'])([A-Za-z0-9]{12})\1/', $input, $matches ) ) {
		return $matches[2];
	}

	// A feed URL, e.g. copied out of the address bar after opening it.
	if ( preg_match( '#/embed/tickers/([A-Za-z0-9]{12})#', $input, $matches ) ) {
		return $matches[1];
	}

	// The dashboard's own ticker URL is keyed by the internal row ID, not the
	// public one, so it can never be turned into an embed. Worth its own
	// message: of all the wrong things to paste, this is the one that looks
	// most like it should have worked.
	if ( preg_match( '/^\d+$/', $input ) || preg_match( '#/tickers/\d+#', $input ) ) {
		return new WP_Error(
			'livetickr_internal_id',
			__( 'That is the internal ID from the dashboard URL, which cannot be embedded. The ticker ID is in the ticker’s Embed dialog.', 'livetickr' )
		);
	}

	return new WP_Error(
		'livetickr_invalid',
		__( 'That does not look like a ticker ID. Expected 12 letters and digits, or the snippet from the ticker’s Embed dialog.', 'livetickr' )
	);
}
