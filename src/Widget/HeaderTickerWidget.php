<?php
namespace ElDoviz\Widget;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Header ticker widget – displays selected rates in site header.
 */
class HeaderTickerWidget extends \WP_Widget {
    public function __construct() {
        parent::__construct(
            'el_doviz_header_ticker', // Base ID
            esc_html__( 'El Doviz Header Ticker', 'el-doviz' ), // Name
            [ 'description' => esc_html__( 'Shows a scrolling ticker of exchange rates in the header.', 'el-doviz' ) ]
        );
    }

    /**
     * Front‑end display of widget.
     */
    public function widget( $args, $instance ) {
        echo wp_kses_post( $args['before_widget'] );
        // Retrieve data via DataFetcher.
        $fetcher = new \ElDoviz\Service\DataFetcher( new \ElDoviz\Service\CacheManager() );
        $rates   = $fetcher->fetch( 'tcmb', HOUR_IN_SECONDS );
        if ( is_wp_error( $rates ) ) {
            echo '<p>' . esc_html__( 'Rates unavailable.', 'el-doviz' ) . '</p>';
            echo wp_kses_post( $args['after_widget'] );
            return;
        }
        $currencies = isset( $instance['currencies'] ) ? $instance['currencies'] : 'usd,eur,gbp';
        $currencies = array_map( 'trim', explode( ',', $currencies ) );
        echo '<ul class="el-doviz-header-ticker" aria-live="polite">';
        foreach ( $currencies as $code ) {
            $code_lc = strtolower( $code );
            if ( isset( $rates[ $code_lc ] ) ) {
                $rate = number_format_i18n( $rates[ $code_lc ], 4 );
                echo sprintf(
                    '<li><span class="currency-code">%s</span>: <span class="currency-rate">%s</span></li>',
                    esc_html( strtoupper( $code ) ),
                    esc_html( $rate )
                );
            }
        }
        echo '</ul>';
        echo wp_kses_post( $args['after_widget'] );
    }

    /**
     * Backend widget form.
     */
    public function form( $instance ) {
        $currencies = ! empty( $instance['currencies'] ) ? $instance['currencies'] : 'usd,eur,gbp';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'currencies' ) ); ?>"><?php esc_html_e( 'Currencies (comma separated)', 'el-doviz' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'currencies' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'currencies' ) ); ?>" type="text" value="<?php echo esc_attr( $currencies ); ?>" />
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['currencies'] = sanitize_text_field( $new_instance['currencies'] );
        return $instance;
    }
}
?>
