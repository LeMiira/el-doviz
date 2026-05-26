<?php
namespace ElDoviz\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Elementor widget to display exchange rates and BIST indexes.
 */
class ExchangeRatesWidget extends Widget_Base {
    public function get_name() {
        return 'ledoviz_turkish_exchange_rates_exchange_rates';
    }

    public function get_title() {
        return esc_html__( 'El Döviz Kurları ve Endeksler', 'ledoviz-turkish-exchange-rates' );
    }

    public function get_icon() {
        return 'eicon-bullet-list';
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
            'layout',
            [
                'label'   => esc_html__( 'Görünüm', 'ledoviz-turkish-exchange-rates' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'list',
                'options' => [
                    'list'   => esc_html__( 'Liste', 'ledoviz-turkish-exchange-rates' ),
                    'grid'   => esc_html__( 'Izgara', 'ledoviz-turkish-exchange-rates' ),
                ],
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
                'label'     => esc_html__( 'Varsayılan Metin Rengi', 'ledoviz-turkish-exchange-rates' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-el-container' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'accent_color',
            [
                'label'     => esc_html__( 'Etiket Rengi', 'ledoviz-turkish-exchange-rates' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#C41E3A',
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'label_typography',
                'label'    => esc_html__( 'Etiket Tipografisi', 'ledoviz-turkish-exchange-rates' ),
                'selector' => '{{WRAPPER}} .ledoviz-turkish-exchange-rates-label',
            ]
        );

        $this->add_control(
            'val_color',
            [
                'label'     => esc_html__( 'Değer Rengi', 'ledoviz-turkish-exchange-rates' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-val' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'val_typography',
                'label'    => esc_html__( 'Değer Tipografisi', 'ledoviz-turkish-exchange-rates' ),
                'selector' => '{{WRAPPER}} .ledoviz-turkish-exchange-rates-val',
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Arka Plan Rengi', 'ledoviz-turkish-exchange-rates' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-el-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                'label'    => esc_html__( 'Kapsayıcı Tipografisi', 'ledoviz-turkish-exchange-rates' ),
                'selector' => '{{WRAPPER}} .ledoviz-turkish-exchange-rates-el-container',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label'      => esc_html__( 'İç Boşluk (Padding)', 'ledoviz-turkish-exchange-rates' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-el-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'border',
                'selector' => '{{WRAPPER}} .ledoviz-turkish-exchange-rates-el-container',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label'      => esc_html__( 'Kenar Yumuşatma (Border Radius)', 'ledoviz-turkish-exchange-rates' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-el-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'box_shadow',
                'selector' => '{{WRAPPER}} .ledoviz-turkish-exchange-rates-el-container',
            ]
        );

        $this->add_control(
            'spacing_divider',
            [
                'label'     => esc_html__( 'Yerleşim ve Boşluklar', 'ledoviz-turkish-exchange-rates' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'row_spacing',
            [
                'label'      => esc_html__( 'Satır Boşluğu (Space between items)', 'ledoviz-turkish-exchange-rates' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-elementor-list .ledoviz-turkish-exchange-rates-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-elementor-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_alignment',
            [
                'label'   => esc_html__( 'Birim Hizalaması (Alignment)', 'ledoviz-turkish-exchange-rates' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'space-between' => [
                        'title' => esc_html__( 'Ayrık (Left / Right)', 'ledoviz-turkish-exchange-rates' ),
                        'icon'  => 'eicon-align-stretch',
                    ],
                    'flex-start'    => [
                        'title' => esc_html__( 'Bitişik (Compact)', 'ledoviz-turkish-exchange-rates' ),
                        'icon'  => 'eicon-align-start',
                    ],
                    'center'        => [
                        'title' => esc_html__( 'Merkez', 'ledoviz-turkish-exchange-rates' ),
                        'icon'  => 'eicon-align-center',
                    ],
                ],
                'default'   => 'space-between',
                'selectors' => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-item' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_gap',
            [
                'label'      => esc_html__( 'Sembol ve Değer Boşluğu (Gap)', 'ledoviz-turkish-exchange-rates' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ledoviz-turkish-exchange-rates-item' => 'gap: {{SIZE}}{{UNIT}};',
                ],
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

        $fetcher   = new \ElDoviz\Service\DataFetcher( new \ElDoviz\Service\CacheManager() );
        $rates     = $fetcher->fetch( 'tcmb', HOUR_IN_SECONDS );
        $bist_data = $fetcher->fetch( 'bist', HOUR_IN_SECONDS );

        $layout = $settings['layout'];
        $class  = 'list' === $layout ? 'ledoviz-turkish-exchange-rates-elementor-list' : 'ledoviz-turkish-exchange-rates-elementor-grid';

        echo '<div class="ledoviz-turkish-exchange-rates-el-container ' . esc_attr( $class ) . '">';
        foreach ( $selected as $code ) {
            $code_lc = strtolower( $code );
            if ( 'bist' === $code_lc ) {
                if ( ! is_wp_error( $bist_data ) && isset( $bist_data['bist100'] ) ) {
                    $val = number_format_i18n( $bist_data['bist100'], 2 );
                    echo '<div class="ledoviz-turkish-exchange-rates-item">';
                    echo '<span class="ledoviz-turkish-exchange-rates-label">' . esc_html__( 'BIST 100', 'ledoviz-turkish-exchange-rates' ) . '</span>';
                    echo '<span class="ledoviz-turkish-exchange-rates-val">' . esc_html( $val ) . '</span>';
                    echo '</div>';
                }
            } else {
                if ( ! is_wp_error( $rates ) && isset( $rates[ $code_lc ] ) ) {
                    $val = number_format_i18n( $rates[ $code_lc ], 4 );
                    echo '<div class="ledoviz-turkish-exchange-rates-item">';
                    echo '<span class="ledoviz-turkish-exchange-rates-label">' . esc_html( strtoupper( $code ) ) . '</span>';
                    echo '<span class="ledoviz-turkish-exchange-rates-val">' . esc_html( $val ) . '</span>';
                    echo '</div>';
                }
            }
        }
        echo '</div>';
    }
}
?>
