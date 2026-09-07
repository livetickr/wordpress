/**
 * The Livetickr block's editor UI.
 *
 * Plain JavaScript against the `wp.*` globals — no JSX, no build step, so the
 * repository is the plugin and a checkout is installable as-is.
 *
 * The editor deliberately shows a card rather than the ticker itself. The
 * block editor renders in a sandboxed frame, which reports no origin, and the
 * feed authorises embeds by origin — so a live preview here is not something
 * the plugin is choosing to skip, it is something the frame cannot have.
 */
( function ( blocks, element, blockEditor, components, i18n ) {
	'use strict';

	var el = element.createElement;
	var Fragment = element.Fragment;
	var useState = element.useState;
	var __ = i18n.__;
	var sprintf = i18n.sprintf;

	/**
	 * Reads a ticker ID out of user input.
	 *
	 * Mirrors livetickr_ticker_id_from_input() in includes/ticker-id.php —
	 * keep the two in step. The server stays the authority; this exists so a
	 * pasted snippet resolves as the editor types instead of after publishing.
	 *
	 * @param {string} input Raw input.
	 * @return {string|null} The ticker ID, or null.
	 */
	function readTickerId( input ) {
		var value = String( input || '' ).trim();
		var match;

		if ( /^[A-Za-z0-9]{12}$/.test( value ) ) {
			return value;
		}

		match = value.match( /data-ticker-id\s*=\s*("|')([A-Za-z0-9]{12})\1/ );

		if ( match ) {
			return match[ 2 ];
		}

		match = value.match( /\/embed\/tickers\/([A-Za-z0-9]{12})/ );

		if ( match ) {
			return match[ 1 ];
		}

		return null;
	}

	/**
	 * Says what was wrong with input that did not resolve.
	 *
	 * @param {string} input Raw input.
	 * @return {string} A message for the editor.
	 */
	function problemWith( input ) {
		var value = String( input || '' ).trim();

		if ( /^\d+$/.test( value ) || /\/tickers\/\d+/.test( value ) ) {
			return __(
				'That is the internal ID from the dashboard URL, which cannot be embedded. The ticker ID is in the ticker’s Embed dialog.',
				'livetickr'
			);
		}

		return __(
			'That does not look like a ticker ID. Expected 12 letters and digits, or the snippet from the ticker’s Embed dialog.',
			'livetickr'
		);
	}

	/**
	 * The ticker ID form, used both in the placeholder and the sidebar.
	 *
	 * @param {Object} props          Component props.
	 * @param {Function} props.onCommit Called with a resolved ticker ID.
	 * @param {string} props.label    Field label.
	 * @param {string} props.submit   Submit button label.
	 * @return {Object} Element.
	 */
	function TickerForm( props ) {
		var draftState = useState( '' );
		var draft = draftState[ 0 ];
		var setDraft = draftState[ 1 ];
		var errorState = useState( '' );
		var error = errorState[ 0 ];
		var setError = errorState[ 1 ];

		function commit() {
			var tickerId = readTickerId( draft );

			if ( ! tickerId ) {
				setError( problemWith( draft ) );
				return;
			}

			setError( '' );
			setDraft( '' );
			props.onCommit( tickerId );
		}

		return el(
			'div',
			{ className: 'livetickr-form' },
			el( components.TextControl, {
				label: props.label,
				value: draft,
				placeholder: __( 'e.g. Ab3xY9kLmN01', 'livetickr' ),
				help: __( 'Paste the ticker ID, or the whole snippet from the ticker’s Embed dialog.', 'livetickr' ),
				onChange: function ( value ) {
					setDraft( value );

					if ( error ) {
						setError( '' );
					}
				},
				onKeyDown: function ( event ) {
					if ( 'Enter' === event.key ) {
						event.preventDefault();
						commit();
					}
				},
				__next40pxDefaultSize: true,
				__nextHasNoMarginBottom: true,
			} ),
			error
				? el( components.Notice, { status: 'error', isDismissible: false }, error )
				: null,
			el(
				components.Button,
				{
					variant: 'primary',
					disabled: '' === draft.trim(),
					onClick: commit,
					__next40pxDefaultSize: true,
				},
				props.submit
			)
		);
	}

	blocks.registerBlockType( 'livetickr/ticker', {
		edit: function ( props ) {
			var tickerId = props.attributes.tickerId;
			var blockProps = blockEditor.useBlockProps();

			function setTickerId( value ) {
				props.setAttributes( { tickerId: value } );
			}

			var sidebar = el(
				blockEditor.InspectorControls,
				null,
				el(
					components.PanelBody,
					{ title: __( 'Ticker', 'livetickr' ) },
					tickerId
						? el(
								'p',
								{ className: 'livetickr-sidebar-current' },
								el( 'code', null, tickerId )
						  )
						: null,
					el( TickerForm, {
						onCommit: setTickerId,
						label: tickerId
							? __( 'Replace with', 'livetickr' )
							: __( 'Ticker ID', 'livetickr' ),
						submit: tickerId
							? __( 'Replace ticker', 'livetickr' )
							: __( 'Add ticker', 'livetickr' ),
					} )
				)
			);

			if ( ! tickerId ) {
				return el(
					Fragment,
					null,
					sidebar,
					el(
						'div',
						blockProps,
						el(
							components.Placeholder,
							{
								icon: 'rss',
								label: __( 'Livetickr', 'livetickr' ),
								instructions: __(
									'Which ticker should appear here?',
									'livetickr'
								),
							},
							el( TickerForm, {
								onCommit: setTickerId,
								label: __( 'Ticker ID', 'livetickr' ),
								submit: __( 'Add ticker', 'livetickr' ),
							} )
						)
					)
				);
			}

			return el(
				Fragment,
				null,
				sidebar,
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'livetickr-card' },
						el(
							'span',
							{ className: 'livetickr-card__icon' },
							el( components.Icon, { icon: 'rss' } )
						),
						el(
							'span',
							{ className: 'livetickr-card__text' },
							el(
								'strong',
								null,
								__( 'Livetickr', 'livetickr' )
							),
							el(
								'span',
								{ className: 'livetickr-card__id' },
								sprintf(
									/* translators: %s: the ticker's ID. */
									__( 'Ticker %s', 'livetickr' ),
									tickerId
								)
							),
							el(
								'span',
								{ className: 'livetickr-card__hint' },
								__(
									'The live ticker appears here on the published page.',
									'livetickr'
								)
							)
						),
						el(
							components.Button,
							{
								variant: 'secondary',
								size: 'small',
								onClick: function () {
									setTickerId( '' );
								},
							},
							__( 'Change', 'livetickr' )
						)
					)
				)
			);
		},

		// Dynamic block: the loader tag is built by PHP at render time.
		save: function () {
			return null;
		},

		transforms: {
			from: [
				{
					type: 'shortcode',
					tag: 'livetickr',
					attributes: {
						tickerId: {
							type: 'string',
							shortcode: function ( attributes ) {
								var named = attributes.named || {};
								var numeric = attributes.numeric || [];

								return (
									readTickerId(
										named.id ||
											named.ticker ||
											named.key ||
											numeric[ 0 ] ||
											''
									) || ''
								);
							},
						},
					},
				},
				{
					// Tickers already pasted into a Custom HTML block convert
					// with one click instead of being re-entered.
					type: 'block',
					blocks: [ 'core/html' ],
					isMatch: function ( attributes ) {
						return null !== readTickerId( attributes.content );
					},
					transform: function ( attributes ) {
						return blocks.createBlock( 'livetickr/ticker', {
							tickerId: readTickerId( attributes.content ),
						} );
					},
				},
			],
		},
	} );
} )(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.i18n
);
