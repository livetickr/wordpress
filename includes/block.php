<?php
/**
 * The Livetickr block.
 *
 * @package Livetickr
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block, its editor assets and its render callback.
 *
 * The editor script is registered by hand rather than through block.json's
 * `file:` shorthand: without a generated asset file WordPress cannot know that
 * index.js needs wp-blocks, wp-element and the rest, and would enqueue it with
 * no dependencies at all. In the post editor that happens to work — those
 * scripts are already there — but in the site and widget editors the block
 * would silently fail to register. Declaring the dependencies is what makes
 * skipping a build step safe.
 *
 * The block is dynamic on purpose. It stores only the ticker ID and the loader
 * tag is built at render time; a static block would have to save a <script>
 * tag into post content, which WordPress strips for any author without the
 * `unfiltered_html` capability — the ticker would vanish for exactly the
 * editors most likely to be adding one.
 *
 * @return void
 */
function livetickr_register_block() {
	wp_register_script(
		'livetickr-ticker-editor',
		plugins_url( 'blocks/livetickr/index.js', LIVETICKR_FILE ),
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		LIVETICKR_VERSION,
		array( 'in_footer' => true )
	);

	wp_set_script_translations( 'livetickr-ticker-editor', 'livetickr', LIVETICKR_PATH . 'languages' );

	wp_register_style(
		'livetickr-ticker-editor-style',
		plugins_url( 'blocks/livetickr/editor.css', LIVETICKR_FILE ),
		array(),
		LIVETICKR_VERSION
	);

	register_block_type(
		LIVETICKR_PATH . 'blocks/livetickr',
		array( 'render_callback' => 'livetickr_render_block' )
	);
}
add_action( 'init', 'livetickr_register_block' );

/**
 * Renders the block on the front end.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function livetickr_render_block( $attributes ) {
	$ticker_id = isset( $attributes['tickerId'] ) ? $attributes['tickerId'] : '';

	return livetickr_render(
		$ticker_id,
		get_block_wrapper_attributes( array( 'class' => 'livetickr' ) )
	);
}
