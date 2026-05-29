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
        return 'ledoviz_turkish_exchange_rates_ticker';
    }

    public function get_title() {
        return esc_html__( 'El Döviz Canlı Kur Bandı', 'ledoviz-turkish-exchange-rates' );
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
                'label' => esc_html__( 'İçerik', 'ledoviz-turkish-exchange-rates' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'currencies',
            [
                'label'       => esc_html__( 'Para Birimi/Endeks Seçin', 'ledoviz-turkish-exchange-rates' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'default'     => [ 'usd', 'eur', 'gbp', 'bist' ],
                'options'     => [
                    'usd'  => esc_html__( 'USD (Amerikan Doları)', 'ledoviz-turkish-exchange-rates' ),
                    'eur'  => esc_html__( 'EUR (Euro)', 'ledoviz-turkish-exchange-rates' ),
                    'gbp'  => esc_html__( 'GBP (İngiliz Sterlini)', 'ledoviz-turkish-exchange-rates' ),
                    'chf'  => esc_html__( 'CHF (İsviçre Frangı)', 'ledoviz-turkish-exchange-rates' ),
                    'cad'  => esc_html__( 'CAD (Kanada Doları)', 'ledoviz-turkish-exchange-rates' ),
                    'aud'  => esc_html__( 'AUD (Avustralya Doları)', 'ledoviz-turkish-exchange-rates' ),
                    'dkk'  => esc_html__( 'DKK (Danimarka Kronu)', 'ledoviz-turkish-exchange-rates' ),
                    'sek'  => esc_html__( 'SEK (İsveç Kronu)', 'ledoviz-turkish-exchange-rates' ),
                    'nok'  => esc_html__( 'NOK (Norveç Kronu)', 'ledoviz-turkish-exchange-rates' ),
                    'sar'  => esc_html__( 'SAR (Suudi Arabistan Riyali)', 'ledoviz-turkish-exchange-rates' ),
                    'jpy'  => esc_html__( 'JPY (Japon Yeni)', 'ledoviz-turkish-exchange-rates' ),
                    'rub'  => esc_html__( 'RUB (Rus Rublesi)', 'ledoviz-turkish-exchange-rates' ),
                    'kwd'  => esc_html__( 'KWD (Kuveyt Dinarı)', 'ledoviz-turkish-exchange-rates' ),
                    'bist' => esc_html__( 'BIST 100 Endeksi', 'ledoviz-turkish-exchange-rates' ),
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'speed',
            [
                'label'   => esc_html__( 'Bant Hızı (ms)', 'ledoviz-turkish-exchange-rates' ),
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
                'label' => esc_html__( 'Stil Seçenekleri', 'ledoviz-turkish-exchange-rates' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__( 'Metin Rengi', 'ledoviz-turkish-exchange-rates' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-ticker' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Arka Plan Rengi', 'ledoviz-turkish-exchange-rates' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-ticker' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                'selector' => '{{WRAPPER}} .ledoviz-turkish-exchange-rates-ticker',
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
                    $items[] = sprintf( '%s: %s', esc_html__( 'BIST 100', 'ledoviz-turkish-exchange-rates' ), esc_html( $val ) );
                }
            } else {
                if ( ! is_wp_error( $rates ) && isset( $rates[ $code_lc ] ) ) {
                    $val = number_format_i18n( $rates[ $code_lc ], 4 );
                    $items[] = sprintf( '%s: %s', esc_html( strtoupper( $code ) ), esc_html( $val ) );
                }
            }
        }
        $ticker_text = implode( ' | ', $items );

        $duration = $speed * max( 1, count( $items ) );
        echo '<div class="ledoviz-turkish-exchange-rates-ticker" style="--ledoviz-turkish-exchange-rates-duration: ' . (int) $duration . 'ms; padding: 10px;" aria-live="polite"><span>' . esc_html( $ticker_text ) . '</span></div>';
    }
}
?>
