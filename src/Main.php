<?php
namespace ElDoviz;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use ElDoviz\Installer;

use ElDoviz\Admin\Settings;
use ElDoviz\Shortcode\ShortcodeGenerator;
use ElDoviz\Widget\HeaderTickerWidget;
use ElDoviz\Widget\FooterTickerWidget;
use ElDoviz\Widget\SidebarWidget;
use ElDoviz\Block\BlockRegistrar;
use ElDoviz\Elementor\ElementorRegistrar;
use ElDoviz\REST\RatesController;
use ElDoviz\Service\DataFetcher;
use ElDoviz\Service\CacheManager;
use ElDoviz\Util\Sanitizer;

/**
 * Main plugin class – singleton.
 */
class Main {
    /** @var Main|null */
    private static $instance = null;

    /** @var Settings */
    public $settings;

    /** @var DataFetcher */
    public $data_fetcher;

    /** @var CacheManager */
    public $cache_manager;

    private function __construct() {
        // Initialize services.
        $this->cache_manager = new CacheManager();
        $this->data_fetcher  = new DataFetcher( $this->cache_manager );
        $this->settings      = new Settings();

        // Register assets.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        // Register core components and hooks.
        add_action( 'init', [ $this, 'register_components' ] );
        add_action( 'admin_init', [ $this->settings, 'register_settings' ] );
        add_action( 'admin_menu', [ Settings::class, 'add_admin_menu' ] );
        add_action( 'widgets_init', [ $this, 'register_widgets' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        // Cron callback for data refresh.
        add_action( 'el_doviz_refresh_data', [ Installer::class, 'refresh_data' ] );
    }

    /**
     * Enqueue frontend CSS and optional JS.
     */
    public function enqueue_assets() {
        // Enqueue CSS.
        wp_enqueue_style( 'el-doviz-frontend', EL_DOVIZ_URL . 'assets/css/frontend.css', [], EL_DOVIZ_VERSION );
        // Enqueue JS (optional, currently empty placeholder).
        wp_enqueue_script( 'el-doviz-frontend-js', EL_DOVIZ_URL . 'assets/js/frontend.js', [], EL_DOVIZ_VERSION, true );
    }


    /**
     * Get singleton instance.
     *
     * @return Main
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register Gutenberg blocks, Elementor widgets, shortcodes, etc.
     */
    public function register_components() {
        // Gutenberg blocks.
        if ( function_exists( 'register_block_type' ) ) {
            BlockRegistrar::register();
        }
        // Elementor.
        if ( class_exists( '\Elementor\Plugin' ) ) {
            ElementorRegistrar::register();
        }
        // Shortcodes.
        ShortcodeGenerator::register();
    }

    /**
     * Register widget classes.
     */
    public function register_widgets() {
        register_widget( HeaderTickerWidget::class );
        register_widget( FooterTickerWidget::class );
        register_widget( SidebarWidget::class );
    }

    /**
     * Register custom REST endpoints.
     */
    public function register_rest_routes() {
        $controller = new RatesController();
        $controller->register_routes();
    }

    /**
     * Activation hook.
     */
    public static function activate() {
        // Schedule cron for data refresh.
        if ( ! wp_next_scheduled( 'el_doviz_refresh_data' ) ) {
            wp_schedule_event( time(), 'hourly', 'el_doviz_refresh_data' );
        }
        // Set default options if not existent.
        if ( false === get_option( 'el_doviz_options' ) ) {
            add_option( 'el_doviz_options', [] );
        }
    }

    /**
     * Deactivation hook.
     */
    public static function deactivate() {
        wp_clear_scheduled_hook( 'el_doviz_refresh_data' );
    }
}
?>
