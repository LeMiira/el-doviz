<?php
/**
 * Plugin Name: LeDoviz - Turkish Exchange Rates
 * Plugin URI: https://github.com/LeMiira/ledoviz-turkish-exchange-rates
 * Description: Lightweight Turkish exchange rates, plugin for WordPress with Elementor, Gutenberg, shortcode, and widget support.
 * Version: 1.0.0
 * Author: Mira
 * Author URI: https://miiiira.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ledoviz-turkish-exchange-rates
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 7.0
 * Requires PHP: 7.4
 */

// 1. ABSPATH protection
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 2. Constants
define( 'EL_DOVIZ_VERSION', '1.0.0' );
define( 'EL_DOVIZ_PATH', plugin_dir_path( __FILE__ ) );
define( 'EL_DOVIZ_URL', plugin_dir_url( __FILE__ ) );
define( 'EL_DOVIZ_SLUG', 'ledoviz-turkish-exchange-rates' );

// 3. Composer autoload
if ( file_exists( EL_DOVIZ_PATH . 'vendor/autoload.php' ) ) {
    require_once EL_DOVIZ_PATH . 'vendor/autoload.php';
}


// 5. Hooks
if ( class_exists( '\ElDoviz\Installer' ) ) {
    register_activation_hook( __FILE__, [ '\ElDoviz\Installer', 'activate' ] );
    register_deactivation_hook( __FILE__, [ '\ElDoviz\Installer', 'deactivate' ] );
}

// 6. Plugin boot
if ( class_exists( '\ElDoviz\Main' ) && method_exists( '\ElDoviz\Main', 'instance' ) ) {
    \ElDoviz\Main::instance();
}

// 7. Plugin Action Links (Donate link)
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
    $donate_link = '<a href="https://github.com/sponsors/LeMiira" target="_blank" style="color: #C41E3A; font-weight: bold;">' . esc_html__( 'Donate', 'ledoviz-turkish-exchange-rates' ) . '</a>';
    array_unshift( $links, $donate_link );
    return $links;
} );
?>
