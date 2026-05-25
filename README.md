# 📈 El Döviz

[![WordPress Version](https://img.shields.io/badge/WordPress-%3E%3D%206.0-blue.svg?style=flat-square&logo=wordpress)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%207.4-8892BF.svg?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-GPLv2%20or%20later-green.svg?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Elementor Compatible](https://img.shields.io/badge/Elementor-Compatible-red.svg?style=flat-square&logo=elementor)](https://elementor.com)
[![Gutenberg Compatible](https://img.shields.io/badge/Gutenberg-Compatible-black.svg?style=flat-square&logo=gutenberg)](https://wordpress.org/gutenberg/)

> **El Döviz** is a premium, lightweight, and high-performance WordPress plugin designed to seamlessly display Turkish Central Bank (TCMB) exchange rates. It features native support for Elementor widgets, Gutenberg blocks, responsive sidebars, shortcodes, and scrolling marquees.

---

## ✨ Features

- **🚀 Multiple Display Options**: Grid, List, Scrolling Ticker, Sidebar, Header, and Footer display variations.
- **🎨 Premium Custom Styling**: Features a sleek Turkish-inspired styling palette (Persian Red accents with silver/white theme colors) customizable via CSS variables.
- **🛡️ Full Security Compliance**: Meets 100% of WordPress.org security, capability validation, and direct access guidelines.
- **⚖️ KVKK & GDPR Ready**: Fully compliant with Turkish KVKK privacy rules. Includes dedicated warning banners, a customizable KVKK consent widget, and standard privacy shortcodes.
- **⚡ Advanced Performance Caching**: Leverages the WordPress Transients API to cache rates for 1 hour, reducing external XML hits.
- **♿ Fully Accessible (a11y)**: Built with ARIA live regions (`aria-live="polite"`), native keyboard navigation, and `prefers-reduced-motion` animation-override support.
- **🌍 Bilingual Support**: 100% localized and ready for Turkish (`tr_TR`) and English (`en_US`).

---

## 🎨 Visual Themes & CSS Variables

Customize colors directly using CSS variables in your theme:

```css
:root {
  --el-doviz-primary: #C41E3A; /* Accent Color (Persian Red) */
  --el-doviz-bg: #f8f9fa;      /* Silver/white container background */
  --el-doviz-text: #212529;    /* Text Color */
}
```

---

## 🔌 Integration Guide

### 1. Shortcodes
Use these shortcodes in standard posts, pages, or widgets:

* **Rates Grid/List**:
  ```text
  [el_doviz_exchange_rates currencies="usd,eur,gbp" layout="list" theme="auto"]
  ```
* **Live Scrolling Ticker**:
  ```text
  [el_doviz_ticker currencies="usd,eur,gbp" speed="5000"]
  ```
* **KVKK / Privacy Disclosure**:
  ```text
  [el_doviz_privacy]
  ```

### 2. Gutenberg Blocks
Search for these blocks in the block editor:
* **Exchange Rates** - Customizable grid/list list of currencies.
* **Live Ticker** - Clean marquee banner ticker.

### 3. Elementor Widgets
Available under the **El Döviz** category in the Elementor sidebar:
* **Exchange Rates** - Includes interactive sliders for Row Spacing, Alignment, and gaps.
* **Live Ticker** - Fully customizable scrolling marquee.
* **Privacy & KVKK** - Golden lock-themed compliance disclaimer block.

---

## 🛠️ Developer Hooks & Filters

For advanced users, filter parameters dynamically:

```php
// Customize API endpoints (e.g. TCMB XML URL and BIST Hurriyet endpoint)
add_filter( 'el_doviz_api_endpoints', function( $endpoints ) {
    $endpoints['tcmb'] = 'https://your-fallback-source.com/xml';
    return $endpoints;
} );
```

---

## 📦 Installation & Setup

1. **Clone the repository** to your plugins directory:
   ```bash
   cd wp-content/plugins
   git clone https://github.com/LeMiira/el-doviz.git
   ```
2. **Generate the autoloader**:
   ```bash
   cd el-doviz
   composer install
   ```
3. **Activate the plugin** in your WordPress dashboard under **Plugins**.
4. Configure options under the **El Döviz** settings panel.
