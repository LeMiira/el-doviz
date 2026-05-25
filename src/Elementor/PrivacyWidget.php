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
 * Elementor widget to display privacy disclosures and KVKK compliance text.
 */
class PrivacyWidget extends Widget_Base {
    public function get_name() {
        return 'el_doviz_privacy';
    }

    public function get_title() {
        return esc_html__( 'El Döviz Gizlilik ve KVKK', 'el-doviz' );
    }

    public function get_icon() {
        return 'eicon-lock';
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

        $default_text = esc_html__( 'Bu sitede gösterilen tüm döviz kurları ve finansal veriler, Türkiye Cumhuriyet Merkez Bankası (TCMB) tarafından sağlanan halka açık verilerden alınmakta olup yalnızca bilgilendirme amaçlıdır. Verilerin kesin doğruluğu veya anlık güncelliği garanti edilmez. Kişisel verileriniz KVKK kapsamında korunmakta olup, bu eklenti aracılığıyla hiçbir kişisel ziyaretçi verisi toplanmamakta veya işlenmemektedir.', 'el-doviz' );

        $this->add_control(
            'privacy_text',
            [
                'label'       => esc_html__( 'Açıklama Metni', 'el-doviz' ),
                'type'        => Controls_Manager::WYSIWYG,
                'default'     => $default_text,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'align',
            [
                'label'   => esc_html__( 'Hizalama', 'el-doviz' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'   => [
                        'title' => esc_html__( 'Sol', 'el-doviz' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Orta', 'el-doviz' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Sağ', 'el-doviz' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default'   => 'left',
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-privacy-container' => 'text-align: {{VALUE}};',
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
                'label'     => esc_html__( 'Metin Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-privacy-container' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__( 'Arka Plan Rengi', 'el-doviz' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-privacy-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'typography',
                'selector' => '{{WRAPPER}} .el-doviz-privacy-container',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label'      => esc_html__( 'İç Boşluk (Padding)', 'el-doviz' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-privacy-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'border',
                'selector' => '{{WRAPPER}} .el-doviz-privacy-container',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label'      => esc_html__( 'Kenar Yumuşatma (Border Radius)', 'el-doviz' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .el-doviz-privacy-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'box_shadow',
                'selector' => '{{WRAPPER}} .el-doviz-privacy-container',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $text = ! empty( $settings['privacy_text'] ) ? $settings['privacy_text'] : '';

        echo '<div class="el-doviz-privacy-container">';
        echo wp_kses_post( $text );
        echo '</div>';
    }
}
?>
