<?php
namespace ElDoviz\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Elementor widget for a live ticker of selected rates/indices.
 */
class TickerWidget extends Widget_Base {
    public function get_name() {
        return 'el_doviz_ticker';
    }

    public function get_title() {
        return esc_html__( 'El Döviz Canlı Kur Bandı', 'el-doviz' );
    }

    public function get_icon() {
        return 'eicon-chevron-right';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function _register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'İçerik', 'el-doviz' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'currencies',
            [
                'label'       => esc_html__( 'Para Birimi/Endeks Seçin', 'el-doviz' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'default'     => [ 'usd', 'eur', 'gbp', 'bist' ],
                'options'     => [
                    'usd'  => esc_html__( 'USD (Amerikan Doları)', 'el-doviz' ),
                    'eur'  => esc_html__( 'EUR (Euro)', 'el-doviz' ),
                    'gbp'  => esc_html__( 'GBP (İngiliz Sterlini)', 'el-doviz' ),
                    'chf'  => esc_html__( 'CHF (İsviçre Frangı)', 'el-doviz' ),
                    'cad'  => esc_html__( 'CAD (Kanada Doları)', 'el-doviz' ),
                    'aud'  => esc_html__( 'AUD (Avustralya Doları)', 'el-doviz' ),
                    'dkk'  => esc_html__( 'DKK (Danimarka Kronu)', 'el-doviz' ),
                    'sek'  => esc_html__( 'SEK (İsveç Kronu)', 'el-doviz' ),
                    'nok'  => esc_html__( 'NOK (Norveç Kronu)', 'el-doviz' ),
                    'sar'  => esc_html__( 'SAR (Suudi Arabistan Riyali)', 'el-doviz' ),
                    'jpy'  => esc_html__( 'JPY (Japon Yeni)', 'el-doviz' ),
                    'rub'  => esc_html__( 'RUB (Rus Rublesi)', 'el-doviz' ),
                    'kwd'  => esc_html__( 'KWD (Kuveyt Dinarı)', 'el-doviz' ),
                    'bist' => esc_html__( 'BIST 100 Endeksi', 'el-doviz' ),
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'speed',
            [
                'label'   => esc_html__( 'Bant Hızı (ms)', 'el-doviz' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 5000,
                'min'     => 1000,
                'step'    => 500,
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__( 'Stil Seçenekleri', 'el-doviz' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__( 'Metin Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-ticker' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Arka Plan Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-ticker' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                'selector' => '{{WRAPPER}} .el-doviz-ticker',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $selected = ! empty( $settings['currencies'] ) ? $settings['currencies'] : [];
        if ( empty( $selected ) ) {
            return;
        }
        $speed = intval( $settings['speed'] );

        $fetcher   = new \ElDoviz\Service\DataFetcher( new \ElDoviz\Service\CacheManager() );
        $rates     = $fetcher->fetch( 'tcmb', HOUR_IN_SECONDS );
        $bist_data = $fetcher->fetch( 'bist', HOUR_IN_SECONDS );

        $items = [];
        foreach ( $selected as $code ) {
            $code_lc = strtolower( $code );
            if ( 'bist' === $code_lc ) {
                if ( ! is_wp_error( $bist_data ) && isset( $bist_data['bist100'] ) ) {
                    $val = number_format_i18n( $bist_data['bist100'], 2 );
                    $items[] = sprintf( '%s: %s', esc_html__( 'BIST 100', 'el-doviz' ), esc_html( $val ) );
                }
            } else {
                if ( ! is_wp_error( $rates ) && isset( $rates[ $code_lc ] ) ) {
                    $val = number_format_i18n( $rates[ $code_lc ], 4 );
                    $items[] = sprintf( '%s: %s', esc_html( strtoupper( $code ) ), esc_html( $val ) );
                }
            }
        }
        $ticker_text = implode( ' | ', $items );

        $animation_css = sprintf(
            '<style>.el-doviz-ticker { overflow: hidden; white-space: nowrap; padding: 10px; }
            .el-doviz-ticker span { display: inline-block; padding-right: 2rem; animation: el-doviz-scroll %dms linear infinite; }
            @keyframes el-doviz-scroll { 0%% { transform: translateX(100%%); } 100%% { transform: translateX(-100%%); } }
            </style>',
            $speed * max( 1, count( $items ) )
        );
        echo wp_kses( $animation_css, [ 'style' => [] ] );
        echo '<div class="el-doviz-ticker" aria-live="polite"><span>' . esc_html( $ticker_text ) . '</span></div>';
    }
}
?>
