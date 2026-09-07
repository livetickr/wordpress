<?php
/**
 * The plugin's own row on the Plugins screen.
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds a documentation link to the plugin's row meta.
 *
 * Sits beside the version, the author and the plugin site, which WordPress
 * builds from the header. That line is where someone looking at an installed
 * plugin goes for help, so it is worth a link and worth knowing it was used:
 * a click from a live installation says more than one from a repository page.
 *
 * @param string[] $links Row meta links.
 * @param string   $file  Plugin file the row belongs to.
 * @return string[]
 */
function livetickr_plugin_row_meta( $links, $file ) {
	if ( plugin_basename( LIVETICKR_FILE ) !== $file ) {
		return $links;
	}

	// The documentation index, not /docs/embed/wordpress/, which this pointed at
	// and which answers 404: that page is still a draft on the site and drafts
	// are dropped from the production build. A dead link is worse than a general
	// one, and the index routes onward. Point it back at the deep link once the
	// WordPress page is written.
	//
	// Tagged like the readmes, with the surface in utm_medium and this exact
	// placement in utm_content. The label stays `docs_wordpress`: it names the
	// link that was clicked, which is still the docs link in the plugin row, so
	// the numbers stay comparable across this change.
	$url = 'https://livetickr.io/docs/'
		. '?utm_source=wp_plugin&utm_medium=plugins_list'
		. '&utm_campaign=livetickr_wp&utm_content=docs_wordpress';

	$links[] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( $url ),
		esc_html__( 'Documentation', 'livetickr' )
	);

	return $links;
}
add_filter( 'plugin_row_meta', 'livetickr_plugin_row_meta', 10, 2 );
