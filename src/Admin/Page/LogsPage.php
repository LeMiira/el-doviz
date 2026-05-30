<?php
namespace ElDoviz\Admin\Page;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Logs page to display local debug logs.
 */
class LogsPage extends AdminPage {
    public static function render() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Eklenti Günlükleri', 'el-doviz' ) . '</h1>';
        echo '<p>' . esc_html__( 'Hata ayıklama günlüğü dosyası içeriklerini görüntüleyin. Ayarlar altında Hata Ayıklama Modu etkin olmalıdır.', 'el-doviz' ) . '</p>';

        $upload_dir = wp_upload_dir();
        $log_file   = trailingslashit( $upload_dir['basedir'] ) . 'ledoviz-turkish-exchange-rates/logs/debug.log';

        echo '<div class="card" style="max-width: 900px; padding: 20px; border-left: 4px solid #C41E3A;">';
        echo '<h2>' . esc_html__( 'Hata Ayıklama Günlüğü Görüntüleyici', 'el-doviz' ) . '</h2>';

        if ( file_exists( $log_file ) ) {
            $logs = file_get_contents( $log_file );
            echo '<textarea readonly style="width: 100%; height: 350px; font-family: monospace; background: #272822; color: #f8f8f2; padding: 15px; border-radius: 4px;">';
            echo esc_textarea( $logs );
            echo '</textarea>';
        } else {
            echo '<p>' . esc_html__( 'Kayıtlı günlük bulunmamaktadır veya Hata Ayıklama Modu devre dışıdır.', 'el-doviz' ) . '</p>';
        }
        echo '</div>';
        echo '</div>';
    }
}
?>
