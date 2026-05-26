<?php
namespace ElDoviz\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use ElDoviz\Util\Sanitizer;

/**
 * Settings page using WordPress Settings API.
 */
class Settings {
    /** Option name used to store plugin settings. */
    const OPTION_NAME = 'ledoviz_turkish_exchange_rates_options';

    /**
     * Register settings, sections and fields.
     */
    public function register_settings() {
        register_setting( 'ledoviz_turkish_exchange_rates_settings_group', self::OPTION_NAME, [
            'type'              => 'array',
            'description'       => esc_html__( 'El Döviz eklenti seçenekleri', 'ledoviz-turkish-exchange-rates' ),
            'sanitize_callback' => [ $this, 'sanitize_options' ],
            'default'           => [],
        ] );

        add_settings_section(
            'ledoviz_turkish_exchange_rates_general_section',
            esc_html__( 'Genel Ayarlar', 'ledoviz-turkish-exchange-rates' ),
            function () {
                echo '<p>' . esc_html__( 'El Döviz eklentisinin genel davranışını yapılandırın.', 'ledoviz-turkish-exchange-rates' ) . '</p>'; },
            'ledoviz-turkish-exchange-rates-settings'
        );

        add_settings_field(
            'enable_header_ticker',
            esc_html__( 'Üst Bilgi Bandını Etkinleştir', 'ledoviz-turkish-exchange-rates' ),
            [ $this, 'field_enable_header_ticker' ],
            'ledoviz-turkish-exchange-rates-settings',
            'ledoviz_turkish_exchange_rates_general_section'
        );

        // Debug mode toggle – writes to log when enabled.
        add_settings_field(
            'debug_mode',
            esc_html__( 'Hata Ayıklama Modu', 'ledoviz-turkish-exchange-rates' ),
            [ $this, 'field_debug_mode' ],
            'ledoviz-turkish-exchange-rates-settings',
            'ledoviz_turkish_exchange_rates_general_section'
        );
    }

    /**
     * Sanitize the whole options array.
     *
     * @param mixed $input
     * @return array
     */
    public function sanitize_options( $input ) {
        $sanitized = [];
        if ( isset( $input['enable_header_ticker'] ) ) {
            $sanitized['enable_header_ticker'] = Sanitizer::int( $input['enable_header_ticker'] );
        }
        if ( isset( $input['debug_mode'] ) ) {
            $sanitized['debug_mode'] = Sanitizer::int( $input['debug_mode'] );
        }
        return $sanitized;
    }

    /**
     * Render checkbox field for header ticker.
     */
    public function field_enable_header_ticker() {
        $options = get_option( self::OPTION_NAME );
        $value   = isset( $options['enable_header_ticker'] ) ? (int) $options['enable_header_ticker'] : 0;
        echo '<input type="checkbox" id="enable_header_ticker" name="' . esc_attr( self::OPTION_NAME ) . '[enable_header_ticker]" value="1" ' . checked( 1, $value, false ) . ' />';
        echo '<label for="enable_header_ticker">' . esc_html__( 'Site üst bilgisinde döviz kuru bandını göster.', 'ledoviz-turkish-exchange-rates' ) . '</label>';
    }

    // New debug mode field
    public function field_debug_mode() {
        $options = get_option( self::OPTION_NAME );
        $value   = isset( $options['debug_mode'] ) ? (int) $options['debug_mode'] : 0;
        echo '<input type="checkbox" id="debug_mode" name="' . esc_attr( self::OPTION_NAME ) . '[debug_mode]" value="1" ' . checked( 1, $value, false ) . ' />';
        echo '<label for="debug_mode">' . esc_html__( 'Hata ayıklama günlüğünü etkinleştir (uploads/ledoviz-turkish-exchange-rates/logs/debug.log dosyasına yazar).', 'ledoviz-turkish-exchange-rates' ) . '</label>';
    }

    /**
     * Add admin menu items.
     */
    public static function add_admin_menu() {
        add_menu_page(
            esc_html__( 'El Döviz', 'ledoviz-turkish-exchange-rates' ),
            esc_html__( 'El Döviz', 'ledoviz-turkish-exchange-rates' ),
            'manage_options',
            'ledoviz-turkish-exchange-rates',
            [ __CLASS__, 'render_dashboard' ],
            'dashicons-chart-pie',
            81
        );
        add_submenu_page( 'ledoviz-turkish-exchange-rates', esc_html__( 'Panel', 'ledoviz-turkish-exchange-rates' ), esc_html__( 'Panel', 'ledoviz-turkish-exchange-rates' ), 'manage_options', 'ledoviz-turkish-exchange-rates', [ __CLASS__, 'render_dashboard' ] );
        add_submenu_page( 'ledoviz-turkish-exchange-rates', esc_html__( 'Günlükler', 'ledoviz-turkish-exchange-rates' ), esc_html__( 'Günlükler', 'ledoviz-turkish-exchange-rates' ), 'manage_options', 'ledoviz-turkish-exchange-rates-logs', [ \ElDoviz\Admin\Page\LogsPage::class, 'render' ] );
    }

    public static function render_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Yetersiz yetki.', 'ledoviz-turkish-exchange-rates' ) );
        }

        // Handle tools operations (e.g. clear cache) if post request is sent
        if ( isset( $_POST['ledoviz_turkish_exchange_rates_clear_cache'] ) && check_admin_referer( 'ledoviz_turkish_exchange_rates_tools_action', 'ledoviz_turkish_exchange_rates_nonce' ) ) {
            $cache = new \ElDoviz\Service\CacheManager();
            $cache->delete( 'tcmb' );
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Önbellek başarıyla temizlendi.', 'ledoviz-turkish-exchange-rates' ) . '</p></div>';
        }

        // Active tab detection
        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'usage';
        $allowed_tabs = [ 'usage', 'settings', 'tools' ];
        if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
            $active_tab = 'usage';
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'El Döviz Kontrol Paneli', 'ledoviz-turkish-exchange-rates' ) . '</h1>';

        // Styling is loaded via admin.css using admin_enqueue_scripts.

        echo '<div class="ledoviz-turkish-exchange-rates-dashboard-layout">';
        echo '<div class="ledoviz-turkish-exchange-rates-main-column">';

        // KVKK warning alert block
        echo '<div class="ledoviz-turkish-exchange-rates-kvkk-alert" style="background: #fff8e5; border: 1px solid #ffeb3b; border-left: 4px solid #ffb900; padding: 15px; margin: 15px 0 25px 0; border-radius: 4px; display: flex; align-items: center; max-width: 1000px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
        echo '<span class="dashicons dashicons-shield" style="font-size: 24px; width: 24px; height: 24px; color: #ffb900; margin-right: 12px; display: flex; align-items: center;"></span>';
        echo '<div style="font-size: 0.95rem; line-height: 1.5; color: #665c40;">';
        echo '<strong>' . esc_html__( 'KVKK ve Veri Güvenliği Bildirimi:', 'ledoviz-turkish-exchange-rates' ) . '</strong> ';
        echo esc_html__( 'Bu eklenti, Türkiye Cumhuriyeti Merkez Bankası (TCMB) verilerini kullanarak döviz kurlarını gösterir. Eklenti aracılığıyla ziyaretçilerinize ait hiçbir kişisel veri toplanmaz, işlenmez veya üçüncü şahıslarla paylaşılmaz. KVKK uyumluluğu için sayfalarınıza "El Döviz Gizlilik ve KVKK" bileşenini veya [ledoviz_turkish_exchange_rates_privacy] kısa kodunu ekleyebilirsiniz.', 'ledoviz-turkish-exchange-rates' );
        echo '</div>';
        echo '</div>';

        // Render Premium Navigation Tabs
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="' . esc_url( admin_url( 'admin.php?page=ledoviz-turkish-exchange-rates&tab=usage' ) ) . '" class="nav-tab ' . ( 'usage' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Kullanım Rehberi', 'ledoviz-turkish-exchange-rates' ) . '</a>';
        echo '<a href="' . esc_url( admin_url( 'admin.php?page=ledoviz-turkish-exchange-rates&tab=settings' ) ) . '" class="nav-tab ' . ( 'settings' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Genel Ayarlar', 'ledoviz-turkish-exchange-rates' ) . '</a>';
        echo '<a href="' . esc_url( admin_url( 'admin.php?page=ledoviz-turkish-exchange-rates&tab=tools' ) ) . '" class="nav-tab ' . ( 'tools' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Araçlar', 'ledoviz-turkish-exchange-rates' ) . '</a>';
        echo '</h2>';

        echo '<div class="tab-content" style="margin-top: 20px;">';

        if ( 'usage' === $active_tab ) {
            // Documentations content
            echo '<div style="max-width: 1000px;">';
            echo '<p style="font-size: 1.15rem; color: #555;">' . esc_html__( 'El Döviz eklentisinin tüm özelliklerini, kısa kodlarını ve entegrasyonlarını bu sayfada bulabilirsiniz.', 'ledoviz-turkish-exchange-rates' ) . '</p>';



            // Section 1: Shortcodes
            echo '<div class="ledoviz-turkish-exchange-rates-doc-section">';
            echo '<h2>' . esc_html__( 'Kısa Kodlar (Shortcodes)', 'ledoviz-turkish-exchange-rates' ) . '</h2>';
            echo '<p>' . esc_html__( 'Eklentinin sunduğu özellikleri herhangi bir yazı veya sayfaya eklemek için aşağıdaki kısa kodları kullanabilirsiniz.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '<div class="ledoviz-turkish-exchange-rates-doc-grid">';
            
            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<h3>' . esc_html__( 'Döviz Kurları Listesi', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<div class="ledoviz-turkish-exchange-rates-code-box">[ledoviz_turkish_exchange_rates_exchange_rates]</div>';
            echo '<p>' . esc_html__( 'Belirtilen para birimlerini ve endeksleri liste veya ızgara biçiminde gösterir.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '<p><strong>' . esc_html__( 'Parametreler:', 'ledoviz-turkish-exchange-rates' ) . '</strong><br>';
            echo '<code>currencies</code>: ' . esc_html__( 'Gösterilecek para birimleri (örn: usd,eur,gbp,bist)', 'ledoviz-turkish-exchange-rates' ) . '<br>';
            echo '<code>layout</code>: ' . esc_html__( 'Görünüm biçimi (list veya grid)', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<h3>' . esc_html__( 'Canlı Kur Bandı', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<div class="ledoviz-turkish-exchange-rates-code-box">[ledoviz_turkish_exchange_rates_ticker]</div>';
            echo '<p>' . esc_html__( 'Sitenin üst veya alt kısmında kayan döviz kurları bandı görüntüler.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '<p><strong>' . esc_html__( 'Parametreler:', 'ledoviz-turkish-exchange-rates' ) . '</strong><br>';
            echo '<code>currencies</code>: ' . esc_html__( 'Kayan bantta yer alacak birimler (örn: usd,eur,bist)', 'ledoviz-turkish-exchange-rates' ) . '<br>';
            echo '<code>speed</code>: ' . esc_html__( 'Milisaniye cinsinden döngü hızı (örn: 5000)', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<h3>' . esc_html__( 'Gizlilik ve KVKK Açıklaması', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<div class="ledoviz-turkish-exchange-rates-code-box">[ledoviz_turkish_exchange_rates_privacy]</div>';
            echo '<p>' . esc_html__( 'Kullanıcılara eklenti aracılığıyla hiçbir kişisel verinin işlenmediğini belirten KVKK uyarı metnini gösterir.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '</div>';
            echo '</div>';

            // Section 2: Elementor
            echo '<div class="ledoviz-turkish-exchange-rates-doc-section">';
            echo '<h2>' . esc_html__( 'Elementor Entegrasyonu', 'ledoviz-turkish-exchange-rates' ) . '</h2>';
            echo '<p>' . esc_html__( 'Elementor Düzenleyicide "El Döviz" kategorisi altındaki bileşenleri sürükleyip bırakarak sayfalarınıza ekleyebilirsiniz.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '<div class="ledoviz-turkish-exchange-rates-doc-grid">';

            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<span class="ledoviz-turkish-exchange-rates-badge">Elementor Widget</span>';
            echo '<h3>' . esc_html__( 'El Döviz Kurları ve Endeksler', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<p>' . esc_html__( 'Gelişmiş liste veya ızgara düzeni seçimi. Stil sekmesinden etiket ve değerlerin tipografi, renk, kenarlık ve kutu gölgesi ayarlarını dinamik olarak değiştirebilirsiniz.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<span class="ledoviz-turkish-exchange-rates-badge">Elementor Widget</span>';
            echo '<h3>' . esc_html__( 'El Döviz Canlı Kur Bandı', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<p>' . esc_html__( 'Canlı kurları kayan yazı bandı şeklinde eklemenizi sağlar. Kayan yazı hızı ve yazı tipi renkleri düzenlenebilir.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<span class="ledoviz-turkish-exchange-rates-badge">Elementor Widget</span>';
            echo '<h3>' . esc_html__( 'El Döviz Gizlilik ve KVKK', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<p>' . esc_html__( 'Gizlilik ve veri sorumluluğu beyan metnini sayfalarınıza ekler. Yazı rengi, arka planı ve kutu tasarımı özelleştirilebilir.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '</div>';
            echo '</div>';

            // Section 3: Gutenberg & Widgets
            echo '<div class="ledoviz-turkish-exchange-rates-doc-section">';
            echo '<h2>' . esc_html__( 'Gutenberg ve Yan Menü Bileşenleri', 'ledoviz-turkish-exchange-rates' ) . '</h2>';
            echo '<div class="ledoviz-turkish-exchange-rates-doc-grid">';

            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<h3>' . esc_html__( 'Gutenberg Blokları', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<p>' . esc_html__( 'WordPress blok düzenleyicisinde "Döviz Kurları" veya "Canlı Kur Bandı" bloklarını aratarak yazılarınıza kolayca yerleştirebilirsiniz.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '<div class="ledoviz-turkish-exchange-rates-doc-card">';
            echo '<h3>' . esc_html__( 'Yan Menü Bileşenleri (Widgets)', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
            echo '<p>' . esc_html__( 'Görünüm > Bileşenler sayfasına giderek "El Döviz Yan Menü Bileşeni"ni sitenizin aktif yan menü alanlarına (sidebar) ekleyebilirsiniz.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '</div>';

            echo '</div>';
            echo '</div>';

            // Section 4: Developer Filters
            echo '<div class="ledoviz-turkish-exchange-rates-doc-section">';
            echo '<h2>' . esc_html__( 'Geliştirici Filtreleri (Filters)', 'ledoviz-turkish-exchange-rates' ) . '</h2>';
            echo '<p>' . esc_html__( 'Yazılımcılar ve temalar için sunulan kanca (filter) kütüphanesi:', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            echo '<ul style="list-style: disc; padding-left: 20px;">';
            echo '<li><code>ledoviz_turkish_exchange_rates_api_endpoints</code>: ' . esc_html__( 'TCMB ve BIST 100 API veri kaynağı adreslerini değiştirmek için kullanılır.', 'ledoviz-turkish-exchange-rates' ) . '</li>';
            echo '</ul>';
            echo '</div>';

            echo '</div>';
        } elseif ( 'settings' === $active_tab ) {
            // Settings Form Page
            echo '<form method="post" action="options.php">';
            settings_fields( 'ledoviz_turkish_exchange_rates_settings_group' );
            do_settings_sections( 'ledoviz-turkish-exchange-rates-settings' );
            submit_button();
            echo '</form>';
        } elseif ( 'tools' === $active_tab ) {
            // Tools/Diagnostics Page
            echo '<div class="card" style="max-width: 600px; padding: 20px; border-left: 4px solid #C41E3A; background: #fff; border-top: 1px solid #ccd0d4; border-right: 1px solid #ccd0d4; border-bottom: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
            echo '<h2>' . esc_html__( 'Teşhis ve Testler', 'ledoviz-turkish-exchange-rates' ) . '</h2>';
            echo '<form method="post" action="">';
            wp_nonce_field( 'ledoviz_turkish_exchange_rates_tools_action', 'ledoviz_turkish_exchange_rates_nonce' );
            echo '<p>' . esc_html__( 'TCMB API uç noktalarından anında yeni kurların alınması için önbelleğin süresini dolmaya zorlayın.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
            submit_button( esc_html__( 'Önbelleği Temizle', 'ledoviz-turkish-exchange-rates' ), 'primary', 'ledoviz_turkish_exchange_rates_clear_cache', false );
            echo '</form>';
            echo '</div>';
        }

        echo '</div>'; // .tab-content
        echo '</div>'; // .ledoviz-turkish-exchange-rates-main-column

        // Sidebar Column
        echo '<div class="ledoviz-turkish-exchange-rates-sidebar-column">';
        
        // Support Card
        echo '<div class="ledoviz-turkish-exchange-rates-sidebar-card">';
        echo '<h3>' . esc_html__( 'Eklentiyi Destekleyin', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
        echo '<p>' . esc_html__( 'El Döviz eklentisini beğendiyseniz, geliştirilmesine katkıda bulunmak ve yeni özelliklerin eklenmesini desteklemek için bağış yapabilirsiniz.', 'ledoviz-turkish-exchange-rates' ) . '</p>';
        echo '<div class="ledoviz-turkish-exchange-rates-btn-group">';
        
        // GitHub Sponsors Button
        echo '<a href="https://github.com/sponsors/LeMiira" target="_blank" class="ledoviz-turkish-exchange-rates-btn ledoviz-turkish-exchange-rates-btn-github">';
        echo '<svg viewBox="0 0 16 16" width="18" height="18" fill="currentColor" style="margin-right: 8px;"><path fill-rule="evenodd" d="M8 1.482c4.847-3.817 12.23 2.196 0 11.625-12.23-9.429-4.847-15.442 0-11.625z"/></svg>';
        echo esc_html__( 'GitHub Sponsors', 'ledoviz-turkish-exchange-rates' );
        echo '</a>';
        
        // Buy Me a Coffee Button
        echo '<a href="https://www.buymeacoffee.com/miiiira" target="_blank" class="ledoviz-turkish-exchange-rates-btn ledoviz-turkish-exchange-rates-btn-coffee">';
        echo '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>';
        echo esc_html__( 'Buy Me a Coffee', 'ledoviz-turkish-exchange-rates' );
        echo '</a>';
        
        echo '</div>'; // .ledoviz-turkish-exchange-rates-btn-group
        echo '</div>'; // .ledoviz-turkish-exchange-rates-sidebar-card

        // Quick Info Card
        echo '<div class="ledoviz-turkish-exchange-rates-sidebar-card" style="border-top-color: #72777c;">';
        echo '<h3>' . esc_html__( 'Eklenti Bilgileri', 'ledoviz-turkish-exchange-rates' ) . '</h3>';
        echo '<p>';
        echo '<strong>' . esc_html__( 'Sürüm:', 'ledoviz-turkish-exchange-rates' ) . '</strong> ' . esc_html( EL_DOVIZ_VERSION ) . '<br>';
        echo '<strong>' . esc_html__( 'Geliştirici:', 'ledoviz-turkish-exchange-rates' ) . '</strong> <a href="https://miiiira.com" target="_blank">Mira</a><br>';
        echo '<strong>' . esc_html__( 'Lisans:', 'ledoviz-turkish-exchange-rates' ) . '</strong> GPLv2<br>';
        echo '<strong>' . esc_html__( 'Veri Kaynağı:', 'ledoviz-turkish-exchange-rates' ) . '</strong> ' . esc_html__( 'TCMB (T.C. Merkez Bankası)', 'ledoviz-turkish-exchange-rates' );
        echo '</p>';
        echo '</div>'; // .ledoviz-turkish-exchange-rates-sidebar-card
        
        echo '</div>'; // .ledoviz-turkish-exchange-rates-sidebar-column

        echo '</div>'; // .ledoviz-turkish-exchange-rates-dashboard-layout
        echo '</div>'; // .wrap
    }
}
?>
