<?php
/**
 * Plugin Name: LeDoviz - Turkish Exchange Rates
 * Plugin URI: https://github.com/LeMiira/Le-Doviz
 * Description: Lightweight Turkish exchange rates, plugin for WordPress with Elementor, Gutenberg, shortcode, and widget support.
 * Version: 1.0.1
 * Author: Mira
 * Author URI: https://miiiira.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: el-doviz
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
define( 'EL_DOVIZ_VERSION', '1.0.1' );
define( 'EL_DOVIZ_PATH', plugin_dir_path( __FILE__ ) );
define( 'EL_DOVIZ_URL', plugin_dir_url( __FILE__ ) );
define( 'EL_DOVIZ_SLUG', 'el-doviz' );

// 3. Autoloader
spl_autoload_register( function ( $class ) {
    $prefix = 'ElDoviz\\';
    $base_dir = EL_DOVIZ_PATH . 'src/';
    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        return;
    }
    $relative_class = substr( $class, $len );
    $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
    if ( file_exists( $file ) ) {
        require $file;
    }
} );


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
    $donate_link = '<a href="https://github.com/sponsors/LeMiira" target="_blank" style="color: #C41E3A; font-weight: bold;">' . esc_html__( 'Donate', 'el-doviz' ) . '</a>';
    array_unshift( $links, $donate_link );
    return $links;
} );
?>
