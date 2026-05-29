<?php
namespace ElDoviz\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class ShortcodeGenerator
 * Handles shortcode registration and rendering by routing to existing block render callbacks.
 */
class ShortcodeGenerator {
    /**
     * Register all shortcodes.
     */
    public static function register() {
        add_shortcode( 'ledoviz_turkish_exchange_rates_exchange_rates', [ self::class, 'render_exchange_rates' ] );
        add_shortcode( 'ledoviz_turkish_exchange_rates_ticker', [ self::class, 'render_ticker' ] );
        add_shortcode( 'ledoviz_turkish_exchange_rates_privacy', [ self::class, 'render_privacy' ] );
    }

    /**
     * Render exchange rates shortcode.
     *
     * @param array|string $atts Shortcode attributes.
     * @return string HTML output.
     */
    public static function render_exchange_rates( $atts ) {
        $atts = shortcode_atts( [
            'currencies' => 'usd,eur,gbp',
            'layout'     => 'list',
            'theme'      => 'auto',
        ], $atts, 'ledoviz_turkish_exchange_rates_exchange_rates' );

        // Ensure functions are loaded.
        if ( ! function_exists( 'ledoviz_turkish_exchange_rates_render_exchange_rates' ) ) {
            require_once dirname( __DIR__ ) . '/Block/exchange-rates/render.php';
        }

        return ledoviz_turkish_exchange_rates_render_exchange_rates( $atts );
    }

    /**
     * Render ticker shortcode.
     *
     * @param array|string $atts Shortcode attributes.
     * @return string HTML output.
     */
    public static function render_ticker( $atts ) {
        $atts = shortcode_atts( [
            'currencies' => 'usd,eur,gbp',
            'speed'      => 5000,
        ], $atts, 'ledoviz_turkish_exchange_rates_ticker' );

        // Ensure functions are loaded.
        if ( ! function_exists( 'ledoviz_turkish_exchange_rates_render_ticker' ) ) {
            require_once dirname( __DIR__ ) . '/Block/ticker/render.php';
        }

        return ledoviz_turkish_exchange_rates_render_ticker( $atts );
    }

    /**
     * Render privacy/KVKK disclosure shortcode.
     *
     * @return string HTML output.
     */
    public static function render_privacy() {
        $text = esc_html__( 'Bu sitede gösterilen tüm döviz kurları ve finansal veriler, Türkiye Cumhuriyet Merkez Bankası (TCMB) tarafından sağlanan halka açık verilerden alınmakta olup yalnızca bilgilendirme amaçlıdır. Verilerin kesin doğruluğu veya anlık güncelliği garanti edilmez. Kişisel verileriniz KVKK kapsamında korunmakta olup, bu eklenti aracılığıyla hiçbir kişisel ziyaretçi verisi toplanmamakta veya işlenmemektedir.', 'ledoviz-turkish-exchange-rates' );
        return '<div class="ledoviz-turkish-exchange-rates-privacy-container">' . $text . '</div>';
    }
}
?>
