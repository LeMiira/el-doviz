<?php
namespace ElDoviz\Service;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Simple logger that writes to a file when debug mode is enabled.
 * The log directory is wp-content/uploads/el-doviz/logs/.
 */
class Logger {
    /**
     * Log a message with timestamp.
     *
     * @param string $message
     */
    public static function log( $message ) {
        $upload_dir = wp_upload_dir();
        $log_dir    = trailingslashit( $upload_dir['basedir'] ) . 'el-doviz/logs';
        if ( ! file_exists( $log_dir ) ) {
            wp_mkdir_p( $log_dir );
        }
        $log_file = trailingslashit( $log_dir ) . 'debug.log';
        $time     = current_time( 'Y-m-d H:i:s' );
        $entry    = "[$time] $message\n";
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        error_log( $entry, 3, $log_file );
    }
}
?>
