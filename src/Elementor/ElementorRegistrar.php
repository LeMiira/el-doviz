<?php
namespace ElDoviz\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers Elementor widgets for the plugin.
 */
class ElementorRegistrar {
    /**
     * Hook into Elementor widgets registration.
     */
    public static function register() {
        add_action( 'elementor/widgets/register', [ __CLASS__, 'register_widgets' ] );
    }

    /**
     * Register each widget class.
     */
    public static function register_widgets( $widgets_manager ) {
        // Ensure widget classes are autoloaded.
        require_once EL_DOVIZ_PATH . 'src/Elementor/ExchangeRatesWidget.php';
        require_once EL_DOVIZ_PATH . 'src/Elementor/TickerWidget.php';
        require_once EL_DOVIZ_PATH . 'src/Elementor/PrivacyWidget.php';

        $widgets_manager->register( new ExchangeRatesWidget() );
        $widgets_manager->register( new TickerWidget() );
        $widgets_manager->register( new PrivacyWidget() );
    }
}
?>
