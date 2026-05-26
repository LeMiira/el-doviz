=== El Doviz ===
Contributors: miiira
Donate link: https://github.com/sponsors/LeMiira
Tags: exchange-rates, tcmb, elementor, gutenberg, widget
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight Turkish exchange rates display with Elementor, Gutenberg, shortcodes, and widget support.

== Description ==
El Doviz is a lightweight, modern, and fully secure WordPress plugin that displays Turkish exchange rates. It works with Gutenberg blocks, Elementor widgets, classic shortcodes, and header/footer widgets. All data sources are free public APIs (TCMB for rates) – no API keys required.

* Responsive mobile‑first design with Persian red accents and premium silver/white theme.
* Accessible UI – ARIA live regions, keyboard navigation, and reduced‑motion support.
* SEO‑friendly – schema.org `FinancialProduct` markup.
* KVKK‑compliant privacy page and disclaimer.
* Multisite compatible, translation ready (EN & TR), and fully PHPCS/WordPress‑Coding‑Standards compliant.

== Shortcodes ==
Use the following shortcodes to display exchange rates and ticker feeds anywhere on your site:
* `[el_doviz_exchange_rates currencies="usd,eur,gbp" layout="list"]` - Displays a list or grid of currency exchange rates.
  * **currencies**: Comma-separated ISO codes (default: `usd,eur,gbp`).
  * **layout**: `list`, `grid` (default: `list`).
  * **theme**: `auto`, `light`, `dark` (default: `auto`).
* `[el_doviz_ticker currencies="usd,eur,gbp" speed="5000"]` - Displays a scrolling marquee header/footer ticker.
  * **currencies**: Comma-separated ISO codes (default: `usd,eur,gbp`).
  * **speed**: Animation duration per item in ms (default: `5000`).
* `[el_doviz_privacy]` - Outputs standard disclosure/privacy text stating that the rates are provided for informational purposes only.

== Gutenberg Blocks & Elementor Widgets ==
The plugin includes native, high-performance editor integrations:
* **Exchange Rates**: Drag and drop block/widget to display selected rates as a list or grid.
* **Live Ticker**: Sliding marquee block/widget showing live exchange rates.

== Custom Styling ==
Colors can be customized in your theme stylesheet using CSS custom properties:
`
:root {
  --el-doviz-primary: #C41E3A; /* Persian Red accent color */
  --el-doviz-bg: #f8f9fa;      /* Silver/white container background */
  --el-doviz-text: #212529;    /* Text color */
}
`

== Developer Filters ==
* `el_doviz_tcmb_url` - Filter the TCMB XML feed URL.
* `el_doviz_enqueue_assets` - Control when assets are enqueued on the frontend.

== Performance & Caching ==
* **Cache Engine**: Utilizes the WordPress Transients API.
* **Cache TTL**: Cached for 1 hour (3600 seconds) to prevent redundant external API hits.
* **Asset Optimization**: CSS and JS files are enqueued conditionally (only when blocks, widgets, or shortcodes are present on the rendered page).

== Accessibility (a11y) ==
* **ARIA Live Regions**: Tickers use `aria-live="polite"` so screen readers process updates gracefully.
* **Motion Control**: Disables animations if `prefers-reduced-motion: reduce` is enabled at the OS/browser level.
* **Keyboard Navigation**: Native tab indices and focus states are respected in all layouts.

== Installation ==
1. Upload the `el-doviz` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Configure settings under **El Doviz → Appearance** to match your site theme.
4. Insert blocks/widgets/shortcodes where you want the data to appear.

== Frequently Asked Questions ==
= Do I need an API key? =
No. All data sources are publicly available without authentication.

= How often is the data refreshed? =
Data is cached for 1 hour and refreshed via a scheduled cron job.

= Does the plugin store any personal data? =
No. It only fetches public financial data.

= Is the plugin compatible with Elementor? =
Yes – three Elementor widgets are provided.

== Screenshots ==
1. Settings page with Persian red accents and premium silver/white design.
2. Gutenberg Exchange Rates block preview.
3. Elementor widget settings panel.
4. Mobile header ticker view.
5. Appearance settings page with color picker.

== Changelog ==
= 1.0.0 =
* Initial release – full feature set, SEO and accessibility compliance, KVKK privacy page, CI pipeline, unit tests.

== Upgrade Notice ==
= 1.0.0 =
First stable release.
