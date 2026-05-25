<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Render callback for the Exchange Rates Gutenberg block.
 *
 * @param array $attributes Block attributes.
 * @return string HTML output.
 */
function el_doviz_render_exchange_rates( $attributes ) {
    // Retrieve data via DataFetcher.
    if ( ! class_exists( 'ElDoviz\Service\DataFetcher' ) ) {
        return '';
    }
    $fetcher = new \ElDoviz\Service\DataFetcher( new \ElDoviz\Service\CacheManager() );
    $rates   = $fetcher->fetch( 'tcmb', HOUR_IN_SECONDS );
    if ( is_wp_error( $rates ) ) {
        return '<p>' . esc_html__( 'Döviz kurları yüklenemedi.', 'el-doviz' ) . '</p>';
    }

    $currencies = array_map( 'trim', explode( ',', $attributes['currencies'] ) );
    $output = '<section class="el-doviz-exchange-rates" itemscope itemtype="https://schema.org/FinancialProduct"><ul>';
    foreach ( $currencies as $code ) {
        $code_lc = strtolower( $code );
        if ( isset( $rates[ $code_lc ] ) ) {
            $rate = number_format_i18n( $rates[ $code_lc ], 4 );
            $output .= sprintf(
                '<li><span class="currency-code">%s</span>: <span class="currency-rate">%s</span></li>',
                esc_html( strtoupper( $code ) ),
                esc_html( $rate )
            );
        }
    }
    $output .= '</ul></section>';
    return $output;
}
?>
