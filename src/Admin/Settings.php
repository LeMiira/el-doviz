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
    const OPTION_NAME = 'el_doviz_options';

    /**
     * Register settings, sections and fields.
     */
    public function register_settings() {
        register_setting( 'el_doviz_settings_group', self::OPTION_NAME, [
            'type'              => 'array',
            'description'       => esc_html__( 'El Döviz eklenti seçenekleri', 'el-doviz' ),
            'sanitize_callback' => [ $this, 'sanitize_options' ],
            'default'           => [],
        ] );

        add_settings_section(
            'el_doviz_general_section',
            esc_html__( 'Genel Ayarlar', 'el-doviz' ),
            function () {
                echo '<p>' . esc_html__( 'El Döviz eklentisinin genel davranışını yapılandırın.', 'el-doviz' ) . '</p>'; },
            'el-doviz-settings'
        );

        add_settings_field(
            'enable_header_ticker',
            esc_html__( 'Üst Bilgi Bandını Etkinleştir', 'el-doviz' ),
            [ $this, 'field_enable_header_ticker' ],
            'el-doviz-settings',
            'el_doviz_general_section'
        );

        // Debug mode toggle – writes to log when enabled.
        add_settings_field(
            'debug_mode',
            esc_html__( 'Hata Ayıklama Modu', 'el-doviz' ),
            [ $this, 'field_debug_mode' ],
            'el-doviz-settings',
            'el_doviz_general_section'
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
        echo '<label for="enable_header_ticker">' . esc_html__( 'Site üst bilgisinde döviz kuru bandını göster.', 'el-doviz' ) . '</label>';
    }

    // New debug mode field
    public function field_debug_mode() {
        $options = get_option( self::OPTION_NAME );
        $value   = isset( $options['debug_mode'] ) ? (int) $options['debug_mode'] : 0;
        echo '<input type="checkbox" id="debug_mode" name="' . esc_attr( self::OPTION_NAME ) . '[debug_mode]" value="1" ' . checked( 1, $value, false ) . ' />';
        echo '<label for="debug_mode">' . esc_html__( 'Hata ayıklama günlüğünü etkinleştir (uploads/el-doviz/logs/debug.log dosyasına yazar).', 'el-doviz' ) . '</label>';
    }

    /**
     * Add admin menu items.
     */
    public static function add_admin_menu() {
        add_menu_page(
            esc_html__( 'El Döviz', 'el-doviz' ),
            esc_html__( 'El Döviz', 'el-doviz' ),
            'manage_options',
            'el-doviz',
            [ __CLASS__, 'render_dashboard' ],
            'dashicons-chart-pie',
            81
        );
        add_submenu_page( 'el-doviz', esc_html__( 'Panel', 'el-doviz' ), esc_html__( 'Panel', 'el-doviz' ), 'manage_options', 'el-doviz', [ __CLASS__, 'render_dashboard' ] );
        add_submenu_page( 'el-doviz', esc_html__( 'Günlükler', 'el-doviz' ), esc_html__( 'Günlükler', 'el-doviz' ), 'manage_options', 'el-doviz-logs', [ \ElDoviz\Admin\Page\LogsPage::class, 'render' ] );
    }

    public static function render_dashboard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Yetersiz yetki.', 'el-doviz' ) );
        }

        // Handle tools operations (e.g. clear cache) if post request is sent
        if ( isset( $_POST['el_doviz_clear_cache'] ) && check_admin_referer( 'el_doviz_tools_action', 'el_doviz_nonce' ) ) {
            $cache = new \ElDoviz\Service\CacheManager();
            $cache->delete( 'tcmb' );
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Önbellek başarıyla temizlendi.', 'el-doviz' ) . '</p></div>';
        }

        // Active tab detection
        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'usage';
        $allowed_tabs = [ 'usage', 'settings', 'tools' ];
        if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
            $active_tab = 'usage';
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'El Döviz Kontrol Paneli', 'el-doviz' ) . '</h1>';

        // Styling for layout and support cards
        echo '<style>
            .el-doviz-dashboard-layout {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                max-width: 1200px;
                margin-top: 20px;
                align-items: flex-start;
            }
            .el-doviz-main-column {
                flex: 3;
                min-width: 320px;
            }
            .el-doviz-sidebar-column {
                flex: 1;
                min-width: 280px;
                max-width: 360px;
            }
            .el-doviz-sidebar-card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-top: 4px solid #C41E3A;
                padding: 20px;
                border-radius: 4px;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
                margin-bottom: 20px;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .el-doviz-sidebar-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            }
            .el-doviz-sidebar-card h3 {
                margin-top: 0;
                margin-bottom: 12px;
                color: #23282d;
                font-size: 1.1rem;
                font-weight: 600;
            }
            .el-doviz-sidebar-card p {
                color: #555d66;
                font-size: 0.9rem;
                line-height: 1.5;
                margin-bottom: 15px;
            }
            .el-doviz-btn-group {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .el-doviz-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 16px;
                border-radius: 4px;
                font-weight: 600;
                font-size: 0.9rem;
                text-decoration: none !important;
                transition: all 0.2s ease-in-out;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            .el-doviz-btn-github {
                background: #24292e;
                color: #fff !important;
            }
            .el-doviz-btn-github:hover {
                background: #1b1f23;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(36, 41, 46, 0.15);
            }
            .el-doviz-btn-coffee {
                background: #FFDD00;
                color: #000000 !important;
            }
            .el-doviz-btn-coffee:hover {
                background: #ffea00;
                transform: translateY(-1px);
                box-shadow: 0 4px 6px rgba(255, 221, 0, 0.25);
            }
            @media (max-width: 900px) {
                .el-doviz-dashboard-layout {
                    flex-direction: column;
                }
                .el-doviz-sidebar-column {
                    max-width: 100%;
                    width: 100%;
                }
            }
        </style>';

        echo '<div class="el-doviz-dashboard-layout">';
        echo '<div class="el-doviz-main-column">';

        // KVKK warning alert block
        echo '<div class="el-doviz-kvkk-alert" style="background: #fff8e5; border: 1px solid #ffeb3b; border-left: 4px solid #ffb900; padding: 15px; margin: 15px 0 25px 0; border-radius: 4px; display: flex; align-items: center; max-width: 1000px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
        echo '<span class="dashicons dashicons-shield" style="font-size: 24px; width: 24px; height: 24px; color: #ffb900; margin-right: 12px; display: flex; align-items: center;"></span>';
        echo '<div style="font-size: 0.95rem; line-height: 1.5; color: #665c40;">';
        echo '<strong>' . esc_html__( 'KVKK ve Veri Güvenliği Bildirimi:', 'el-doviz' ) . '</strong> ';
        echo esc_html__( 'Bu eklenti, Türkiye Cumhuriyeti Merkez Bankası (TCMB) verilerini kullanarak döviz kurlarını gösterir. Eklenti aracılığıyla ziyaretçilerinize ait hiçbir kişisel veri toplanmaz, işlenmez veya üçüncü şahıslarla paylaşılmaz. KVKK uyumluluğu için sayfalarınıza "El Döviz Gizlilik ve KVKK" bileşenini veya [el_doviz_privacy] kısa kodunu ekleyebilirsiniz.', 'el-doviz' );
        echo '</div>';
        echo '</div>';

        // Render Premium Navigation Tabs
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="' . esc_url( admin_url( 'admin.php?page=el-doviz&tab=usage' ) ) . '" class="nav-tab ' . ( 'usage' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Kullanım Rehberi', 'el-doviz' ) . '</a>';
        echo '<a href="' . esc_url( admin_url( 'admin.php?page=el-doviz&tab=settings' ) ) . '" class="nav-tab ' . ( 'settings' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Genel Ayarlar', 'el-doviz' ) . '</a>';
        echo '<a href="' . esc_url( admin_url( 'admin.php?page=el-doviz&tab=tools' ) ) . '" class="nav-tab ' . ( 'tools' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Araçlar', 'el-doviz' ) . '</a>';
        echo '</h2>';

        echo '<div class="tab-content" style="margin-top: 20px;">';

        if ( 'usage' === $active_tab ) {
            // Documentations content
            echo '<div style="max-width: 1000px;">';
            echo '<p style="font-size: 1.15rem; color: #555;">' . esc_html__( 'El Döviz eklentisinin tüm özelliklerini, kısa kodlarını ve entegrasyonlarını bu sayfada bulabilirsiniz.', 'el-doviz' ) . '</p>';

            echo '<style>
                .el-doviz-doc-section {
                    background: #fff;
                    border: 1px solid #ccd0d4;
                    border-left: 4px solid #C41E3A;
                    padding: 20px;
                    margin-bottom: 25px;
                    border-radius: 4px;
                    box-shadow: 0 1px 1px rgba(0,0,0,.04);
                }
                .el-doviz-doc-section h2 {
                    margin-top: 0;
                    color: #23282d;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 10px;
                }
                .el-doviz-doc-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                    margin-top: 15px;
                }
                .el-doviz-doc-card {
                    background: #f9f9f9;
                    border: 1px solid #e5e5e5;
                    padding: 15px;
                    border-radius: 4px;
                }
                .el-doviz-doc-card h3 {
                    margin-top: 0;
                    color: #C41E3A;
                }
                .el-doviz-code-box {
                    background: #272822;
                    color: #f8f8f2;
                    padding: 8px 12px;
                    border-radius: 4px;
                    font-family: monospace;
                    display: inline-block;
                    margin: 5px 0;
                }
                .el-doviz-badge {
                    background: #e1f0fa;
                    color: #0073aa;
                    padding: 3px 8px;
                    border-radius: 3px;
                    font-size: 0.85rem;
                    font-weight: 600;
                    display: inline-block;
                    margin-bottom: 8px;
                }
            </style>';

            // Section 1: Shortcodes
            echo '<div class="el-doviz-doc-section">';
            echo '<h2>' . esc_html__( 'Kısa Kodlar (Shortcodes)', 'el-doviz' ) . '</h2>';
            echo '<p>' . esc_html__( 'Eklentinin sunduğu özellikleri herhangi bir yazı veya sayfaya eklemek için aşağıdaki kısa kodları kullanabilirsiniz.', 'el-doviz' ) . '</p>';
            echo '<div class="el-doviz-doc-grid">';
            
            echo '<div class="el-doviz-doc-card">';
            echo '<h3>' . esc_html__( 'Döviz Kurları Listesi', 'el-doviz' ) . '</h3>';
            echo '<div class="el-doviz-code-box">[el_doviz_exchange_rates]</div>';
            echo '<p>' . esc_html__( 'Belirtilen para birimlerini ve endeksleri liste veya ızgara biçiminde gösterir.', 'el-doviz' ) . '</p>';
            echo '<p><strong>' . esc_html__( 'Parametreler:', 'el-doviz' ) . '</strong><br>';
            echo '<code>currencies</code>: ' . esc_html__( 'Gösterilecek para birimleri (örn: usd,eur,gbp,bist)', 'el-doviz' ) . '<br>';
            echo '<code>layout</code>: ' . esc_html__( 'Görünüm biçimi (list veya grid)', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '<div class="el-doviz-doc-card">';
            echo '<h3>' . esc_html__( 'Canlı Kur Bandı', 'el-doviz' ) . '</h3>';
            echo '<div class="el-doviz-code-box">[el_doviz_ticker]</div>';
            echo '<p>' . esc_html__( 'Sitenin üst veya alt kısmında kayan döviz kurları bandı görüntüler.', 'el-doviz' ) . '</p>';
            echo '<p><strong>' . esc_html__( 'Parametreler:', 'el-doviz' ) . '</strong><br>';
            echo '<code>currencies</code>: ' . esc_html__( 'Kayan bantta yer alacak birimler (örn: usd,eur,bist)', 'el-doviz' ) . '<br>';
            echo '<code>speed</code>: ' . esc_html__( 'Milisaniye cinsinden döngü hızı (örn: 5000)', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '<div class="el-doviz-doc-card">';
            echo '<h3>' . esc_html__( 'Gizlilik ve KVKK Açıklaması', 'el-doviz' ) . '</h3>';
            echo '<div class="el-doviz-code-box">[el_doviz_privacy]</div>';
            echo '<p>' . esc_html__( 'Kullanıcılara eklenti aracılığıyla hiçbir kişisel verinin işlenmediğini belirten KVKK uyarı metnini gösterir.', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '</div>';
            echo '</div>';

            // Section 2: Elementor
            echo '<div class="el-doviz-doc-section">';
            echo '<h2>' . esc_html__( 'Elementor Entegrasyonu', 'el-doviz' ) . '</h2>';
            echo '<p>' . esc_html__( 'Elementor Düzenleyicide "El Döviz" kategorisi altındaki bileşenleri sürükleyip bırakarak sayfalarınıza ekleyebilirsiniz.', 'el-doviz' ) . '</p>';
            echo '<div class="el-doviz-doc-grid">';

            echo '<div class="el-doviz-doc-card">';
            echo '<span class="el-doviz-badge">Elementor Widget</span>';
            echo '<h3>' . esc_html__( 'El Döviz Kurları ve Endeksler', 'el-doviz' ) . '</h3>';
            echo '<p>' . esc_html__( 'Gelişmiş liste veya ızgara düzeni seçimi. Stil sekmesinden etiket ve değerlerin tipografi, renk, kenarlık ve kutu gölgesi ayarlarını dinamik olarak değiştirebilirsiniz.', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '<div class="el-doviz-doc-card">';
            echo '<span class="el-doviz-badge">Elementor Widget</span>';
            echo '<h3>' . esc_html__( 'El Döviz Canlı Kur Bandı', 'el-doviz' ) . '</h3>';
            echo '<p>' . esc_html__( 'Canlı kurları kayan yazı bandı şeklinde eklemenizi sağlar. Kayan yazı hızı ve yazı tipi renkleri düzenlenebilir.', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '<div class="el-doviz-doc-card">';
            echo '<span class="el-doviz-badge">Elementor Widget</span>';
            echo '<h3>' . esc_html__( 'El Döviz Gizlilik ve KVKK', 'el-doviz' ) . '</h3>';
            echo '<p>' . esc_html__( 'Gizlilik ve veri sorumluluğu beyan metnini sayfalarınıza ekler. Yazı rengi, arka planı ve kutu tasarımı özelleştirilebilir.', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '</div>';
            echo '</div>';

            // Section 3: Gutenberg & Widgets
            echo '<div class="el-doviz-doc-section">';
            echo '<h2>' . esc_html__( 'Gutenberg ve Yan Menü Bileşenleri', 'el-doviz' ) . '</h2>';
            echo '<div class="el-doviz-doc-grid">';

            echo '<div class="el-doviz-doc-card">';
            echo '<h3>' . esc_html__( 'Gutenberg Blokları', 'el-doviz' ) . '</h3>';
            echo '<p>' . esc_html__( 'WordPress blok düzenleyicisinde "Döviz Kurları" veya "Canlı Kur Bandı" bloklarını aratarak yazılarınıza kolayca yerleştirebilirsiniz.', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '<div class="el-doviz-doc-card">';
            echo '<h3>' . esc_html__( 'Yan Menü Bileşenleri (Widgets)', 'el-doviz' ) . '</h3>';
            echo '<p>' . esc_html__( 'Görünüm > Bileşenler sayfasına giderek "El Döviz Yan Menü Bileşeni"ni sitenizin aktif yan menü alanlarına (sidebar) ekleyebilirsiniz.', 'el-doviz' ) . '</p>';
            echo '</div>';

            echo '</div>';
            echo '</div>';

            // Section 4: Developer Filters
            echo '<div class="el-doviz-doc-section">';
            echo '<h2>' . esc_html__( 'Geliştirici Filtreleri (Filters)', 'el-doviz' ) . '</h2>';
            echo '<p>' . esc_html__( 'Yazılımcılar ve temalar için sunulan kanca (filter) kütüphanesi:', 'el-doviz' ) . '</p>';
            echo '<ul style="list-style: disc; padding-left: 20px;">';
            echo '<li><code>el_doviz_api_endpoints</code>: ' . esc_html__( 'TCMB ve BIST 100 API veri kaynağı adreslerini değiştirmek için kullanılır.', 'el-doviz' ) . '</li>';
            echo '</ul>';
            echo '</div>';

            echo '</div>';
        } elseif ( 'settings' === $active_tab ) {
            // Settings Form Page
            echo '<form method="post" action="options.php">';
            settings_fields( 'el_doviz_settings_group' );
            do_settings_sections( 'el-doviz-settings' );
            submit_button();
            echo '</form>';
        } elseif ( 'tools' === $active_tab ) {
            // Tools/Diagnostics Page
            echo '<div class="card" style="max-width: 600px; padding: 20px; border-left: 4px solid #C41E3A; background: #fff; border-top: 1px solid #ccd0d4; border-right: 1px solid #ccd0d4; border-bottom: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
            echo '<h2>' . esc_html__( 'Teşhis ve Testler', 'el-doviz' ) . '</h2>';
            echo '<form method="post" action="">';
            wp_nonce_field( 'el_doviz_tools_action', 'el_doviz_nonce' );
            echo '<p>' . esc_html__( 'TCMB API uç noktalarından anında yeni kurların alınması için önbelleğin süresini dolmaya zorlayın.', 'el-doviz' ) . '</p>';
            submit_button( esc_html__( 'Önbelleği Temizle', 'el-doviz' ), 'primary', 'el_doviz_clear_cache', false );
            echo '</form>';
            echo '</div>';
        }

        echo '</div>'; // .tab-content
        echo '</div>'; // .el-doviz-main-column

        // Sidebar Column
        echo '<div class="el-doviz-sidebar-column">';
        
        // Support Card
        echo '<div class="el-doviz-sidebar-card">';
        echo '<h3>' . esc_html__( 'Eklentiyi Destekleyin', 'el-doviz' ) . '</h3>';
        echo '<p>' . esc_html__( 'El Döviz eklentisini beğendiyseniz, geliştirilmesine katkıda bulunmak ve yeni özelliklerin eklenmesini desteklemek için bağış yapabilirsiniz.', 'el-doviz' ) . '</p>';
        echo '<div class="el-doviz-btn-group">';
        
        // GitHub Sponsors Button
        echo '<a href="https://github.com/sponsors/LeMiira" target="_blank" class="el-doviz-btn el-doviz-btn-github">';
        echo '<svg viewBox="0 0 16 16" width="18" height="18" fill="currentColor" style="margin-right: 8px;"><path fill-rule="evenodd" d="M8 1.482c4.847-3.817 12.23 2.196 0 11.625-12.23-9.429-4.847-15.442 0-11.625z"/></svg>';
        echo esc_html__( 'GitHub Sponsors', 'el-doviz' );
        echo '</a>';
        
        // Buy Me a Coffee Button
        echo '<a href="https://www.buymeacoffee.com/miiiira" target="_blank" class="el-doviz-btn el-doviz-btn-coffee">';
        echo '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>';
        echo esc_html__( 'Buy Me a Coffee', 'el-doviz' );
        echo '</a>';
        
        echo '</div>'; // .el-doviz-btn-group
        echo '</div>'; // .el-doviz-sidebar-card

        // Quick Info Card
        echo '<div class="el-doviz-sidebar-card" style="border-top-color: #72777c;">';
        echo '<h3>' . esc_html__( 'Eklenti Bilgileri', 'el-doviz' ) . '</h3>';
        echo '<p>';
        echo '<strong>' . esc_html__( 'Sürüm:', 'el-doviz' ) . '</strong> ' . esc_html( EL_DOVIZ_VERSION ) . '<br>';
        echo '<strong>' . esc_html__( 'Geliştirici:', 'el-doviz' ) . '</strong> <a href="https://miiiira.com" target="_blank">Mira</a><br>';
        echo '<strong>' . esc_html__( 'Lisans:', 'el-doviz' ) . '</strong> GPLv2<br>';
        echo '<strong>' . esc_html__( 'Veri Kaynağı:', 'el-doviz' ) . '</strong> ' . esc_html__( 'TCMB (T.C. Merkez Bankası)', 'el-doviz' );
        echo '</p>';
        echo '</div>'; // .el-doviz-sidebar-card
        
        echo '</div>'; // .el-doviz-sidebar-column

        echo '</div>'; // .el-doviz-dashboard-layout
        echo '</div>'; // .wrap
    }
}
?>
