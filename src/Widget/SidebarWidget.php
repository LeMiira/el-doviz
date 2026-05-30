<?php
namespace ElDoviz\Widget;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Sidebar widget – displays selected exchange rates in standard layout.
 */
class SidebarWidget extends \WP_Widget {
    public function __construct() {
        parent::__construct(
            'ledoviz_turkish_exchange_rates_sidebar_widget',
            esc_html__( 'El Döviz Yan Menü Bileşeni', 'el-doviz' ),
            [ 'description' => esc_html__( 'Döviz kurlarını yan menüde gösterir.', 'el-doviz' ) ]
        );
    }

    public function widget( $args, $instance ) {
        echo wp_kses_post( $args['before_widget'] );
        if ( ! empty( $instance['title'] ) ) {
            echo wp_kses_post( $args['before_title'] ) . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . wp_kses_post( $args['after_title'] );
        }

        $currencies = isset( $instance['currencies'] ) ? $instance['currencies'] : 'usd,eur,gbp';

        // Reuse shortcode rendering callback or display simple list.
        if ( function_exists( 'ledoviz_turkish_exchange_rates_render_exchange_rates' ) ) {
            echo wp_kses_post( ledoviz_turkish_exchange_rates_render_exchange_rates( [
                'currencies' => $currencies,
                'layout'     => 'list',
                'theme'      => 'auto',
            ] ) );
        }

        echo wp_kses_post( $args['after_widget'] );
    }

    public function form( $instance ) {
        $title      = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Döviz Kurları', 'el-doviz' );
        $currencies = ! empty( $instance['currencies'] ) ? $instance['currencies'] : 'usd,eur,gbp';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Başlık:', 'el-doviz' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'currencies' ) ); ?>"><?php esc_html_e( 'Para Birimleri (virgülle ayrılmış):', 'el-doviz' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'currencies' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'currencies' ) ); ?>" type="text" value="<?php echo esc_attr( $currencies ); ?>" />
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title']      = sanitize_text_field( $new_instance['title'] );
        $instance['currencies'] = sanitize_text_field( $new_instance['currencies'] );
        return $instance;
    }
}
?>
