<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Render callback for the Privacy Gutenberg block.
 *
 * @param array $attributes Block attributes.
 * @return string HTML output.
 */
function ledoviz_turkish_exchange_rates_render_privacy( $attributes ) {
    $default_text = esc_html__( 'Bu sitede gösterilen tüm döviz kurları ve finansal veriler, Türkiye Cumhuriyet Merkez Bankası (TCMB) tarafından sağlanan halka açık verilerden alınmakta olup yalnızca bilgilendirme amaçlıdır. Verilerin kesin doğruluğu veya anlık güncelliği garanti edilmez. Kişisel verileriniz KVKK kapsamında korunmakta olup, bu eklenti aracılığıyla hiçbir kişisel ziyaretçi verisi toplanmamakta veya işlenmemektedir.', 'el-doviz' );
    
    $text = isset( $attributes['privacy_text'] ) && ! empty( $attributes['privacy_text'] ) ? $attributes['privacy_text'] : $default_text;
    $align = isset( $attributes['align'] ) ? $attributes['align'] : 'left';

    $output = '<div class="ledoviz-turkish-exchange-rates-privacy-container" style="text-align: ' . esc_attr( $align ) . ';">';
    $output .= wp_kses_post( $text );
    $output .= '</div>';

    return $output;
}
?>
