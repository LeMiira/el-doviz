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
        return 'el_doviz_exchange_rates';
    }

    public function get_title() {
        return esc_html__( 'El Döviz Kurları ve Endeksler', 'el-doviz' );
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
            'layout',
            [
                'label'   => esc_html__( 'Görünüm', 'el-doviz' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'list',
                'options' => [
                    'list'   => esc_html__( 'Liste', 'el-doviz' ),
                    'grid'   => esc_html__( 'Izgara', 'el-doviz' ),
                ],
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
                'label'     => esc_html__( 'Varsayılan Metin Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-el-container' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'accent_color',
            [
                'label'     => esc_html__( 'Etiket Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#C41E3A',
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'label_typography',
                'label'    => esc_html__( 'Etiket Tipografisi', 'el-doviz' ),
                'selector' => '{{WRAPPER}} .el-doviz-label',
            ]
        );

        $this->add_control(
            'val_color',
            [
                'label'     => esc_html__( 'Değer Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-val' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'val_typography',
                'label'    => esc_html__( 'Değer Tipografisi', 'el-doviz' ),
                'selector' => '{{WRAPPER}} .el-doviz-val',
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Arka Plan Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-el-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                'label'    => esc_html__( 'Kapsayıcı Tipografisi', 'el-doviz' ),
                'selector' => '{{WRAPPER}} .el-doviz-el-container',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label'      => esc_html__( 'İç Boşluk (Padding)', 'el-doviz' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-el-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'border',
                'selector' => '{{WRAPPER}} .el-doviz-el-container',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label'      => esc_html__( 'Kenar Yumuşatma (Border Radius)', 'el-doviz' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-el-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'box_shadow',
                'selector' => '{{WRAPPER}} .el-doviz-el-container',
            ]
        );

        $this->add_control(
            'spacing_divider',
            [
                'label'     => esc_html__( 'Yerleşim ve Boşluklar', 'el-doviz' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'row_spacing',
            [
                'label'      => esc_html__( 'Satır Boşluğu (Space between items)', 'el-doviz' ),
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
                    '{{WRAPPER}} .el-doviz-elementor-list .el-doviz-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .el-doviz-elementor-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_alignment',
            [
                'label'   => esc_html__( 'Birim Hizalaması (Alignment)', 'el-doviz' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'space-between' => [
                        'title' => esc_html__( 'Ayrık (Left / Right)', 'el-doviz' ),
                        'icon'  => 'eicon-align-stretch',
                    ],
                    'flex-start'    => [
                        'title' => esc_html__( 'Bitişik (Compact)', 'el-doviz' ),
                        'icon'  => 'eicon-align-start',
                    ],
                    'center'        => [
                        'title' => esc_html__( 'Merkez', 'el-doviz' ),
                        'icon'  => 'eicon-align-center',
                    ],
                ],
                'default'   => 'space-between',
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-item' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'item_gap',
            [
                'label'      => esc_html__( 'Sembol ve Değer Boşluğu (Gap)', 'el-doviz' ),
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
                    '{{WRAPPER}} .el-doviz-item' => 'gap: {{SIZE}}{{UNIT}};',
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
        $class  = 'list' === $layout ? 'el-doviz-elementor-list' : 'el-doviz-elementor-grid';

        echo '<div class="el-doviz-el-container ' . esc_attr( $class ) . '">';
        foreach ( $selected as $code ) {
            $code_lc = strtolower( $code );
            if ( 'bist' === $code_lc ) {
                if ( ! is_wp_error( $bist_data ) && isset( $bist_data['bist100'] ) ) {
                    $val = number_format_i18n( $bist_data['bist100'], 2 );
                    echo '<div class="el-doviz-item">';
                    echo '<span class="el-doviz-label">' . esc_html__( 'BIST 100', 'el-doviz' ) . '</span>';
                    echo '<span class="el-doviz-val">' . esc_html( $val ) . '</span>';
                    echo '</div>';
                }
            } else {
                if ( ! is_wp_error( $rates ) && isset( $rates[ $code_lc ] ) ) {
                    $val = number_format_i18n( $rates[ $code_lc ], 4 );
                    echo '<div class="el-doviz-item">';
                    echo '<span class="el-doviz-label">' . esc_html( strtoupper( $code ) ) . '</span>';
                    echo '<span class="el-doviz-val">' . esc_html( $val ) . '</span>';
                    echo '</div>';
                }
            }
        }
        echo '</div>';
    }
}
?>
