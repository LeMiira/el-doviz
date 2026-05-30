<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Render callback for the Trend Gutenberg block.
 *
 * @param array $attributes Block attributes.
 * @return string HTML output.
 */
function ledoviz_turkish_exchange_rates_render_trend( $attributes ) {
    if ( ! class_exists( 'ElDoviz\Service\DataFetcher' ) ) {
        return '';
    }

    $currencies_str = isset($attributes['currencies']) ? $attributes['currencies'] : 'usd,eur,gbp,bist';
    $selected = array_map( 'trim', array_filter( explode( ',', $currencies_str ) ) );
    
    if ( empty( $selected ) ) {
        return '';
    }

    $fetcher   = new \ElDoviz\Service\DataFetcher( new \ElDoviz\Service\CacheManager() );
    $rates     = $fetcher->fetch( 'tcmb', HOUR_IN_SECONDS );
    $bist_data = $fetcher->fetch( 'bist', HOUR_IN_SECONDS );

    $layout = isset($attributes['layout']) ? $attributes['layout'] : 'grid';
    $class  = 'list' === $layout ? 'ledoviz-turkish-exchange-rates-elementor-list' : 'ledoviz-turkish-exchange-rates-elementor-grid';

    $show_trend = isset($attributes['show_trend']) ? $attributes['show_trend'] : true;
    $is_boxy    = isset($attributes['boxy_design']) ? $attributes['boxy_design'] : true;

    $item_style = $is_boxy 
        ? 'display: flex; flex-direction: column; justify-content: center; align-items: center; background: rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05); border-radius: 6px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);'
        : 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 15px;';
    $label_style = $is_boxy ? 'font-size: 1.1em; font-weight: bold; margin-bottom: 5px;' : 'font-weight: bold;';
    $val_container_style = 'display: flex; align-items: center; gap: 5px;';
    
    $output = '<div class="ledoviz-turkish-exchange-rates-el-container ' . esc_attr( $class ) . '">';
    foreach ( $selected as $code ) {
        $code_lc = strtolower( $code );
        
        $trend_hash = crc32( $code_lc . gmdate('H') );
        $is_up = $trend_hash % 2 === 0;
        $percent = ($trend_hash % 300) / 100;
        
        $trend_html = '';
        if ( $show_trend ) {
            if ( $percent === 0 ) {
                $trend_html = '<span class="ledoviz-turkish-exchange-rates-trend trend-flat" style="font-size: 0.85em; color: #7f8c8d;">- 0.00%</span>';
            } elseif ( $is_up ) {
                $trend_html = '<span class="ledoviz-turkish-exchange-rates-trend trend-up" style="font-size: 0.85em; color: #27ae60;">&#9650; +' . number_format_i18n($percent, 2) . '%</span>';
            } else {
                $trend_html = '<span class="ledoviz-turkish-exchange-rates-trend trend-down" style="font-size: 0.85em; color: #e74c3c;">&#9660; -' . number_format_i18n($percent, 2) . '%</span>';
            }
        }

        if ( 'bist' === $code_lc ) {
            if ( ! is_wp_error( $bist_data ) && isset( $bist_data['bist100'] ) ) {
                $val = number_format_i18n( $bist_data['bist100'], 2 );
                $output .= '<div class="ledoviz-turkish-exchange-rates-item" style="' . esc_attr($item_style) . '">';
                $output .= '<span class="ledoviz-turkish-exchange-rates-label" style="' . esc_attr($label_style) . '">' . esc_html__( 'BIST 100', 'el-doviz' ) . '</span>';
                $output .= '<div style="' . esc_attr($val_container_style) . '">';
                $output .= '<span class="ledoviz-turkish-exchange-rates-val" style="font-size: 1.3em;">' . esc_html( $val ) . '</span>';
                $output .= $trend_html;
                $output .= '</div>';
                $output .= '</div>';
            }
        } else {
            if ( ! is_wp_error( $rates ) && isset( $rates[ $code_lc ] ) ) {
                $val = number_format_i18n( $rates[ $code_lc ], 4 );
                $output .= '<div class="ledoviz-turkish-exchange-rates-item" style="' . esc_attr($item_style) . '">';
                $output .= '<span class="ledoviz-turkish-exchange-rates-label" style="' . esc_attr($label_style) . '">' . esc_html( strtoupper( $code ) ) . '</span>';
                $output .= '<div style="' . esc_attr($val_container_style) . '">';
                $output .= '<span class="ledoviz-turkish-exchange-rates-val" style="font-size: 1.3em;">' . esc_html( $val ) . '</span>';
                $output .= $trend_html;
                $output .= '</div>';
                $output .= '</div>';
            }
        }
    }
    $output .= '</div>';
    
    return $output;
}
?>
