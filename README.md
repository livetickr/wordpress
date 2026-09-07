# Livetickr for WordPress

Embed a [Livetickr](https://livetickr.com/) live ticker in a post or page with a block or a
shortcode. Paste the ticker ID, the feed does the rest.

## What it does

Livetickr serves a ticker as a small loader script that fetches the feed and keeps it
refreshed. Pasting that snippet into a Custom HTML block works, but the editor never shows
the ticker, and caching plugins are free to move the script somewhere it no longer functions.

This plugin wraps the same snippet in something an editor can actually use:

- a **Livetickr block** with one field for the ticker ID,
- a **`[livetickr]` shortcode** for the classic editor, page builders and templates,
- ID input that accepts the whole snippet from the ticker's Embed dialog, not just the bare ID,
- a one-click conversion for tickers already sitting in a Custom HTML block,
- opt-out markers and filters so caching and optimisation plugins leave the loader alone.

## Requirements

- WordPress 6.3 or newer, PHP 7.4 or newer
- A Livetickr account with a published ticker
- **This site's domain registered in your Livetickr workspace.** Tickers are only served on
  registered domains — until yours is registered the ticker stays empty on the page. This is
  the single most common reason an embed looks broken.

## Usage

### Block

Add the **Livetickr** block and paste the ticker ID, or the whole snippet from the ticker's
Embed dialog.

The editor shows a card rather than the ticker itself. That is not a shortcut: the block
editor renders inside a sandboxed frame, which reports no origin, and Livetickr authorises
embeds by origin — so the frame cannot show a live feed at all. The ticker appears on the
published page.

### Shortcode

```
[livetickr id="Ab3xY9kLmN01"]
```

`ticker=` and `key=` work as aliases, and the ID may be passed positionally
(`[livetickr Ab3xY9kLmN01]`).

### In a template

```php
echo do_shortcode( '[livetickr id="Ab3xY9kLmN01"]' );
```

## Filters

### `livetickr_cdn_url`

The origin serving `embed.js`. Defaults to `https://cdn.livetickr.io`.

Whichever host serves the loader also receives the feed requests it makes — `embed.js` derives
the feed URL from its own script `src` — so this one value moves the whole reader surface.
That makes it the knob for pointing an install at staging, at the application directly, or at
a customer's own CNAME.

```php
add_filter( 'livetickr_cdn_url', function () {
    return 'https://cdn.example.com';
} );
```

### `livetickr_app_url`

The origin of the Livetickr application, where the API lives. Defaults to
`https://api.livetickr.io`.

Nothing calls it yet — the plugin makes no request of its own, it prints a tag and the browser
does the rest. It is separate from the CDN on purpose: when something server-side does need
the API, reaching for "the base URL" would send it to a CDN edge that has no API on it, and a
request landing in a cache instead of the application is the kind of wrong that looks like it
works.

### `livetickr_script_attributes`

The loader tag's attributes, as an `name => value` map. `true` renders a valueless attribute.

```php
add_filter( 'livetickr_script_attributes', function ( $attributes, $ticker_id ) {
    $attributes['data-example'] = '1';
    return $attributes;
}, 10, 2 );
```

### `livetickr_optimizer_attributes`

Just the opt-out markers aimed at caching and optimisation plugins, should you need to drop
or extend them.

## Caching and optimisation plugins

`embed.js` locates itself with `document.currentScript` and inserts the feed as its own next
sibling. The tag's position in the document is therefore functional, not cosmetic: a plugin
that moves it to the footer, bundles it or inlines it does not slow the ticker down, it stops
it rendering in the right place.

The plugin marks the tag for Cloudflare Rocket Loader, WP Rocket, Autoptimize, LiteSpeed
Cache, SG Optimizer and Perfmatters, and registers each one's exclusion filter. If another
optimiser interferes, exclude `embed.js` from its JavaScript handling.

Page caches are fine: the loader tag is static and the feed is fetched by the browser.

## Development

The plugin has no build step — `index.js` runs against the `wp.*` globals — so a checkout is
installable as-is.

```bash
# Syntax
find . -name '*.php' -print0 | xargs -0 -n1 php -l

# Regenerate the translation template
wp i18n make-pot . languages/livetickr.pot --slug=livetickr --domain=livetickr
```

`.distignore` lists what is development-only. CI checks the built plugin rather than the
repository, because the repository legitimately holds files a distributed plugin must not.

Run [WordPress Plugin Check](https://github.com/WordPress/plugin-check) the same way CI does:

```bash
mkdir -p build/livetickr
tar --exclude-from=.distignore -cf - . | tar -C build/livetickr -xf -
wp plugin check build/livetickr --include-experimental
```

## License

GPL-2.0-or-later
