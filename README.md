# Livetickr for WordPress

Embed a [Livetickr](https://livetickr.io/?utm_source=wp_plugin&utm_medium=readme.md&utm_campaign=livetickr_wp&utm_content=intro) live ticker in a post or page with a block or a
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
  registered domains. Until yours is registered the ticker stays empty on the page. This is
  the single most common reason an embed looks broken.

## Usage

### Block

Add the **Livetickr** block and paste the ticker ID, or the whole snippet from the ticker's
Embed dialog.

The editor shows a card rather than the ticker itself. That is not a shortcut: the block
editor renders inside a sandboxed frame, which reports no origin, and Livetickr authorises
embeds by origin, so the frame cannot show a live feed at all. The ticker appears on the
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

## Caching and optimisation plugins

`embed.js` locates itself with `document.currentScript` and inserts the feed as its own next
sibling. The tag's position in the document is therefore functional, not cosmetic: a plugin
that moves it to the footer, bundles it or inlines it does not slow the ticker down, it stops
it rendering in the right place.

The plugin marks the tag for Cloudflare Rocket Loader, WP Rocket, Autoptimize, LiteSpeed
Cache, SG Optimizer and Perfmatters, and registers each one's exclusion filter. If another
optimiser interferes, exclude `embed.js` from its JavaScript handling.

Page caches are fine: the loader tag is static and the feed is fetched by the browser.

## License

GPL-2.0-or-later
