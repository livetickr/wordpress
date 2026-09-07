=== Livetickr ===
Contributors: livetickr
Tags: live ticker, liveblog, live blog, embed, block
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed a Livetickr live ticker in a post or page with a block or a shortcode. Paste the ticker ID, the feed does the rest.

== Description ==

Livetickr is a hosted live ticker service for newsrooms, sports portals and event organisers. This plugin puts one of your tickers into a post or page without touching HTML.

Add the **Livetickr** block, paste the ticker ID, done. The ticker updates itself on the published page. No reloads, nothing to schedule.

= What it does =

* A **Livetickr block** with a single field for the ticker ID.
* A **`[livetickr]` shortcode** for the classic editor, page builders and template files.
* Accepts the whole snippet from the ticker's Embed dialog, not just the bare ID, so you can paste straight from the clipboard.
* Converts an existing Custom HTML block containing a ticker into a Livetickr block.
* Asks the common caching and optimisation plugins to leave the ticker's script alone, because moving it breaks where the ticker renders.
* English and German.

= What it needs =

An account at [livetickr.io](https://livetickr.io/) with at least one published ticker, and the domain of this site registered in your Livetickr workspace. Tickers are only served on registered domains. Until yours is registered, the ticker will stay empty on the page.

= External services =

This plugin loads a script from the Livetickr content delivery network (`https://cdn.livetickr.io`) on any page that contains a ticker. That script then requests the ticker's contents from the same host and refreshes them periodically for as long as the page is open.

The requests transmit the visitor's IP address, browser user agent and the address of the page the ticker is on, which is how Livetickr authorises the embed and counts readers. No data is sent for pages without a ticker.

Service provided by Livetickr: [privacy policy](https://livetickr.io/privacy-policy/) · [legal disclosure](https://livetickr.io/legal-disclosure/).

== Installation ==

1. Upload the plugin to `/wp-content/plugins/livetickr` or install the ZIP under *Plugins > Add New*.
2. Activate it under *Plugins*.
3. In your Livetickr workspace, register this site's domain under *Workspace settings*.
4. Add the **Livetickr** block to a post and paste a ticker ID.

== Frequently Asked Questions ==

= Where do I find the ticker ID? =

Open the ticker in your Livetickr dashboard and choose *Embed*. The ID is the twelve-character code in the snippet, and you can paste the whole snippet into the block: the plugin reads the ID out of it.

The number in the dashboard's own address bar is an internal ID and cannot be embedded.

= The block is there but the page stays empty. =

Almost always the site's domain is not registered in the Livetickr workspace yet. Tickers are only served on domains you have registered. A ticker still in draft is served to nobody either, so publish it first.

= Can I put more than one ticker on a page? =

Yes. Each block or shortcode renders its own ticker, where you placed it.

= Does the ticker work with a caching plugin? =

Yes. The plugin marks its script so that the common optimisation plugins do not move, combine or delay it. That placement is what tells the ticker where on the page it belongs.

= Can I change how the ticker looks? =

The ticker's appearance comes from Livetickr, not from this plugin, so it stays consistent everywhere you embed it. Colours, feed style and branding are set under *Appearance* in your Livetickr workspace.

= Which languages are included? =

English and German. The German translation is the `de_DE` locale, which addresses you informally. Sites set to another German locale, such as `de_DE_formal` or `de_AT`, see the English original: WordPress does not fall back from one German locale to another for plugin translations.

= Can I point the plugin at a different Livetickr host? =

Yes, with a constant in `wp-config.php`:

`define( 'LIVETICKR_CDN_URL', 'https://cdn.example.com' );`

That is the host serving the loader script, which is also the host it fetches the ticker from. A `livetickr_cdn_url` filter is available too, for deciding it per request.

== Changelog ==

= 0.1.0 =
* First release: Livetickr block, `[livetickr]` shortcode, and compatibility handling for caching and optimisation plugins.
