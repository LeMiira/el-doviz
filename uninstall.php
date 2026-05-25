<?php
/**
 * Uninstall script for El Doviz plugin.
 *
 * This file is executed when the plugin is deleted via the WordPress admin.
 * It removes all stored options, transients, scheduled cron jobs, and log files.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

(function() {
    // Delete plugin options.
    if ( function_exists( 'delete_option' ) ) {
        delete_option( 'el_doviz_options' );
    }

    // Delete transients.
    if ( function_exists( 'delete_transient' ) ) {
        delete_transient( 'el_doviz_tcmb' );
        delete_transient( 'el_doviz_bist' );
    }

    // Clear scheduled cron hook.
    if ( function_exists( 'wp_clear_scheduled_hook' ) ) {
        wp_clear_scheduled_hook( 'el_doviz_refresh_data' );
    }

    // Remove logs directory using standard WordPress WP_Filesystem.
    $upload_dir = wp_upload_dir();
    $log_dir    = trailingslashit( $upload_dir['basedir'] ) . 'el-doviz';

    require_once ABSPATH . 'wp-admin/includes/file.php';
    if ( function_exists( 'WP_Filesystem' ) ) {
        WP_Filesystem();
        global $wp_filesystem;
        if ( $wp_filesystem && $wp_filesystem->is_dir( $log_dir ) ) {
            $wp_filesystem->delete( $log_dir, true );
        }
    }
})();
?>
