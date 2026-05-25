<?php
namespace ElDoviz;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use ElDoviz\Service\CacheManager;
use ElDoviz\Service\DataFetcher;

/**
 * Installer handles activation, deactivation and cron scheduling.
 */
class Installer {
    /**
     * Run on plugin activation.
     */
    public static function activate() {
        // Schedule hourly data refresh.
        if ( ! wp_next_scheduled( 'el_doviz_refresh_data' ) ) {
            wp_schedule_event( time(), 'hourly', 'el_doviz_refresh_data' );
        }
        // Set default options if not exist.
        if ( false === get_option( 'el_doviz_options' ) ) {
            add_option( 'el_doviz_options', [] );
        }
    }

    /**
     * Run on plugin deactivation.
     */
    public static function deactivate() {
        wp_clear_scheduled_hook( 'el_doviz_refresh_data' );
    }

    /**
     * Cron callback to refresh cached data.
     */
    public static function refresh_data() {
        $cache = new CacheManager();
        $fetcher = new DataFetcher( $cache );
        // Refresh each source.
        $sources = [ 'tcmb' ];
        foreach ( $sources as $src ) {
            $fetcher->fetch( $src, HOUR_IN_SECONDS );
        }
    }
}
?>
