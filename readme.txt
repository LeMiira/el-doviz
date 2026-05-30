=== LeDoviz - Turkish Exchange Rates ===
Contributors: miiira
Donate link: https://github.com/sponsors/LeMiira
Tags: currency converter, exchange rates, turkey, elementor, gutenberg
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display live Turkish exchange rates using Gutenberg blocks, Elementor widgets, shortcodes, and classic WordPress widgets.

== Description ==

LeDoviz is a lightweight WordPress plugin for displaying Turkish exchange rates on your website. It supports Gutenberg, Elementor, shortcodes, and traditional widgets while focusing on performance, accessibility, and ease of use.

LeDoviz, Türkiye Cumhuriyeti Merkez Bankası (TCMB) döviz kurlarını sitenizde şık ve yüksek performanslı bir şekilde göstermeniz için tasarlanmış birinci sınıf, hafif ve güvenli bir WordPress eklentisidir.

Features / Özellikler:

* Display live exchange rates from trusted public sources / Güvenilir kaynaklardan canlı kurlar.
* Native Gutenberg block support / Yerel Gutenberg blok desteği.
* Native Elementor widget support / Yerel Elementor bileşeni desteği.
* Shortcode support for easy placement anywhere / Kolay kullanım için kısa kod desteği.
* Responsive mobile-friendly layouts / Mobil uyumlu tasarımlar.
* Translation ready (English & Turkish) / Çeviriye hazır (İngilizce & Türkçe).
* Accessibility-focused interface / Erişilebilirlik odaklı arayüz.
* Lightweight and optimized for performance / Hafif ve performans odaklı.
* No API keys required / API anahtarı gerektirmez.

== External Services ==

This plugin connects to external services to retrieve financial data.

= Central Bank of the Republic of Turkey (TCMB) =

Used to obtain daily exchange rates against the Turkish Lira.

* URL: https://www.tcmb.gov.tr/kurlar/today.xml
* Data sent: None
* Data received: Public exchange rate information
* Data is cached locally for one hour

Terms:
https://www.tcmb.gov.tr/wps/wcm/connect/tr/tcmb+tr/main+menu/yasal+uyari

= Bigpara (Hürriyet) =

Used to retrieve BIST 100 index information.

* URL: https://bigpara.hurriyet.com.tr
* Data sent: None
* Data received: Public market information
* Data is cached locally for one hour

Terms:
https://www.hurriyet.com.tr/veriler/politikamiz/

== Shortcodes ==

Display exchange rates:

[ledoviz_turkish_exchange_rates_exchange_rates currencies="usd,eur,gbp" layout="list"]

Parameters:

* currencies - Comma separated currency codes.
* layout - list or grid.
* theme - auto, light, or dark.

Display ticker:

[ledoviz_turkish_exchange_rates_ticker currencies="usd,eur,gbp" speed="5000"]

Parameters:

* currencies - Comma separated currency codes.
* speed - Animation speed in milliseconds.

Display privacy notice:

[ledoviz_turkish_exchange_rates_privacy]

== Blocks and Widgets / Bloklar ve Bileşenler ==

Gutenberg Blocks / Blokları:

* Exchange Rates / Döviz Kurları
* Live Ticker / Canlı Kur Bandı
* Privacy & KVKK / Gizlilik ve KVKK
* Exchange Rates & Trends / Döviz Kurları ve Trendler

Elementor Widgets / Bileşenleri:

* Exchange Rates / Döviz Kurları
* Live Ticker / Canlı Kur Bandı
* Privacy & KVKK / Gizlilik ve KVKK
* Exchange Rates & Trends / Döviz Kurları ve Trendler

== Performance ==

* Uses WordPress Transients API for caching.
* Financial data is cached for one hour.
* Frontend assets are loaded only when required.

== Accessibility ==

* Keyboard accessible controls.
* Screen-reader friendly markup.
* Reduced motion support where available.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins screen in WordPress.
3. Add a block, widget, or shortcode where you want exchange rates displayed.

== Frequently Asked Questions ==

= Do I need an API key? =

No. Public data sources are used and no API key is required.

= How often is data refreshed? =

Data is cached for one hour before being refreshed.

= Does the plugin collect personal information? =

No. The plugin only retrieves publicly available financial information.

= Is Elementor supported? =

Yes. Dedicated Elementor widgets are included.

== Screenshots ==

1. Exchange rates displayed on the frontend.
2. Elementor widget settings and live preview.

== Changelog ==

= 1.0.1 =

* Added "Privacy & KVKK" and "Exchange Rates & Trends" Gutenberg blocks.
* Added "Boxy Design" toggle to the Elementor Trend Widget.
* Fixed translation files not loading in the admin dashboard and ensured 100% localization via .po/.mo files.
* "Gizlilik ve KVKK" ile "Döviz Kurları ve Trendler" Gutenberg blokları eklendi.
* Elementor Trend bileşenine "Kutu Tasarımı (Boxy Design)" seçeneği eklendi.
* Yönetim panelindeki çeviri sorunları giderildi ve tüm metinler %100 çevrilebilir hale getirildi.

= 1.0.0 =

* Initial public release.

== Upgrade Notice ==

= 1.0.1 =

Contains important translation fixes and new Gutenberg blocks. / Önemli çeviri düzeltmeleri ve yeni Gutenberg blokları içerir.

= 1.0.0 =

Initial stable release.