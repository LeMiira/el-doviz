<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Render callback for the Live Ticker Gutenberg block.
 *
 * @param array $attributes Block attributes.
 * @return string HTML output.
 */
function el_doviz_render_ticker( $attributes ) {
    $currencies = isset( $attributes['currencies'] ) ? $attributes['currencies'] : 'usd,eur,gbp';
    $speed      = isset( $attributes['speed'] ) ? (int) $attributes['speed'] : 5000;
    $list       = array_map( 'trim', explode( ',', $currencies ) );

    $fetcher = new \ElDoviz\Service\DataFetcher( new \ElDoviz\Service\CacheManager() );
    $rates   = $fetcher->fetch( 'tcmb', HOUR_IN_SECONDS );
    if ( is_wp_error( $rates ) ) {
        return '<p>' . esc_html__( 'Kurlar yüklenemedi.', 'el-doviz' ) . '</p>';
    }

    $items = [];
    foreach ( $list as $code ) {
        $code_lc = strtolower( $code );
        if ( isset( $rates[ $code_lc ] ) ) {
            $rate = number_format_i18n( $rates[ $code_lc ], 4 );
            $items[] = sprintf( '%s: %s', esc_html( strtoupper( $code ) ), esc_html( $rate ) );
        }
    }
    $ticker_text = implode( ' | ', $items );

    // Inline CSS animation for ticker (respect prefers-reduced-motion).
    $animation_css = sprintf(
        '<style>.el-doviz-ticker { overflow: hidden; white-space: nowrap; }
        @media (prefers-reduced-motion: no-preference) { .el-doviz-ticker span { display: inline-block; padding-right: 2rem; animation: el-doviz-scroll %dms linear infinite; } }
        @keyframes el-doviz-scroll { 0%% { transform: translateX(100%%); } 100%% { transform: translateX(-100%%); } }
        </style>',
        $speed * max( 1, count( $items ) )
    );

    return $animation_css . '<div class="el-doviz-ticker" aria-live="polite"><span>' . $ticker_text . '</span></div>';
}
?>
