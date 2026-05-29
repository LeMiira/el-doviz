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
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        // Register core components and hooks.
        add_action( 'init', [ $this, 'register_components' ] );
        add_action( 'admin_init', [ $this->settings, 'register_settings' ] );
        add_action( 'admin_menu', [ Settings::class, 'add_admin_menu' ] );
        add_action( 'widgets_init', [ $this, 'register_widgets' ] );
        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        // Cron callback for data refresh.
        add_action( 'ledoviz_turkish_exchange_rates_refresh_data', [ Installer::class, 'refresh_data' ] );

        // Language override
        add_filter( 'gettext', [ $this, 'translate_strings' ], 10, 3 );
    }

    /**
     * Enqueue frontend CSS and optional JS.
     */
    public function enqueue_assets() {
        // Enqueue CSS.
        wp_enqueue_style( 'ledoviz-turkish-exchange-rates-frontend', EL_DOVIZ_URL . 'assets/css/frontend.css', [], EL_DOVIZ_VERSION );
        // Enqueue JS (optional, currently empty placeholder).
        wp_enqueue_script( 'ledoviz-turkish-exchange-rates-frontend-js', EL_DOVIZ_URL . 'assets/js/frontend.js', [], EL_DOVIZ_VERSION, true );
    }

    /**
     * Enqueue admin CSS.
     */
    public function enqueue_admin_assets() {
        wp_enqueue_style( 'ledoviz-turkish-exchange-rates-admin', EL_DOVIZ_URL . 'assets/css/admin.css', [], EL_DOVIZ_VERSION );
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
        if ( ! wp_next_scheduled( 'ledoviz_turkish_exchange_rates_refresh_data' ) ) {
            wp_schedule_event( time(), 'hourly', 'ledoviz_turkish_exchange_rates_refresh_data' );
        }
        // Set default options if not existent.
        if ( false === get_option( 'ledoviz_turkish_exchange_rates_options' ) ) {
            add_option( 'ledoviz_turkish_exchange_rates_options', [] );
        }
    }

    /**
     * Deactivation hook.
     */
    public static function deactivate() {
        wp_clear_scheduled_hook( 'ledoviz_turkish_exchange_rates_refresh_data' );
    }

    /**
     * Override Turkish strings to English if language is set to 'en'.
     */
    public function translate_strings( $translation, $text, $domain ) {
        if ( 'ledoviz-turkish-exchange-rates' !== $domain ) {
            return $translation;
        }

        $options = get_option( 'ledoviz_turkish_exchange_rates_options' );
        $lang = isset( $options['language'] ) ? $options['language'] : 'tr';

        if ( 'en' === $lang ) {
            $dict = [
                'Bu sitede gösterilen tüm döviz kurları ve finansal veriler, Türkiye Cumhuriyet Merkez Bankası (TCMB) tarafından sağlanan halka açık verilerden alınmakta olup yalnızca bilgilendirme amaçlıdır. Verilerin kesin doğruluğu veya anlık güncelliği garanti edilmez. Kişisel verileriniz KVKK kapsamında korunmakta olup, bu eklenti aracılığıyla hiçbir kişisel ziyaretçi verisi toplanmamakta veya işlenmemektedir.' => 'All exchange rates and financial data shown on this site are obtained from public data provided by the Central Bank of the Republic of Turkey (CBRT) and are for informational purposes only. Absolute accuracy or instant currency of the data is not guaranteed. Your personal data is protected under data privacy laws; no personal visitor data is collected or processed through this plugin.',
                'Döviz kurları yüklenemedi.' => 'Exchange rates could not be loaded.',
                'Döviz Kurları' => 'Exchange Rates',
                'El Döviz Canlı Kur Bandı' => 'El Doviz Live Ticker',
                'El Döviz Gizlilik ve KVKK' => 'El Doviz Privacy & GDPR',
                'El Döviz' => 'El Doviz',
                'Panel' => 'Dashboard',
                'Günlükler' => 'Logs',
                'Genel Ayarlar' => 'General Settings',
                'Araçlar' => 'Tools',
                'Kullanım Rehberi' => 'Documentation',
                'Hata Ayıklama Modu' => 'Debug Mode',
                'Dil / Language' => 'Language',
                'Eklenti Günlükleri' => 'Plugin Logs',
                'Teşhis ve Testler' => 'Diagnostics & Tests',
                'Önbelleği Temizle' => 'Clear Cache',
                'Önbellek başarıyla temizlendi.' => 'Cache cleared successfully.',
                'El Döviz Kontrol Paneli' => 'El Doviz Dashboard',
                'El Döviz Kurları ve Endeksler' => 'El Doviz Exchange Rates and Indexes',
                'İçerik' => 'Content',
                'Para Birimi/Endeks Seçin' => 'Select Currency/Index',
                'Bant Hızı (ms)' => 'Ticker Speed (ms)',
                'Stil Seçenekleri' => 'Style Options',
                'Metin Rengi' => 'Text Color',
                'Arka Plan Rengi' => 'Background Color',
                'İç Boşluk (Padding)' => 'Padding',
                'Hizalama' => 'Alignment',
                'Açıklama Metni' => 'Description Text',
                'Sol' => 'Left',
                'Orta' => 'Center',
                'Sağ' => 'Right',
                'Site üst bilgisinde döviz kuru bandını göster.' => 'Show exchange rate ticker in site header.',
                'Hata ayıklama günlüğünü etkinleştir (uploads/ledoviz-turkish-exchange-rates/logs/debug.log dosyasına yazar).' => 'Enable debug logging (writes to uploads/ledoviz-turkish-exchange-rates/logs/debug.log).',
                'Kısa Kodlar (Shortcodes)' => 'Shortcodes',
                'Döviz Kurları Listesi' => 'Exchange Rates List',
                'Canlı Kur Bandı' => 'Live Ticker',
                'Gizlilik ve KVKK Açıklaması' => 'Privacy & GDPR Disclosure',
                'Elementor Entegrasyonu' => 'Elementor Integration',
                'Gutenberg ve Yan Menü Bileşenleri' => 'Gutenberg & Sidebar Widgets',
                'Geliştirici Filtreleri (Filters)' => 'Developer Filters',
                'Eklentiyi Destekleyin' => 'Support the Plugin',
                'Eklenti Bilgileri' => 'Plugin Information',
                'Sürüm:' => 'Version:',
                'Geliştirici:' => 'Developer:',
                'Lisans:' => 'License:',
                'Veri Kaynağı:' => 'Data Source:',
                'TCMB (T.C. Merkez Bankası)' => 'CBRT (Central Bank of the Republic of Turkey)',
            ];
            
            if ( isset( $dict[ $text ] ) ) {
                return $dict[ $text ];
            }
        }

        return $translation;
    }
}
?>
